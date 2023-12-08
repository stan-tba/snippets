<?php
function psquery($query, ...$vars) {
    // prepared statement only for the purpose of escaping
    global $db, $DB_DEBUG;
    if ($DB_DEBUG) {
        echo "$query\n";
        print_r($vars);
    }
    ($stmt = $db->prepare($query)) || die($db->error);
    // mysql works the same when giving int value as a string
    $types = str_repeat("s", $count = count($vars));
    if($count) $stmt->bind_param($types, ...$vars) || die("bind error: " . $db->error);
    ($stmt->execute()) || die("execute error: " . $db->error);
    return $stmt;
}
function my_query($query) {
    return psquery($query)->get_result()->fetch_all(MYSQLI_ASSOC);
}
function db_get_row_where($table, $where=[]) {
    // php7.4 $wheres = array_map(fn($a) => "$a=?", array_keys($where));
    $wheres = array_map(function($a){ return "$a=?";}, array_keys($where));
    $query = "SELECT * from $table WHERE " . implode(" AND ", $wheres);
    return psquery($query, ...array_values($where))->get_result()->fetch_assoc();
}
function db_insert($table, $data=[], $no_escape=[]) {
    $columns = array_merge(array_keys($data), array_keys($no_escape));
    // php7.4 $values = array_merge(array_map(fn()=>'?', $data), array_values($no_escape));
    $values = array_merge(array_map(function(){return '?';}, $data), array_values($no_escape));
    $query = "INSERT INTO $table
        (". implode(',', $columns) .") VALUES
        (". implode(',', $values) .")";
    return psquery($query, ...array_values($data));
}
function get_or_insert($table, ...$args) {
    global $rows, $opts;
    if (isset($opts['debug'])) echo "get_or_insert($table)\n";
    if (function_exists($fn = "{$table}_iddata")) $args[] = $fn();
    if ($row = ($getfn="get_$table")(...$args)) {
        return $rows[$table] = $row;
    }
    ("insert_$table")(...$args);
    return $rows[$table] = $getfn(...$args);
}
function result_to_array($result) { // return first column
    $ret = [];
    foreach ($result->fetch_all() as $row) {
        $a = array_values($row);
        $ret[] = $a[0];
    }
    return $ret;
}
// todo to_assoc()
?>
