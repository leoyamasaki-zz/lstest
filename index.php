<?PHP

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "lstest", "view all", "index.php?id=$course->id", "");

    $strstyles  = get_string("modulenameplural", "lstest");
    $navlinks = array();
    $navlinks[] = array('name' => $strstyles, 'link' => '', 'type' => 'activity');

    print_header("$course->shortname: $strstyles", $course->fullname,
                    build_navigation($navlinks));

    if (! $tests = get_all_instances_in_course("lstest", $course)) {
        notice("There are no styless", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("CENTER", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($tests as $test) {
        $link = "<a href=\"view.php?id=$test->coursemodule\">$test->name</A>";

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($test->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br>";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
