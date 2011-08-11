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
    $answers = lstest_get_answers_submitted($test->answersnum);
    $items = lstest_get_items($test->itemsnum, $test->answersnum, $test->id, $styledefined);

    $modpath = "$CFG->wwwroot/mod/lstest";

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strmanagemodules = get_string("managemodules");
    $strmodulename = get_string("modulename", "lstest");
    $streditingtest = get_string("editingitems", "lstest");

    print_header("$site->shortname: $strmodulename", $site->fullname,
                 "<a href=\"$CFG->wwwroot/admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/modules.php\">$strmanagemodules</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/module.php?module=lstest\">$strmodulename</a> -> $streditingtest");


    $icon = "<img align=absmiddle height=16 width=16 src=\"$modpath/icon.gif\">&nbsp;";

    $pageheading = get_string('addingitems', "lstest");

    print_heading_with_help($pageheading, "items", "lstest", $icon);
    print_simple_box_start("center", "");
?>

<script>
<!-- // BEGIN
function checkform() {

    var error=false;

<?php
    for ($i=1; $i<=$test->itemsnum; $i++) {
        $stritemstatement = "!document.form.question".$i.".value";
        echo("  if (" . $stritemstatement .") error=true;\n");
    }
    
    echo("  if (error) {\n");
    echo("    alert(\"" . get_string('itemsnotanswered', 'lstest') . "\")\n");
?>
    } 
    else {
        document.form.submit();
    }
}
// END -->
</script>

<form name="form" method="post" action="<?php p($modpath) ?>/thresholds.php">
<center>
<table cellpadding=5 border="1">

<tr valign=top>
    <td></td>
    <td></td>
<!-- modification -->
<?php
    if($styledefined == 0) {
       echo("<td></td>\n");
    }
    for($i=1; $i<=$test->answersnum; $i++) {
        $stranswer = "answer".$i;
        echo("<td colspan=2 align=center><b>\n");
        p($answers->$stranswer);
        echo("</b></td>\n");
    }
?>
</tr>

<tr valign=top>
    <td></td>
    <td></td>
<?php
    if($styledefined == 0) {
       echo("<td></td>\n");
    }
    for($i=1; $i<=$test->answersnum; $i++) {
        echo("<td align=\"center\" valign=\"center\">\n");
        print_string("notchecked", "lstest");
        echo("</td>\n");
        echo("<td align=\"center\" valign=\"center\">\n");
        print_string("checked", "lstest");
        echo("</td>\n");
    }
?>
</tr>

<?php

if ( !empty($test->id)) {
    $onestyles = get_records("lstest_styles", "testsid", $test->id, "id asc");
    $i = 1;
    foreach ($onestyles as $onestyle) {
        $styleids[$i++] = $onestyle->id;
    }
    $i = 1;
    foreach ($styles as $stylename) {
        $options[$styleids[$i++]] = $stylename;
    }
} else {
    $i = 1;
    foreach ($styles as  $stylename) {
        $options[$i++] = $stylename;
    }
}

for ($i=-10; $i<=10; $i++) {
    $scoreoptions[$i] = "$i";
}

for ($i=1; $i<=$test->itemsnum; $i++) {
    if($i % 2 == 0) {
       $bgcolor="#999999";
    }
    else
    {
       $bgcolor="#CCCCCC";
    }
    $strquestion = "question".$i;
    $strstylesid = "stylesid".$i;
    echo("<tr valign=top>\n");
    echo("<td align=\"center\" valign=\"center\" bgcolor=\"" . $bgcolor. "\">\n");
    echo("<p><b>" . get_string("itemnumber", "lstest", $i) . ":</b></p>\n");
    echo("</td>\n");
    echo("<td align=\"right\" valign=\"center\" bgcolor=\"" . $bgcolor. "\">\n");
    echo("<textarea name=\"" . $strquestion . "\" rows=\"4\" cols=\"50\">" . $items->$strquestion ."</textarea>\n");
    echo("</td>\n");
    if($styledefined == 0) {
        echo("<td align=\"center\" valign=\"center\" bgcolor=\"" . $bgcolor. "\">\n");
        choose_from_menu($options, "$strstylesid", $items->$strstylesid, "");
        echo("</td>\n");
    }
    for ($j=1; $j<=$test->answersnum; $j++) {
        $strnocheckedscore = "nocheckedscore".$i.$j;
        $strcheckedscore = "checkedscore".$i.$j;
        echo("<td align=\"center\" valign=\"center\" bgcolor=\"" . $bgcolor. "\">\n");
        choose_from_menu($scoreoptions, "$strnocheckedscore", $items->$strnocheckedscore, "");
        echo("</td>\n");
        echo("<td align=\"center\" valign=\"center\" bgcolor=\"" . $bgcolor. "\">\n");
        choose_from_menu($scoreoptions, "$strcheckedscore", $items->$strcheckedscore, "");
        echo("</td>\n");
    }
    if($styledefined == 1) {
        echo("</tr>\n<tr>\n");
        echo("<td bgcolor=\"" . $bgcolor. "\"></td>\n");
        echo("<td bgcolor=\"" . $bgcolor. "\"></td>\n");
        for ($j=1; $j<=$test->answersnum; $j++) {
            $strstylesid = "stylesid".$i.$j;
//TEST
            echo("\n<!-- Prueba\n");
            echo(print_r($options));
            //echo("\n $items->$strstylesid=" . print_r($items->$strstylesid));
            echo("\n$strstylesid=" . $strstylesid);
            echo("\n-->\n");
//ENDTEST
            echo("<td colspan=\"2\" valign=\"center\" align=\"center\" bgcolor=\"" . $bgcolor. "\">\n");
            choose_from_menu($options, "$strstylesid", $items->$strstylesid, "");
            echo("</td>\n");
        }
    }
    echo("</tr>\n");
}
?>

</table>
<br>
<script>
<!-- // BEGIN
    document.write('<input type=button value="<?php print_string('continue') ?>" onClick=checkform()>');
// END -->
</script>

<noscript>
    <input type="submit" value="<?php print_string("continue") ?>">
</noscript>

<?php
    lstest_submit_test($test);
    lstest_submit_styles($test->stylesnum, $styles);
    lstest_submit_levels($test->levelsnum, $levels);
    lstest_submit_answers($test->answersnum, $answers);
?>

<input type="hidden" name="sesskey" value="<?php p("$USER->sesskey") ?>">

</center>
</form>

<?php
    print_simple_box_end();
    print_footer();
?>
