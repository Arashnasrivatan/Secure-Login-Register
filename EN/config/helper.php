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


function logFailedAttempt($ipAddress)
{
    global $db;

    // Check if IP address exists in the database
    $stmt = $db->prepare('SELECT id FROM ip_tracking WHERE ip_address = ?');
    $stmt->bind_param('s', $ipAddress); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // IP address exists; update failed attempts and last attempt timestamp
        $stmt = $db->prepare('UPDATE ip_tracking SET failed_attempts = failed_attempts + 1, last_attempt = NOW() WHERE ip_address = ?');
        $stmt->bind_param('s', $ipAddress);
        $stmt->execute();
    } else {
        // IP address does not exist; insert new record
        $stmt = $db->prepare('INSERT INTO ip_tracking (ip_address, failed_attempts, last_attempt) VALUES (?, 1, NOW())');
        $stmt->bind_param('s', $ipAddress);
        $stmt->execute();
    }
}


function isBlocked($ipAddress)
{
    global $db;
    // Define thresholds
    $maxAttempts = 5;
    $blockTime = 15 * 60; // 15 minutes

    // Get IP tracking record
    $stmt = $db->prepare('SELECT failed_attempts, last_attempt FROM ip_tracking WHERE ip_address = ?');
    $stmt->bind_param('s', $ipAddress); // 's' specifies the variable type => 'string'
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row) {
            $failedAttempts = $row['failed_attempts'];
            $lastAttempt = strtotime($row['last_attempt']);
            $currentTime = time();

            // Check if the IP should be blocked
            if ($failedAttempts >= $maxAttempts && ($currentTime - $lastAttempt) < $blockTime) {
                return true;
            }
        }
    }

    return false;
}



?>