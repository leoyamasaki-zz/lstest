<?php

    require("../../config.php");
    require("lib.php");

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

?>

<script>
<!-- // BEGIN
function checkform() {

    if (!document.form.name.value) {
        alert("<?php print_string("namenotanswered", "lstest") ?>");
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
    $streditingtest = get_string("editingstyletest", "lstest");

    print_header("$site->shortname: $strmodulename: $strconfiguration", $site->fullname,
                 "<a href=\"$CFG->wwwroot/admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/modules.php\">$strmanagemodules</a> -> ".
                 "<a href=\"$CFG->wwwroot/admin/module.php?module=lstest\">$strmodulename</a> -> $streditingtest");

    if (!empty($_GET['testid'])) {
        $pageheading = get_string("editingstyletest", "lstest");
        $test = lstest_get_test($_GET['testid']);
    }
    else  {
        $pageheading = get_string("addinganew", "moodle", get_string("modulename", "lstest"));
        $test = lstest_get_test("");
    }

    $icon = "<img align=absmiddle height=16 width=16 src=\"$modpath/icon.gif\">&nbsp;";
    print_heading_with_help($pageheading, "mods", "lstest", $icon);
    print_simple_box_start("center", "");

?>


<FORM name="form" method="post" action="<?php p($modpath) ?>/lstyles.php">
<CENTER>
<TABLE cellpadding=5>
<TR valign=top>
    <TD align=right><P><B><?php  print_string("name") ?>:</B></P></TD>
    <TD>
        <INPUT type="text" name="name" size=30 value="<?php  p($test->name) ?>">
    </TD>
</TR>

<TR valign=top>
    <TD align=right><P><B><?php  print_string("language") ?>:</B></P></TD>
    <TD>
        <?php choose_from_menu (get_list_of_languages(), "lang", $test->lang, "", "", ""); ?>
    </TD>
</TR>


<tr valign=top>
    <td align=right><P><B><?php  print_string("stylesnum", "lstest") ?>:</B></P></TD>
    <td>
        <?php
        for ($i=10; $i>=2; $i--) {
            $options[$i] = $i;
        }
        choose_from_menu($options, "stylesnum", $test->stylesnum, "");
        helpbutton("stylesnum", get_string("stylesnum", "lstest"), "lstest");
        ?>
    </td>
</tr>

<tr valign=top>
    <td align=right><P><B><?php  print_string("levelsnum", "lstest") ?>:</B></P></TD>
    <td>
        <?php
        unset($options);
        for ($i=10; $i>=2; $i--) {
            $options[$i] = $i;
        }
        choose_from_menu($options, "levelsnum", $test->levelsnum, "");
        helpbutton("levelsnum", get_string("levelsnum", "lstest"), "lstest");
        ?>
    </td>
</tr>

<tr valign=top>
    <td align=right><P><B><?php  print_string("itemsnum", "lstest") ?>:</B></P></TD>
    <td>
        <?php
        unset($options);
        for ($i=100; $i>=2; $i--) {
            $options[$i] = $i;
        }
        choose_from_menu($options, "itemsnum", $test->itemsnum, "");
        helpbutton("itemsnum", get_string("itemsnum", "lstest"), "lstest");
        ?>
    </td>
</tr>

<tr valign=top>
    <td align=right><P><B><?php  print_string("answersnum", "lstest") ?>:</B></P></TD>
    <td>
        <?php
        unset($options);
        for ($i=10; $i>=2; $i--) {
            $options[$i] = $i;
        }
        choose_from_menu($options, "answersnum", $test->answersnum, "");
        helpbutton("answersnum", get_string("answersnum", "lstest"), "lstest");
        ?>
    </td>
</tr>

<!-- Begin Modification by LYM -->

<tr>
    <td align=right><p><b><?php  print_string("styledefined", "lstest") ?>:</B></P></TD>
    <td>
    <?php
        unset($options);
        $options[0] = get_string("styledefinedquestion","lstest");
        $options[1] = get_string("styledefinedanswer","lstest");
        choose_from_menu($options, "styledefined", $test->styledefined, "");
        helpbutton("styledefined", get_string("styledefined", "lstest"), "lstest");
    ?>
    </td>
</tr>

<!-- End modification by LYM -->

<tr>
    <td align=right><p><b><?php  print_string("available", "lstest") ?>:</b></p></td>
    <td>
    <?php
        unset($options);
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "available", $test->available, "");
        helpbutton("available", get_string("available", "lstest"), "lstest");
    ?>
    </td>
</tr>

<tr>
    <td align=right><P><B><?php  print_string("redoallowed", "lstest") ?>:</B></P></TD>
    <td>
    <?PHP
        unset($options);
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "redoallowed", $test->redoallowed, "");
        helpbutton("redoallowed", get_string("redoallowed", "lstest"), "lstest");
    ?>
    </td>
</tr>

<tr>
    <td align=right><P><B><?php  print_string("multipleanswer", "lstest") ?>:</B></P></TD>
    <td>
    <?PHP
        unset($options);
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "multipleanswer", $test->multipleanswer, "");
        helpbutton("multipleanswer", get_string("multipleanswer", "lstest"), "lstest");
    ?>
    </td>
</tr>

<tr>
    <td align=right><P><B><?php  print_string("notansweredquestion", "lstest") ?>:</B></P></TD>
    <td>
    <?PHP
        unset($options);
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        choose_from_menu($options, "notansweredquestion", $test->notansweredquestion, "");
        helpbutton("notansweredquestion", get_string("notansweredquestion", "lstest"), "lstest");
    ?>
    </td>
</tr>

</TABLE>

<input type="hidden" name="id"   value="<?php p("$test->id") ?>">
<input type="hidden" name="sesskey" value="<?php p("$USER->sesskey") ?>">

<br>

<script>
<!-- // BEGIN
    document.write('<input type=button value=<?php print_string("continue") ?> onClick=checkform();>');
// END -->
</script>

<noscript>
    <input type="submit" value="<?php print_string("continue") ?>">
</noscript>

</CENTER>
</FORM>

<?php
    print_simple_box_end();
    print_footer();
?>
