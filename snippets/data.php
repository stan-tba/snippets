<?

// turns recursive array into one dimensional array with key names in this format "[$val1][$val2]"
// useful for turning input arrays back into their literal form names
function flatten_array($a) {
    $ret = array();
    foreach($a as $k => $v) {
        if (is_array($a[$k])) {
            foreach(flatten_array($a[$k]) as $k2 => $v2) {
                $ret["[$k]$k2"] = $v2;
            } 
        } else {
            $ret["[$k]"] = $v;
        }
    }
    return $ret;
}

function flatten_request_array($name) {
  if (!is_array($_REQUEST)) return false;
  if (!is_array($_REQUEST[$name])) return false;

  foreach(flatten_array($_REQUEST[$name]) as $k => $val) {
    $_REQUEST["$name$k"] = $val;
  }
  return true;
}

function index_by($column, $rows) {
    if (!is_array($rows)) return array();
    $ret = array();
    foreach ($rows as $row) {
        $ret[$row[$column]] = $row;
    }
    return $ret;
}

function simple_csv_implode($v) {
    if (!is_array($v)) return ''; // save a line or two
    return implode(',',$v);
}

function assoc_to_array_for_js($kv, $keyname='k', $valname='v') {
    // to preserve ordering in js eg {'3':'three','2':'two','1':'one'} ==> [{k:3,v:'three'}, {k:2,v:'two'}, {k:1,v:'one'}]
    $a = array();
    foreach ($kv as $k=>$v) $a[] = array($keyname=>$k, $valname=>$v);
    return $a;
}

function array_splice_assoc(&$input, $offset, $length, $replacement) {
    // found here http://php.net/manual/en/function.array-splice.php
    $replacement = (array) $replacement;
    $key_indices = array_flip(array_keys($input));
    if (isset($input[$offset]) && is_string($offset)) {
        $offset = $key_indices[$offset];
    }
    if (isset($input[$length]) && is_string($length)) {
        $length = $key_indices[$length] - $offset;
    }

    $input = array_slice($input, 0, $offset, TRUE)
        + $replacement
        + array_slice($input, $offset + $length, NULL, TRUE);
}


?>
