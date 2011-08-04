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
    $items = lstest_get_items_submitted($test->itemsnum, $test->answersnum, $styledefined);
    $thresholds = lstest_get_thresholds($test->stylesnum, $test->levelsnum, $test->id);
    
    $modpath = "$CFG->wwwroot/mod/lstest";

    if ( !empty($test->id)) {
        $inurl = "?update=".$test->name;
    }
    else{
        $inurl = "?add=".$test->name;
    }

        
    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strmanagemodules = get_string("managemodules");
    $strmodulename = get_string("modulename", "lstest");
    $streditingtest = get_string("editingthresholds", "lstest");

    print_header("$site->shortname: $strmodulename", $site->fullname,
                 "<a href=\"$CFG->wwwroot/admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/modules.php\">$strmanagemodules</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/module.php?module=lstest\">$strmodulename</a> -> $streditingtest");


    $icon = "<img align=absmiddle height=16 width=16 src=\"$modpath/icon.gif\">&nbsp;";

    $pageheading = get_string('addingthresholds', "lstest");

    print_heading_with_help($pageheading, "thresholds", "lstest", $icon);


?>


<form name="form" method="post" action="<?php p($modpath) ?>/change.php<?php p($inurl) ?>">
<center>

<?php

for ($i=-$test->itemsnum; $i<=$test->itemsnum; $i++) {
    $options[$i] = "$i";
}

for ($i=1; $i<=$test->stylesnum; $i++) {

    $strstyle = "style".$i;
    $pageheading = get_string('forstyle', "lstest", $styles->$strstyle);

    print_heading($pageheading);
    print_simple_box_start("center", "");

?>
    <table cellpadding=4>
    <tr align=center>
        <td></td>
        <td> <?php p(get_string('minthreshold', "lstest")) ?> </td>
        <td>-</td>
        <td> <?php p(get_string('maxthreshold', "lstest")) ?> </td>
    </tr>

<?php

    for ($j=1; $j<=$test->levelsnum; $j++) {

        $strlevel = "level".$j;
        $strinfthreshold = "infthreshold".$i.$j;
        $strsupthreshold = "supthreshold".$i.$j;

?>

        <tr>
            <td align=right><p><b><?php  print_string('forlevel', "lstest", $levels->$strlevel) ?>:</b></p></td>
            <td>
                <?php choose_from_menu($options, "$strinfthreshold", $thresholds->$strinfthreshold, "") ?>
            </td>
            <td>-</td>
            <td>
                <?php choose_from_menu($options, "$strsupthreshold", $thresholds->$strsupthreshold, "") ?>
            </td>
        </tr>

<?php

    }

?>

</table>

<?php

    print_simple_box_end();
}
?>

<br>
<input type="submit" value="<?php  print_string("savechanges") ?>">

<?php
    lstest_submit_test($test); 
    lstest_submit_styles($test->stylesnum, $styles);
    lstest_submit_levels($test->levelsnum, $levels);
    lstest_submit_answers($test->answersnum, $answers);
    lstest_submit_items($test->itemsnum, $test->answersnum, $items,$styledefined);
?>

<input type="hidden" name="sesskey" value="<?php p("$USER->sesskey") ?>">

</center>
</form>

<?php
    print_footer();
?>
