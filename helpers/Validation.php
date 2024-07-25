<?php

class Validation {
    
    public $patterns = array(
        'uri'           => '[A-Za-z0-9-\/_?&=]+',
        'url'           => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha'         => '[\p{L}]+',
        'words'         => '[\p{L}\s]+',
        'alphanum'      => '[\p{L}0-9]+',
        'int'           => '[0-9]+',
        'float'         => '[0-9\.,]+',
        'tel'           => '^09[0-9]{9}$',
        'text'          => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
        'file'          => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder'        => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address'       => '[\p{L}0-9\s.,()°-]+',
        'date_dmy'      => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd'      => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email'         => '^[a-zA-Z0-9._%+-]+@gmail\.com$',
        'postal_code'   => '[0-9]{5}(-[0-9]{4})?',
        'credit_card'   => '^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9]{2})[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$',
        'slug'          => '^[a-z0-9-]+$',
        'ipv4'          => '\b(?:\d{1,3}\.){3}\d{1,3}\b',
        'ipv6'          => '\b(?:[a-fA-F0-9]{1,4}:){7}[a-fA-F0-9]{1,4}\b',
    );
    
    public $errors = array();
    
    public function name($name){
        $this->name = $name;
        return $this;
    }
    
    public function value($value){
        $this->value = $value;
        return $this;
    }
    
    public function file($value){
        $this->file = $value;
        return $this;
    }
    
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
    
    public function customPattern($pattern){
        $regex = '/^('.$pattern.')$/u';
        if($this->value != '' && !preg_match($regex, $this->value)){
            $this->errors[] = 'فرمت فیلد '.$this->name.' نامعتبر است.';
        }
        return $this;
    }
    
    public function required(){
        if((isset($this->file) && $this->file['error'] == 4) || ($this->value == '' || $this->value == null)){
            $this->errors[] = 'فیلد '.$this->name.' اجباری است.';
        }            
        return $this;
    }
    
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
    
    public function equal($value){
        if($this->value != $value){
            $this->errors[] = 'مقدار فیلد '.$this->name.' مطابقت ندارد.';
        }
        return $this;
    }
    
    public function maxSize($size){
        if($this->file['error'] != 4 && $this->file['size'] > $size){
            $this->errors[] = 'حجم فایل '.$this->name.' از حداکثر مجاز بیشتر است: '.number_format($size / 1048576, 2).' MB.';
        }
        return $this;
    }
    
    public function ext($extension){
        if($this->file['error'] != 4 && pathinfo($this->file['name'], PATHINFO_EXTENSION) != $extension && strtoupper(pathinfo($this->file['name'], PATHINFO_EXTENSION)) != $extension){
            $this->errors[] = 'فرمت فایل '.$this->name.' باید '.$extension.' باشد.';
        }
        return $this;
    }
    
    public function purify($string){
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public function isSuccess(){
        if(empty($this->errors)) return true;
    }
    
    public function getErrors(){
        if(!$this->isSuccess()) return $this->errors;
    }
    
    public function displayErrors(){
        $html = '<ul>';
        foreach($this->getErrors() as $error){
            $html .= '<li>'.$error.'</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function displayError(){
        if (!empty($this->errors)) {
            $error = $this->errors[0];
            return $error;
        }
    }
    
    
    public function result(){
        if(!$this->isSuccess()){
            foreach($this->getErrors() as $error){
                echo "$error\n";
            }
            exit;
        }else{
            return true;
        }
    }

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
    
    public static function is_int($value){
        if(filter_var($value, FILTER_VALIDATE_INT)) return true;
    }
    
    public static function is_float($value){
        if(filter_var($value, FILTER_VALIDATE_FLOAT)) return true;
    }
    
    public static function is_alpha($value){
        if(filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/")))) return true;
    }
    
    public static function is_alphanum($value){
        if(filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) return true;
    }
    
    public static function is_url($value){
        if(filter_var($value, FILTER_VALIDATE_URL)) return true;
    }
    
    public static function is_uri($value){
        if(filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) return true;
    }
    
    public static function is_bool($value){
        if(is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) return true;
    }
    
    public static function is_email($value){
        if(filter_var($value, FILTER_VALIDATE_EMAIL)) return true;
    }
    
}
?>