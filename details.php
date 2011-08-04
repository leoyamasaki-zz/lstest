<?PHP

    require_once("../../config.php");

    if ($form = data_submitted()) {

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }


        $streditingalstest = get_string("editingstyletest", "lstest");
        $strlstest = get_string("modulenameplural", "lstest");

        print_header_simple("$streditingalstest", "",
                      "<a href=\"index.php?id=$course->id\">$strlstest</a>".
                      " -> $form->name ($streditingalstest)");

        if (!$form->name or !$form->testsid) {
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }

        print_simple_box_start("center");
        ?>
        <form name=form method=post action="<?php p("$CFG->wwwroot/course/mod.php")?>">
        <table cellpadding=5 align=center>

        <tr><td align=right nowrap><p><b><?php print_string("name") ?>:</b></p></td>
            <td><p><?php p($form->name) ?></p></td>
        </tr>

        <tr valign=top>
            <td align=right nowrap>
                <p><b><?php print_string("introtext", "lstest") ?>:</b></p><br>
                <font size="1">
                <?php helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?> <br />
                <?php helpbutton("text", get_string("helptext"), "moodle", true, true) ?> <br />
                <?php emoticonhelpbutton("form", "intro"); ?> <br />
                </font>
            </td>
            <td>
                <textarea name="intro" rows=20 cols=50><?php
                if ($form->intro) {
                    p($form->intro);
                } else {
                    print_string("introeg", "lstest");
                }
                ?></textarea>
            </td>
        </tr>

        </table>
        <input type="hidden" name=name       value="<?php p($form->name) ?>">
        <input type="hidden" name=testsid   value="<?php p($form->testsid) ?>">
        <input type="hidden" name=sesskey   value="<?php p($form->sesskey) ?>">

        <input type="hidden" name=course     value="<?php p($form->course) ?>">
        <input type="hidden" name=coursemodule     value="<?php p($form->coursemodule) ?>">
        <input type="hidden" name=section       value="<?php p($form->section) ?>">
        <input type="hidden" name=module     value="<?php p($form->module) ?>">
        <input type="hidden" name=modulename value="<?php p("lstest") ?>">
        <input type="hidden" name=instance   value="<?php p($form->instance) ?>">
        <input type="hidden" name=mode       value="<?php p($form->mode) ?>">
        <center>
        <br>
        <input type="submit" value="<?php print_string("savechanges") ?>">
        </center>
        </form>
        <?php
        print_simple_box_end();
        print_footer($course);

     } else {
        error("You can't use this page like that!");
     }

?>
