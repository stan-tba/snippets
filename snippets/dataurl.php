#!/bin/bash
<?php
if ($argc != 2) error_die("usage: {$argv[0]} filename");
if (!is_file($f = $argv[1])) error_die("file not found $f");
$mime = ($m=mime_content_type($argv[1])) ? $m : 'application/octet-stream';
echo "data:$mime;base64," . base64_encode(file_get_contents($argv[1]));
function error_die($message) {
    fwrite(STDERR, $message);
    exit(1);
}
