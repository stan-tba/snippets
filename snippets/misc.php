<?php
// MISC --------------------------------------------------------------
function session_get_consume($a) { // alias for get_flash()
    return get_flash($a);
}

function get_flash($name) { // requires session
    $val = $_SESSION[$name];
    unset($_SESSION[$name]);
    return $val;
}
function human_microtime() {
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    return date('Ymd-His-'.$micro, $t);
    // no need to use DateTime but just for reference:
    //$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
    //$d->format("Ymd-His-u");
}

function get_file_mimetype($filename) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if (!is_resource($finfo)) return false;
    return finfo_file($finfo, $filename);
}

function forbidden() {
    // http://stackoverflow.com/questions/3297048/403-forbidden-vs-401-unauthorized-http-responses
    header("HTTP/1.0 403 Forbidden");
    exit;
}

// https://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
function crypto_rand_secure($min, $max){
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}
function getToken($length) {
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
    }

    return $token;
}
// this is more elegant and efficient than above but has two uglier characters
function my_token() { // get-string safe base 64
    // no '=' because multiple of 3 - exact byte boundary
    return strtr(base64_encode(openssl_random_pseudo_bytes(24)),array('+'=>'-','/'=>'.'));
}
function is_ssl() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
}
function escape_html($data) {
    if (is_array($data)) return array_map('escape_html', $data);
    return htmlspecialchars($data);
}

// browser caching helper - thanks to Francisco
function asset_url($url){
    $realpath=realpath(".$url");
    if ($realpath && file_exists($realpath)){
        $time=filemtime($realpath);
        $url.="?".$time;
    }
    return $url;
}

// https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
function format_bytes($bytes, $precision = 2) { 
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB']; 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

