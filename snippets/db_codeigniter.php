<?
// DB DATA FORMATTING ---------------------------
// Codeigniter
function result_to_assoc($result) { // return associative array out of first two columns
    $ret = array();
    foreach ($result->result_array() as $row) {
        $a = array_values($row);
        $ret[$a[0]] = $a[1];
    }
    return $ret;
}

// Codeigniter result to array
function result_to_array($result) { // return first column
    $ret = array();
    foreach ($result->result_array() as $row) {
        $a = array_values($row);
        $ret[] = $a[0];
    }
    return $ret;
}

// Codeigniter db result to comma separated value file format
// use equals to make everything display properly in Excel
// callback to get argument by reference
// stan's old function but for codeigniter
function result_to_csv($result, $callback='', $equals=false, $opts=array()) {
    if (!$result->num_rows())
        return "";
    
    $firstline = true;
    while ($row = $result->unbuffered_row('array')) {
        // heading row
        if ($firstline) {
             $csv = '"' . implode('","', array_keys($row)) . "\"\r\n";
             $firstline = false;
        }

        // clean and escape data
        foreach($row as $k => $v) {
            $v = str_replace ('"', '""', str_replace ("\r\n", " ", $v));
            $v = str_replace ("\r", " ", str_replace ("\n", " ", $v));
            $row[$k] = $v;
        }

        // callback function
        if($callback!="") $callback($row);

        // double quote and equals before imploding
        foreach($row as $k => $v) {
            $v = "\"$v\"";
            if ($equals && (strpos($v, ',') === false)) {
                // excel does not respect commas inside "" if using equals. don't use it if there are commas.
                $v = "=$v";
            }
            $row[$k] = $v;
        }
        if ($opts && is_array($opts['equals_columns'])) {
            // macos numbers shows ="" as =" - avoid
            foreach ($opts['equals_columns'] as $k) if ($row[$k]!='""') $row[$k] = "={$row[$k]}";
        }

        // data row
        $csv .= implode(',', $row) . "\r\n";
    }

    return $csv;
}


// CODEIGNITER
// data not escaped in case you want html in there.  use callback to escape.
function result_to_table($result, $attribs = "", $callback = "", $headings = array(), $opts = array()) { // codeigniter version
    if($result->num_rows() == 0) return "";
    if ($attribs!="") $attribs = " $attribs";
    $table = "<table$attribs>\n";

    // create headings row
    if($headings) { 
        if (is_array($opts['th_attribs'])) { // th_attribs only applies if headings are provided right now
            $th_attribs = $opts['th_attribs']; // th_attribs and headings must be numerically indexed from 0
            $table .= "<thead><tr>";
            $num_headings = count($headings);
            for($i=0; $i<$num_headings; $i++) {
                $attr = (($th_attribs[$i] == '') ? '' : " {$th_attribs[$i]}" );
                $table .= "<th$attr>{$headings[$i]}</th>";
            }
            $table .= "</tr></thead>\n";
        }
        else {
            $table .= "<thead><tr><th>". implode("</th><th>", $headings) ."</th></tr></thead>\n";
        }
    }
    else {
        $table .= "<thead><tr>";
        foreach ($result->list_fields() as $field_name) {
            $table .= "<th>". htmlspecialchars($field_name) ."</th>";
        }
        $table .= "</tr></thead>\n";
    }

    $table .= "<tbody>\n";
    $result->data_seek(0);
    // fill in the data rows
    while ($row = $result->unbuffered_row('array')) {
        if($callback!="") $row_attribs = $callback($row); // argument must be passed by reference
        if (is_array($row_attribs)) { // specify row and cell attribs
            if ($row_attribs['row_attribs'] != '') 
            $tr_attr = (($row_attribs['row_attribs'] != '') ? " {$row_attribs['row_attribs']}" : '');
            $table .= "<tr>";
            foreach ($row as $k => $v) {
                $td_attr = (($row_attribs['cell_attribs'][$k] != '') ? " {$row_attribs['cell_attribs'][$k]}" : '');
                $table .= "<td$td_attr>". $v ."</td>";
            }
            $table .= "</tr>";
        }
        else { // string gives just row attributes
            if ($row_attribs != '') $row_attribs = " $row_attribs"; // maybe allow this to be array if needed
            $table .= "<tr$row_attribs><td>". implode("</td><td>", $row) ."</td></tr>\n";
        }
    }
    $result->data_seek(0);
    $table .= "</tbody>\n";
    $table .= "</table>\n";
    return $table;
}



?>
