<?php
include 'db.php';

function redirect($Url)
{
if (!headers_sent()){
header("Location: $Url");
}else{
echo "<script type='text/javascript'>window.location.href='$Url'</script>";
echo "<noscript><meta http-equiv='refresh' content='0;url=$Url'/></noscript>";
}
exit;
}

function getFullUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
    $host = $_SERVER['HTTP_HOST'];
    
    $fullUrl = $protocol . '://' . $host;
    
    return $fullUrl;
}


?>