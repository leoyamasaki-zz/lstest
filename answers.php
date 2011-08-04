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
	$styledefined = $test->styledefined;
    $styles = lstest_get_styles_submitted($test->stylesnum);
    $levels = lstest_get_levels_submitted($test->levelsnum);
    $answers = lstest_get_answers($test->answersnum, $test->id);

    $modpath = "$CFG->wwwroot/mod/lstest";


?>

<script>
<!-- // BEGIN
function checkform() {

    var error=false;

    <?php
    for ($i=1; $i<=$test->answersnum; $i++) {
        $stranswer = "document.form.answer".$i.".value";
        echo "  if (!".$stranswer.") error=true;\n";
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

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strmanagemodules = get_string("managemodules");
    $strmodulename = get_string("modulename", "lstest");
    $streditingtest = get_string("editinganswers", "lstest");

    print_header("$site->shortname: $strmodulename", $site->fullname,
                 "<a href=\"$CFG->wwwroot/admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/modules.php\">$strmanagemodules</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/module.php?module=lstest\">$strmodulename</a> -> $streditingtest");


    $icon = "<img align=absmiddle height=16 width=16 src=\"$modpath/icon.gif\">&nbsp;";

    $pageheading = get_string('addinganswers', "lstest");

    print_heading_with_help($pageheading, "answernames", "lstest", $icon);
    print_simple_box_start("center", "");
?>

<FORM name="form" method="post" action="<?php p($modpath) ?>/items.php">
<CENTER>
<TABLE cellpadding=5>

<?php

for ($i=1; $i<=$test->answersnum; $i++) {
    $stranswer = "answer".$i;

?>

<TR valign=top>
    <TD align=right><P><B><?php  print_string('answername', "lstest", $i) ?>:</B></P></TD>
    <TD>
        <INPUT type="text" name="<?php p($stranswer) ?>" size=30 value="<?php  p($answers->$stranswer) ?>">
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

<?php
    lstest_submit_test($test); 
    lstest_submit_styles($test->stylesnum, $styles);
    lstest_submit_levels($test->levelsnum, $levels);
?>

<input type="hidden" name="sesskey" value="<?php p("$USER->sesskey") ?>">

</CENTER>
</FORM>

<?php
    print_simple_box_end();
    print_footer();
?>
