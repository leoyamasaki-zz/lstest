<?php

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID

    if (! $cm = get_coursemodule_from_id('lstest', $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    if (! $lstest = get_record("lstest", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);

    if ( (!isadmin()) && (!isteacher($course->id)) ) {
        error(get_string("mustbeadminorteacher", "lstest"));
    }

    add_to_log($course->id, "lstest", "view", "view.php?id=$cm->id", "$lstest->id");

    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($lstest->name), "", $navigation);

    lstest_print_result_menu($course->id, $USER->id, $id);

    lstest_predominance_tables($lstest->testsid, $course->id);

    echo "<BR>";
    print_footer($course);

?>


