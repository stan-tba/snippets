<?
// not checking if exist. want to be notified by an error if there is a name collision.
// FORM ----------------
// requires E_NOTICE turned off
function make_input($name, $attr) {
    if (!$attr['type']) $attr['type'] = 'text';
    $attr_str = '';
    foreach ($attr as $k => $v) $attr_str .= " $k=\"$v\""; // space at start
    $escaped_value = htmlentities($_REQUEST[$name]);
    if (strpos($name, 'evenue')) $escaped_value = number_format((double)$escaped_value, 2);
    return "<input name=\"$name\" value=\"$escaped_value\"$attr_str />";
}

function make_textarea($name, $attr) {
    $attr_str = '';
    foreach ($attr as $k => $v) $attr_str .= " $k=\"$v\""; // space at start
    $escaped_value = htmlentities($_REQUEST[$name]);
    return "<textarea name=\"$name\"$attr_str>$escaped_value</textarea>";
}

// Make a dynamic hidden field or array of dynamic hidden fields
function make_hidden($field_name, $attribs='') { // attribs only apply to single value not array
    ob_start();
    if (is_array($field_name)) {
        foreach($field_name as $field => $value) {
            make_hidden($field);
        }
    }
    else {
        if ($attribs) $attribs=" $attribs";
        print "<input type=\"hidden\" name=\"".htmlentities($field_name)."\" value=\"".htmlentities($_REQUEST[$field_name])."\"$attribs />\n";
    }
    return ob_get_clean();
}

 // make a dynamic radio button control
function make_radio($field_name, $value, $display_name = "", $xtrahtml = "") {
    if ($xtrahtml) $xtrahtml = " $xtrahtml"; // could be made to take either string or assoc array
    return "<input ".($_REQUEST[$field_name] == $value ? "checked=\"checked\" " : "")."type=\"radio\" name=\"$field_name\" "
          ."value=\"".htmlentities($value)."\"$xtrahtml />$display_name";
}
// same but with automagic LABEL FOR
function make_lradio($field_name, $value, $display_name = "", $xtrahtml = "", $id = "") {
    if ($id == "") $id = $field_name."_". htmlentities($value);
    return make_radio($field_name, $value, "<label for=\"$id\"> $display_name</label>", "id=\"$id\"".($xtrahtml != "" ? " $xtrahtml" : ""));
}

 // make a dynamic checkbox control with an optional negative hidden field before it (for yes/no type checkboxes)
function make_checkbox($field_name, $value, $negative = "", $display_name = "", $xtrahtml = "") {
    if ($xtrahtml) $xtrahtml = " $xtrahtml";
    $neg_input = (($negative === false) // note give zero as '0'
        ? ""
        : "<input type=\"hidden\" name=\"$field_name\" value=\"".htmlentities($negative)."\" />\n"
    );
    return "$neg_input<input ".($_REQUEST[$field_name] == $value ? "checked=\"checked\" " : "")."type=\"checkbox\" name=\"$field_name\" ".
          "value=\"".htmlentities($value)."\"$xtrahtml />$display_name";
}
// Same, but with automagic LABEL FOR
function make_lcheckbox($field_name, $value, $negative = "", $display_name = "", $xtrahtml = "", $id = "") {
    if ($id == "") $id = $field_name;
    return make_checkbox($field_name, $value, $negative, "<label for=\"$id\"> $display_name</label>", "id=\"$id\"".($xtrahtml != "" ? " $xtrahtml" : ""));
}
function array_to_select($array,$name, $selected = '', $attributes = '',$disabled = array()) {
    // $disabled is an optional argument. testing for it here
    $out = "<select name=\"$name\" $attributes>\n";
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $out .= "<optgroup label=\"$k\">\n";
            foreach($v as $k2 => $v2) { // optgroup
                if (is_array($selected))
                    $select = (in_array($k2,$selected)) ? ' selected' : '';
                else
                    $select = ($k2==$selected) ? ' selected="selected"' : '';
                $disable = (in_array($k2,$disabled)) ? " disabled" : "";
                $out .= "    <option$select$disable value=\"$k2\">$v2</option>\n";
            }
            $out .= "</optgroup>\n";
        } else { // regular
            if (is_array($selected))
                $select = (in_array($k,$selected)) ? ' selected' : '';
            else
                $select = ($k==$selected) ? ' selected="selected"' : '';
            $disable = (in_array($k,$disabled)) ? " disabled" : "";
            $out .= "    <option$select$disable value=\"$k\">$v</option>\n";
        }
    }
    $out .= "</select>";
    return $out;
}

?>
