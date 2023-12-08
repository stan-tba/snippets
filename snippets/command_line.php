<?php
# common functions for command line scripts
function require_root() {
    if (posix_getuid() != 0) die("This script must be run as root");
}
function cli_or_die() {
    (php_sapi_name() === 'cli') || die();
}
function require_command($cmd) {
    if (!`which $cmd`) die("This script requires $cmd");
}
function check_cwd() {
    // could be adapted but this works for now
    if (getcwd() != ($mydir = dirname(__FILE__))) {
        die("Run this script from where it's located.\n   $mydir");
    }
    // cd or pushd dirname(__FILE__) is better than this
}
