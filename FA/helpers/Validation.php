<?php
class Validation {

    // Define a list of common patterns for validation
    public $patterns = array(
        'uri'           => '[A-Za-z0-9-\/_?&=]+',
        'url'           => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha'         => '[\p{L}]+',
        'words'         => '[\p{L}\s]+',
        'alphanum'      => '[\p{L}0-9]+',
        'int'           => '[0-9]+',
        'float'         => '[0-9\.,]+',
        'tel'           => '^09[0-9]{9}$', // Iranian phone number format
        'text'          => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
        'file'          => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder'        => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address'       => '[\p{L}0-9\s.,()°-]+',
        'date_dmy'      => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd'      => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email'         => '^[a-zA-Z0-9._%+-]+@gmail\.com$', // Only allows Gmail addresses
        'postal_code'   => '[0-9]{5}(-[0-9]{4})?',
        'credit_card'   => '^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9]{2})[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$',
        'slug'          => '^[a-z0-9-]+$',
        'ipv4'          => '\b(?:\d{1,3}\.){3}\d{1,3}\b',
        'ipv6'          => '\b(?:[a-fA-F0-9]{1,4}:){7}[a-fA-F0-9]{1,4}\b',
    );

    // Store validation errors
    public $errors = array();

    // Set the name of the field being validated
    public function name($name){
        $this->name = $name;
        return $this;
    }

    // Set the value of the field being validated
    public function value($value){
        $this->value = $value;
        return $this;
    }

    // Set the file for file validation
    public function file($value){
        $this->file = $value;
        return $this;
    }

    // Apply a predefined pattern to the value
    public function pattern($name){
        if($name == 'array'){
            if(!is_array($this->value)){
                $this->errors[] = 'فرمت فیلد '.$this->name.' نامعتبر است.';
            }
        }else{
            $regex = '/^('.$this->patterns[$name].')$/u';
            if($this->value != '' && !preg_match($regex, $this->value)){
                $this->errors[] = 'فرمت فیلد '.$this->name.' نامعتبر است.';
            }
        }
        return $this;
    }

    // Apply a custom pattern to the value
    public function customPattern($pattern){
        $regex = '/^('.$pattern.')$/u';
        if($this->value != '' && !preg_match($regex, $this->value)){
            $this->errors[] = 'فرمت فیلد '.$this->name.' نامعتبر است.';
        }
        return $this;
    }

    // Check if the value is required and not empty
    public function required(){
        if((isset($this->file) && $this->file['error'] == 4) || ($this->value == '' || $this->value == null)){
            $this->errors[] = 'فیلد '.$this->name.' اجباری است.';
        }
        return $this;
    }

    // Check if the value has a minimum length
    public function min($length){
        if(is_string($this->value)){
            if(strlen($this->value) < $length){
                $this->errors[] = 'مقدار فیلد '.$this->name.' کمتر از '.$length.' است.';
            }
        }else{
            if($this->value < $length){
                $this->errors[] = 'مقدار فیلد '.$this->name.' کمتر از '.$length.' است.';
            }
        }
        return $this;
    }

    // Check if the value does not exceed a maximum length
    public function max($length){
        if(is_string($this->value)){
            if(strlen($this->value) > $length){
                $this->errors[] = 'مقدار فیلد '.$this->name.' بیشتر از '.$length.' است.';
            }
        }else{
            if($this->value > $length){
                $this->errors[] = 'مقدار فیلد '.$this->name.' بیشتر از '.$length.' است.';
            }
        }
        return $this;
    }

    // Check if the value is equal to another value
    public function equal($value){
        if($this->value != $value){
            $this->errors[] = 'مقدار فیلد '.$this->name.' مطابقت ندارد.';
        }
        return $this;
    }

    // Check if the file size does not exceed a maximum size
    public function maxSize($size){
        if($this->file['error'] != 4 && $this->file['size'] > $size){
            $this->errors[] = 'حجم فایل '.$this->name.' از حداکثر مجاز بیشتر است: '.number_format($size / 1048576, 2).' MB.';
        }
        return $this;
    }

    // Check if the file has the correct extension
    public function ext($extension){
        if($this->file['error'] != 4 && pathinfo($this->file['name'], PATHINFO_EXTENSION) != $extension && strtoupper(pathinfo($this->file['name'], PATHINFO_EXTENSION)) != $extension){
            $this->errors[] = 'فرمت فایل '.$this->name.' باید '.$extension.' باشد.';
        }
        return $this;
    }

    // Sanitize a string
    public function purify($string){
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // Check if there are no validation errors
    public function isSuccess(){
        return empty($this->errors);
    }

    // Get the list of validation errors
    public function getErrors(){
        return $this->isSuccess() ? [] : $this->errors;
    }

    // Display all validation errors as an HTML list
    public function displayErrors(){
        $html = '<ul>';
        foreach($this->getErrors() as $error){
            $html .= '<li>'.$error.'</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    // Display the first validation error
    public function displayError(){
        return !empty($this->errors) ? $this->errors[0] : '';
    }

    // Print validation errors and stop script execution if there are any
    public function result(){
        if(!$this->isSuccess()){
            foreach($this->getErrors() as $error){
                echo "$error\n";
            }
            exit;
        }
        return true;
    }

    // Check if a value is unique in the specified table and column
    public function uniq($table, $cul, $value){
        global $db;

        if (!isset($db)) {
            include('../../config/loader.php');
        }

        $stmt = $db->prepare("SELECT * FROM $table WHERE $cul = ? ORDER BY id DESC");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $this->errors[] = 'مقدار فیلد '.$this->name.' از قبل وجود دارد';
        } else {
            return true;
        }
    }

    // Check if a value exists in the specified table and column
    public function isExist($table, $cul, $value){
        global $db;

        if (!isset($db)) {
            include('../../config/loader.php');
        }

        $stmt = $db->prepare("SELECT * FROM $table WHERE $cul = ? ORDER BY id DESC");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            return true;
        } else {
            $this->errors[] = 'مقدار فیلد '.$this->name.'  وجود ندارد';
        }
    }

    // Static methods for common validation checks
    public static function is_int($value){
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function is_float($value){
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function is_alpha($value){
        return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/"))) !== false;
    }

    public static function is_alphanum($value){
        return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/"))) !== false;
    }

    public static function is_url($value){
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function is_uri($value){
        return filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/"))) !== false;
    }

    public static function is_bool($value){
        return is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
    }

    public static function is_email($value){
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

}

?>