<?
// DB DATA FORMATTING ---------------------------
// DB QUERY
function any_column_like($columns, $escaped_likestr) {
    if (!$columns) return "1=1";
    $likes = array();
    foreach ($columns as $col) {
        $likes[] = "$col like $escaped_likestr";
    }
    return "(" . implode(" OR ", $likes) . ")";
}



// use equals to make everything display properly in Excel
// callback to get argument by reference
// utf8 option
function array_to_csv($rows, $callback='', $equals=false, $opts=array()) {
    if (!is_array($rows) || !$rows)
        return "";
    
    $firstline = true;
    //while ($row = $result->unbuffered_row('array')) {
    foreach ($rows as $row) {
        // heading row
        if ($firstline) {
             $csv = 
                ((isset($opts['utf8']) ? hex2bin("EFBBBF") : '')) // utf-8 BOM
                . '"' . implode('","', array_keys($row)) . "\"\r\n";
             $firstline = false;
        }

        // clean and escape data
        foreach($row as $k => $v) {
            if (isset($opts['utf8'])) {
                $v = str_replace ('"', '""', $v); // escape only
            } else {
                $v = str_replace ('"', '""', str_replace ("\r\n", " ", $v));
                $v = str_replace ("\r", " ", str_replace ("\n", " ", $v));
                $v = str_replace ("×", "x", $v); // replace unicode multiply because excel doesn't like it
            }
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


// same as above but for array of rows (passed by reference to save memory))
function array_to_table(&$rows, $attribs = "", $callback = "", $headings = array(), $opts = array()) {
    if(!is_array($rows) || !$rows) return "";
    if ($attribs!="") $attribs = " $attribs";
    $table = "<table$attribs>\n";

    // create headings row
    if (!isset($opts['no_headings'])) {
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
            foreach (array_keys($rows[0]) as $field_name) {
                $table .= "<th>". htmlspecialchars($field_name) ."</th>";
            }
            $table .= "</tr></thead>\n";
        }
    }

    $table .= "<tbody>\n";
    reset($rows);
    // fill in the data rows
    foreach ($rows as $row) {
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
    reset($rows);
    $table .= "</tbody>\n";
    $table .= "</table>\n";
    return $table;
}


function sum_column($rows, $column) {
    $sum = 0;
    foreach ($rows as $row) $sum += $row[$column];
    return $sum;
}


function sort_rows_by($column, &$rows, $reverse=0) {
    usort($rows, function($a,$b) use ($column, $reverse){
        if ($a[$column] == $b[$column]) return 0;
        if ($reverse) return ($a[$column] > $b[$column]) ? -1 : 1;
        return ($a[$column] < $b[$column]) ? -1 : 1;
    });
}
function column_sum($column, $rows) {
    $sum = 0;
    foreach ($rows as $row) $sum += $row[$column];
    return $sum;
}


?>
