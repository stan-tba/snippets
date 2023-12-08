<?
// HTTP -----------------------------------------------------------------------------
function send_json($data) {
    $bad_ie = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false);
    header('Content-Type: '.($bad_ie ? 'text/plain':'application/json').'; charset=utf-8');
    $output = json_encode($data);
    //header('Content-Length: '.strlen($output));
    nocache_headers();
    echo $output;
}
function not_found() {
    header("HTTP/1.0 404 Not Found");
    exit;
}

function nocache_headers() {
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Last-Modified: ".gmdate ("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}
function download_file($file_data, $filename) {
    header ("Content-Disposition: attachment; filename=$filename\r\n");
    header ("Content-Type: application/octet-stream\r\n");
    header ("Content-Type: application/force-download\r\n");
    header ("Content-Type: application/download\r\n");
    header ("Content-Length: ".strlen($file_data)."\r\n");
    header ("Content-Transfer-Encoding: binary\r\n");
    print $file_data;
}
