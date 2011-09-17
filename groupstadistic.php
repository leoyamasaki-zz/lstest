<?php

    require_once("../../config.php");
    require_once("lib.php");
    require_once("libcluster.php");

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID

    if ($id) {
        if (! $cm = get_coursemodule_from_id('lstest', $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $lstest = get_record("lstest", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    }

    require_login($course->id);

    if ( (!isadmin()) && (!isteacher($course->id)) ) {
        error(get_string("mustbeadminorteacher", "lstest"));
    }

    add_to_log($course->id, "lstest", "view", "view.php?id=$cm->id", "$lstest->id");

    /// Print the page header
    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($lstest->name), "", $navigation);
    lstest_print_result_menu($course->id, $USER->id, $id);
	/// END header

//	print_heading("Testing...");
//	global $DB;


	$styles = get_records("lstest_styles", "testsid", "$lstest->testsid", "id asc");
	foreach($styles as $style){
		$styleslist[$style->id] = $style->id;
	}
	
/*	
	$table->align = array("center","center","center");
	$table->head = array("ID","","");
	$table->data = array();
	lstest_print_table($table);
*/
	print_r($styleslist);
	echo("<br>\n");
	print_r(lstest_all_users_scores($lstest->testsid));
	echo("<br>\n");
	print_r(lstest_course_scores($course->id, $lstest->testsid));
	echo("<br>\n");
	$studentsincourse = lstest_course_students($lstest->testsid, $course->id);
	print_r($studentsincourse);
	echo("<br>\n");
	echo("<div><table border='1'>\n");
	$data = array();
	$n = 0;
	foreach($studentsincourse as $studentid){
		echo("<tr><td>\n");
		$scores = lstest_user_scores($lstest->testsid, $studentid);
		$m = 0;
		foreach($scores as $val){
			$data[$n][$m] = $val;
			$m++;
		}
		$n++;
		print_r($scores);
		echo("</td></tr>\n");
	}
	echo("</div></table>\n");
//	print_r($data);
	$datamatrix = new Math_Matrix($data);
	echo($datamatrix->toHTML());
	$numclases = 3;
	$classasign = lstest_Kmeans($datamatrix,$numclases,"cartesian",10);
	echo($classasign->toHTML());
/*
    $table->align = array("center", "center", "center", "center", "center", "center", "center", "center", "center", "center");
    $coursestudentsids = lstest_course_students($lstest->testsid, $course->id);
    $categorystudentsids = lstest_category_students($lstest->testsid, $course->category);
    $allstudentsids = lstest_all_students($lstest->testsid);

    $scores = lstest_media_scores($lstest->testsid, $allstudentsids, $coursestudentsids, $categorystudentsids);

    $coursestudentsnum = count($coursestudentsids);
    $categorystudentsnum = count($categorystudentsids);
    $allstudentsnum = count($allstudentsids);


    echo "\n<br>\n";

    $inthecoursestr = get_string("inthecourse", "lstest");
    $inthecategorystr = get_string("inthecategory", "lstest");
    $inmoodlestr = get_string("inmoodle", "lstest");
    print_heading(get_string("numberofstudents", "lstest"));
    $table->head = array($inthecoursestr, $inthecategorystr, $inmoodlestr);
    $table->data = array();
    $table->data[] = array($coursestudentsnum, $categorystudentsnum, $allstudentsnum);
    lstest_print_table($table);

    $styles = get_records("lstest_styles", "testsid", "$lstest->testsid", "id asc");
    foreach ($styles as $style){
        $coursemaxscores[$style->id] = -90000;
        $courseminscores[$style->id] = 90000;
        $categorymaxscores[$style->id] = -90000;
        $categoryminscores[$style->id] = 90000;
        $totalmaxscores[$style->id] = -90000;
        $totalminscores[$style->id] = 90000;
    }
    foreach ($allstudentsids as $studentid) {
        $studentscores = lstest_student_scores($lstest->testsid, $studentid);
        foreach ($styles as $style) {
            if ($studentscores[$style->id] > $totalmaxscores[$style->id]) {
                $totalmaxscores[$style->id] = $studentscores[$style->id];
            }
            if ($studentscores[$style->id] < $totalminscores[$style->id]) {
                $totalminscores[$style->id] = $studentscores[$style->id];
            }
            if (in_array($studentid, $coursestudentsids)) {
                if ($studentscores[$style->id] > $coursemaxscores[$style->id]) {
                    $coursemaxscores[$style->id] = $studentscores[$style->id];
                }
                if ($studentscores[$style->id] < $courseminscores[$style->id]) {
                    $courseminscores[$style->id] = $studentscores[$style->id];
                }
            }
            if (in_array($studentid, $categorystudentsids)) {
                if ($studentscores[$style->id] > $categorymaxscores[$style->id]) {
                    $categorymaxscores[$style->id] = $studentscores[$style->id];
                }
                if ($studentscores[$style->id] < $categoryminscores[$style->id]) {
                    $categoryminscores[$style->id] = $studentscores[$style->id];
                }
            }
        }
    }
    foreach ($styles as $style) {
        if ($coursestudentsnum == 0) {
            $coursemaxscores[$style->id] = "";
            $courseminscores[$style->id] = "";
        }
        if ($categorystudentsnum == 0){
            $categorymaxscores[$style->id] = "";
            $categoryminscores[$style->id] = "";
        }
        if ($allstudentsnum == 0) {
            $totalmaxscores[$style->id] = "";
            $totalminscores[$style->id] = "";
        }

    }

    echo "\n<br>\n";

    print_heading(get_string("testresults", "lstest"));
    $table->head = array(get_string("style", "lstest"), get_string("coursemedia", "lstest"), get_string("categorymedia", "lstest"), get_string("totalmedia", "lstest"));
    $table->data = array();
    foreach ($styles as $style) {
        $table->data[] = array($style->name, $scores['course'][$style->id], $scores['category'][$style->id], $scores['all'][$style->id]);
    }
    lstest_print_table($table);

*/

/*
    echo "<BR>";
    print_heading_with_help(get_string("maxandminresults", "lstest"), "maxminscores", "lstest");
    $table->headcolspan = array("1", "2", "2", "2");
    $table->head = array(get_string("style", "lstest"), $inthecoursestr, $inthecategorystr, $inmoodlestr);
    $table->data = array();
    $maxscorestr = get_string("maxscore", "lstest");
    $minscorestr = get_string("minscore", "lstest");
    $table->data[0] = array("", "<FONT size=1>".$maxscorestr."</FONT>", "<FONT size=1>".$minscorestr."</FONT>", "<FONT size=1>".$maxscorestr."</FONT>", "<FONT size=1>".$minscorestr."</FONT>", "<FONT size=1>".$maxscorestr."</FONT>", "<FONT size=1>".$minscorestr."</FONT>");
    foreach ($styles as $style) {
        $table->data[] = array($style->name, $coursemaxscores[$style->id], $courseminscores[$style->id], $categorymaxscores[$style->id], $categoryminscores[$style->id], $totalmaxscores[$style->id], $totalminscores[$style->id]);
    }

    lstest_print_table($table);
*/
    echo "<br>\n";

    print_footer($course);

?>


