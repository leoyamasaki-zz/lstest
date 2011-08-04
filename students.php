<?php

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID
    $userid = optional_param('userid', 0, PARAM_INT);        // Course Module ID
    $actualstudent = optional_param('actualstudent', false, PARAM_BOOL);
    $coursemedia = optional_param('coursemedia', false, PARAM_BOOL);
    $categorymedia = optional_param('categorymedia', false, PARAM_BOOL);
    $totalmedia = optional_param('totalmedia', false, PARAM_BOOL);
    $zoom = optional_param('zoom', false, PARAM_BOOL);
    $writemedia = optional_param('writemedia', false, PARAM_BOOL);
    $orderby = optional_param('orderby', 'items', PARAM_ALPHA);

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

    if(!empty($userid)) {

        echo "<BR>";
        $user = get_record("user", "id", $userid);
        print_heading(get_string("seestudent", "lstest", "$user->firstname $user->lastname"));

        $coursestudentsids = lstest_course_students($lstest->testsid, $course->id);
        $categorystudentsids = lstest_category_students($lstest->testsid, $course->category);
        $allstudentsids = lstest_all_students($lstest->testsid);
        $scores = lstest_media_scores($lstest->testsid, $allstudentsids, $coursestudentsids, $categorystudentsids);
        $studentscores = lstest_student_scores($lstest->testsid, $userid);

        if ( !$actualstudent && !$coursemedia && !$categorymedia && !$totalmedia) {
            $actualstudent = true;
            $coursemedia = true;
            $categorymedia = false;
            $totalmedia = false;
        }

        lstest_print_graphic($cm->id, $userid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia);

        lstest_print_graphic_selector("students.php?id=$id&userid=$userid", $id, $course->id, $userid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia, $orderby);

        lstest_print_result_table($lstest->testsid, $userid, $studentscores, $scores['course'], $scores['category'], $scores['all']);

        print_heading_with_help(get_string("seeanswers", "lstest", "$user->firstname $user->lastname"), "studentresults", "lstest");

        lstest_print_answer_table($lstest->testsid, $id, $userid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia, $orderby);

    }

    $styles = get_records("lstest_styles", "testsid", $lstest->testsid, "id asc");
    $onestyle = current($styles);

    $students = get_course_students($cm->course);
    $table->head[0] = get_string("selectstudent", "lstest");
    $table->align[0] = "center";

    if ($students != NULL) {
        $counter=0;
        foreach ($students as $student) {

            if ( count_records("lstest_user_scores", "stylesid", $onestyle->id, "userid", $student->id) > 0) {
                $user = get_record("user", "id", $student->id);
                $table->data[$counter++][0] = "<A HREF=students.php?id=$id&userid=$student->id>$user->firstname $user->lastname</A><BR>";

            }

        }
    }
    echo "<BR>";
    print_table($table);
    echo "<BR>";
    print_footer($course);

?>


