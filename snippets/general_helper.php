<?
// RECURSIVE ---------------------------------------------------------------
function htmlentities_r($a) {
    if (!is_array($a)) return htmlentities($a);
    foreach ($a as $k => $v) {
        $a[$k] = htmlentities_r($a[$k]);
    }
    return $a;
}

function htmlspecialchars_r($a) {
    if (!is_array($a)) return htmlspecialchars($a);
    foreach ($a as $k => $v) {
        $a[$k] = htmlspecialchars_r($a[$k]);
    }
    return $a;
}




// DEBUG -------------------------------------------------------------------
function debugData($data, $die=false, $dump=false){
    echo "<pre style=\"font-size:10px; background-color: #ff0; color: #000;\">";
    $dump?var_dump($data):print_r($data);
    echo "</pre>";
    if ($die){
        die();
    }
}

$__time_mark=microtime(true);

// debugging information about time and memory usage
function timeMark($label="",$comment=null){
    global $__time_mark;
    $new_mark=microtime(true);
    $mem=memory_get_usage(true)/1024000;
    debugData($label." ".($new_mark-$__time_mark)."ms Mem:{$mem}M");
    if ($comment){
        echo "<!-- ";
        print_r($comment);
        echo "-->";    
    }
    $__time_mark=$new_mark;
}

// CLOUDFRONT ----------------------------------------------
function get_signed_url($resource, $timeout) {
    $pkfile = "application/config/cloudfront/pk-APKAIJEMIOTYE452EBCQ.pem";
    $keyPairId = "APKAIJEMIOTYE452EBCQ";

    $expires = time() + $timeout; //Time out in seconds
    $json = '{"Statement":[{"Resource":"'.$resource.'","Condition":{"DateLessThan":{"AWS:EpochTime":'.$expires.'}}}]}';

    //Read Cloudfront Private Key
    $priv_key = file_get_contents($pkfile);

    //Create the private key
    $key = openssl_get_privatekey($priv_key, null);
    if(!$key) {
        echo $errmsg = "<p>Failed to load private key!</p>";
        error_log($errmsg);
        return;
    }

    //Sign the policy with the private key
    if(!openssl_sign($json, $signed_policy, $key, OPENSSL_ALGO_SHA1)) {
        echo $errmsg = '<p>Failed to sign policy: '.openssl_error_string().'</p>';
        error_log($errmsg);
        return;
    }

    $base64_signed_policy = base64_encode($signed_policy);
    $signature = str_replace(array('+','=','/'), array('-','_','~'), $base64_signed_policy);

    $url = $resource.'?Expires='.$expires.'&Signature='.$signature.'&Key-Pair-Id='.$keyPairId;

    return $url;
} // get_signed_url()


?>
