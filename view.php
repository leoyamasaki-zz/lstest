<?php  // $Id: view.php,v 1.0 2004/09/29 16:44:43 brubior Exp $

    require_once("../../config.php");
    require_once("lib.php");

    $id      = optional_param('id', 0, PARAM_INT);        // Course Module ID

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

    add_to_log($course->id, "lstest", "view", "view.php?id=$cm->id", "$lstest->id", $cm->id);

    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($lstest->name), "", $navigation);

    $test = get_record("lstest_tests", "id", "$lstest->testsid");
    if ($test) {
        $lstestid = $test->id;

        lstest_print_result_menu($course->id, $USER->id, $id);

        $answers = get_records("lstest_answers", "testsid", "$lstestid", "id asc");
        $numanswer = count_records("lstest_answers", "testsid", "$lstestid");
        $styles = get_records("lstest_styles", "testsid", "$lstestid", "id asc");
        $onestyle = current($styles);

        $numscore = count_records("lstest_user_scores", "userid", "$USER->id", "stylesid", "$onestyle->id");

        if( $numscore > 0 ) {

            if (empty($CFG->gdversion)) {
                echo "(".get_string("gdneed").")";
            } else {

                print_heading(get_string("testcompleted", "lstest", date('j-m-y', lstest_completed_date($USER->id, $lstestid))));

                if ( !$actualstudent && !$coursemedia && !$categorymedia && !$totalmedia) {
                    $actualstudent = true;
                    $coursemedia = true;
                    $categorymedia = false;
                    $totalmedia = false;
                }

                lstest_print_graphic($cm->id, $USER->id, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia);
                lstest_print_graphic_selector("view.php?id=$id", $id, $course->id, $USER->id, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia);

                $studentscores = lstest_student_scores($lstestid, $USER->id);

                $coursestudentsids = lstest_course_students($lstestid, $course->id);
                $categorystudentsids = lstest_category_students($lstestid, $course->category);
                $allstudentsids = lstest_all_students($lstestid);
                $scores = lstest_media_scores($lstestid, $allstudentsids, $coursestudentsids, $categorystudentsids);

                lstest_print_result_table($lstestid, $USER->id, $studentscores, $scores['course'], $scores['category'], $scores['all']);
            }
        }

        if ( ( $numscore > 0) && ($test->redoallowed) ) {
            print_heading(get_string("redotest", "lstest"));
        }

        if ( ($numscore == 0 ) || $test->redoallowed ) {

            echo "<br>";
            print_simple_box("<center>".format_text($lstest->intro)."</center>", "center", "80%");


            $items = get_records("lstest_items", "testsid", "$lstestid", "id asc");
            $numitem = count_records("lstest_items", "testsid", "$lstestid");
            $numstyle = count_records("lstest_styles", "testsid", "$lstestid");


            $table->head[0] = "";
            $table->align[0] = "left";
            $table->width = "100%";
            $table->size = array("", "1", "1", "1", "1", "1", "1", "1", "1", "1", "1");

            $table->cellpadding = 15;
?>

            <script>
            <!-- // BEGIN
            function checkform() {

                var error=false;

<?php
                if (! $test->notansweredquestion) {
                    if ($test->multipleanswer) { //respuesta multiple
                        $firstanswer = current($answers);
                        foreach ($items as $item) {
                            $stranswer = "document.form.answer";
                            $condition = "(".$stranswer.$item->id.$firstanswer->id.".checked ";
                            foreach ($answers as $answer) {
                                if ($answer->id != $firstanswer->id) {
                                    $condition .= " || ".$stranswer.$item->id.$answer->id.".checked ";
                                }
                            }
                            $condition .= ")";
                            echo "  if (!".$condition.") error=true;\n";
                        }
                    } else {
                        foreach ($items as $item) {
                            $stranswer = "document.form.answer".$item->id;
                            $condition = "(".$stranswer."[0].checked ";
                            $answersnum = count($answers);
                            for ($i=1; $i<$answersnum; $i++) {
                                $condition .= " || ".$stranswer."[".$i."].checked ";
                            }
                            $condition .= ")";
                            echo "  if (!".$condition.") error=true;\n";
                        }
                    }
                }

?>

                if (error) {
                    alert("<?php print_string("questionsnotanswered", "lstest") ?>");
                } else {
                    document.form.submit();
                }
            }
            // END -->
            </script>

            <form name="form" method="post" action="save.php">
            <center>
            <br><br>

<?php
            foreach ($answers as $answer) {
                $table->head[$answer->id] = $answer->name;
                $table->align[$answer->id] = "center";
            }

            $counter = 1;
            foreach ($items as $item) {
                $table->data[$item->id][0] = "<b>$counter.- $item->question</b>";
                $counter++;
                $stritemid = "itemid".$item->id;
                echo "<input type=hidden name=$stritemid value=$item->id>";
                $stranswer = "answer".$item->id;
                foreach ($answers as $answer) {
                    if ($test->multipleanswer) { //respuesta multiple
                        $stranswer = "answer".$item->id.$answer->id;
                        $table->data[$item->id][$answer->id] = "<input type=checkbox name=$stranswer>";
                    } else {
                        $table->data[$item->id][$answer->id] = "<input type=radio name=$stranswer  value=$answer->id >";
                    }
                }
            }
            lstest_print_table($table);

?>

            <br>
            <br>

            <script>
            <!-- // BEGIN
                document.write('<input type=button value=<?php print_string("continue") ?> onClick=checkform();>');
            // END -->
            </script>

            <noscript>
                <input type="submit" value="<?php print_string("continue") ?>">
            </noscript>

            <input type="hidden" name="id" value="<?php echo $id ?>">
            <br>
            <br>
            </center>

            </form>

<?php

        }
    } 
	else {
        print_string("testnotavailable","lstest");
    }

    print_footer($course);

?>
