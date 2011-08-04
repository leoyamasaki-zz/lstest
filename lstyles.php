<?php

    require_once("../../config.php");
    require_once("lib.php");

    require_login();

    if (! isadmin()) {
        error(get_string("youcannotchange", "lstest"));
    }

    if (! $site = get_site()) {
        error(get_string("sitedoesntexist", "lstest"));
    }
        
    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }
        
    $test = lstest_get_test_submitted();

    $styles = lstest_get_styles($test->stylesnum, $test->id);

    if (!$test->name || !$test->lang) {
        error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
    } else {
        if ($test->id) {
            $oldtest = get_record("lstest_tests", "id", $test->id);    
            if ( ( ($oldtest->name != $test->name) || ($oldtest->lang != $test->lang) ) && count_records("lstest_tests", "name", $test->name, "lang", $test->lang) > 0 ) {
                error(get_string("testexists", "lstest"), $_SERVER["HTTP_REFERER"]);
            }        
        } else {
            if ( count_records("lstest_tests", "name", "$test->name", "lang", "$test->lang") > 0 ) {
                error(get_string("testexists", "lstest"), $_SERVER["HTTP_REFERER"]);
            }        
        }
    }

        
?>

<script>
<!-- // BEGIN
function checkform() {

    var error=false;

    <?php
    for ($i=1; $i<=$test->stylesnum; $i++) {
        $strstyle = "document.form.style".$i.".value";
        echo "  if (!".$strstyle.") error=true;\n";
    }
    ?>

    if (error) {
        alert("<?php print_string("fillallfields", "lstest") ?>");
    } else {
        document.form.submit();
    }
}
// END -->
</script>

<?php
    
    $modpath = "$CFG->wwwroot/mod/lstest";

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strmanagemodules = get_string("managemodules");
    $strmodulename = get_string("modulename", "lstest");
    $streditingtest = get_string("editingstyles", "lstest");
    

    print_header("$site->shortname: $strmodulename", $site->fullname,
                 "<a href=\"$CFG->wwwroot/admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/modules.php\">$strmanagemodules</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/module.php?module=lstest\">$strmodulename</a> -> $streditingtest");


    $icon = "<img align=absmiddle height=16 width=16 src=\"$modpath/icon.gif\">&nbsp;";

    $pageheading = get_string('addingstyles', "lstest");

    print_heading_with_help($pageheading, "stylenames", "lstest", $icon);
    print_simple_box_start("center", "");
?>

<FORM name="form" method="post" action="<?php p($modpath) ?>/levels.php">
<CENTER>
<TABLE cellpadding=5>

<?php
for ($i=1; $i<=$test->stylesnum; $i++) {
    $strstyle = "style".$i;
?>

    <TR valign=top>
        <TD align=right><P><B><?php  print_string('stylename', "lstest", $i) ?>:</B></P></TD>
        <TD>
            <INPUT type="text" name="<?php p($strstyle) ?>" size=30 value="<?php  p($styles->$strstyle) ?>">
        </TD>
    </TR>

<?php
}
?>

</TABLE>
<br>

<script>
<!-- // BEGIN
    document.write('<input type=button value=<?php print_string("continue") ?> onClick=checkform();>');
// END -->
</script>

<noscript>
    <input type="submit" value="<?php print_string("continue") ?>">
</noscript>


<?php lstest_submit_test($test); ?>

<input type="hidden" name="sesskey" value="<?php p("$USER->sesskey") ?>">

</CENTER>
</FORM>

<?php
    print_simple_box_end();
    print_footer();
?>
