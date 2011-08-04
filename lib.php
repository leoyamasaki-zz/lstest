<?php

function lstest_print_graphic($moduleid, $userid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia) {

    echo "<center><img  border=\"1\" src=\"graph.php?id=$moduleid&sid=$userid&actualstudent=$actualstudent&coursemedia=$coursemedia&categorymedia=$categorymedia&totalmedia=$totalmedia&zoom=$zoom&writemedia=$writemedia\"></center>";

}

function lstest_print_graphic_selector($url, $moduleid, $courseid, $userid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia, $orderby="items") {
    if ( !isset($actualstudent) && !isset($coursemedia) && !isset($categorymedia) && !isset($totalmedia)) {
        $actualstudent = true;
        $coursemedia = true;
    }
    echo "<br>";
    echo "<form method=\"post\" action=\"$url\">\n";
    echo "<center>\n";
    if ($courseid > 1) {
		echo "<input type=\"checkbox\" name=\"actualstudent\" VALUE=\"true\" "; frmchecked($actualstudent);
 		echo ">".get_string("actualstudent", "lstest") . " ";
        echo "<input type=\"checkbox\" NAME=\"coursemedia\" VALUE=\"true\" "; frmchecked($coursemedia);
 		echo ">".get_string("coursemedia", "lstest")." ";
        echo "<input type=\"checkbox\" NAME=\"categorymedia\" VALUE=\"true\" "; frmchecked($categorymedia);
 		echo ">".get_string("categorymedia", "lstest")." ";
    } else {
        echo "<input type=\"checkbox\" NAME=\"actualstudent\" VALUE=\"true\" "; frmchecked($actualstudent); echo ">".get_string("actualuser", "lstest")." ";
    }
    echo "<input type=checkbox NAME=totalmedia VALUE=true "; frmchecked($totalmedia); echo ">".get_string("totalmedia", "lstest")." ";
    echo "<BR>";
    echo "<input type=checkbox NAME=zoom VALUE=true "; frmchecked($zoom); echo ">".get_string("zoom", "lstest")." ";
    echo "<input type=checkbox NAME=writemedia VALUE=true "; frmchecked($writemedia); echo ">".get_string("writemedias", "lstest")." ";
    echo "<input type=hidden NAME=orderby VALUE=$orderby > ";
    echo "<br><br>";
    echo "<input type=submit VALUE=\"".get_string("redographic", "lstest")."\">";
    helpbutton("redraw", get_string("redographic", "lstest"), "lstest");
    echo "</center>";
    echo "</form>";
    echo "<br><br>";
}

function lstest_print_result_menu($courseid, $userid, $moduleid) {
    if( isteacher($courseid, $userid) ) {
        $table->align = array("right", "left", "right", "left", "right", "left", "right", "left");
        $table->size = array("1%", "1%", "1%", "1%", "1%", "1%", "1%", "1%");
        $table->data[0][0] = "<a href=students.php?id=$moduleid>".get_string("seestudents", "lstest")."</a>";
        $table->data[0][1] = helpbutton("students", get_string("seestudents", "lstest"), "lstest", true, false, "", true);
        $table->data[0][2] = "<a href=stylestadistic.php?id=$moduleid>".get_string("seestylestadistic", "lstest")."</a>";
        $table->data[0][3] = helpbutton("stylestadistic", get_string("seestylestadistic", "lstest"), "lstest", true, false, "", true);
        $table->data[0][4] = "<a href=itemstadistic.php?id=$moduleid>".get_string("seeitemstadistic", "lstest")."</a>";
        $table->data[0][5] = helpbutton("itemstadistic", get_string("seeitemstadistic", "lstest"), "lstest", true, false, "", true);
        $table->data[0][6] = "<a href=teststadistic.php?id=$moduleid>".get_string("seeteststadistic", "lstest")."</a>";
        $table->data[0][7] = helpbutton("teststadistic", get_string("seeteststadistic", "lstest"), "lstest", true, false, "", true);
        lstest_print_table($table);
    }
}

/*function lstest_print_result_menu($courseid, $userid, $moduleid) {

    global $CFG;

    if( isteacher($courseid, $userid) ) {
        $tabs = array();
        $row  = array();


        // I don't use this function because help buttons cannot be added to rows.
        $row[] = new tabobject('students', "$CFG->wwwroot/mod/lstest/students.php?id=$moduleid", get_string('seestudents', 'lstest'));

        $row[] = new tabobject('stylestadistic', "$CFG->wwwroot/mod/lstest/stylestadistic.php?id=$moduleid", get_string('seestylestadistic', 'lstest'));

        $row[] = new tabobject('itemstadistic', "$CFG->wwwroot/mod/lstest/itemstadistic.php?id=$moduleid", get_string('seeitemstadistic', 'lstest'));
        $row[] = new tabobject('teststadistic', "$CFG->wwwroot/mod/lstest/teststadistic.php?id=$moduleid", get_string('seeteststadistic', 'lstest'));

        $tabs[] = $row;

        $show = 'students';

        print_tabs($tabs, $show);

    }
}*/

function lstest_print_result_table($testid, $userid, $studentscores, $coursescoresmedia, $categoryscoresmedia, $totalscoresmedia) {

    $table->size = array("1", "1", "1", "1", "1", "1");
    $table->align = array("center", "center", "center", "center", "center", "center");
    $table->head = array(get_string("style", "lstest"), get_string("score", "lstest"), get_string("pertenency", "lstest"), get_string("coursemedia", "lstest"), get_string("categorymedia", "lstest"), get_string("totalmedia", "lstest"));
    $table->data = array();
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc", "id,name");
    foreach ($styles as $style) {
        $score = $studentscores[$style->id];
        $select = "stylesid = \"$style->id\" AND infthreshold <= \"$score\" AND supthreshold >= \"$score\"";
        $thresholds = get_records_select("lstest_thresholds", $select, "id asc", "*", "0", "1");
        $threshold = current($thresholds);
        $level = get_record("lstest_levels", "id", "$threshold->levelsid");
        array_push($table->data, array($style->name, $score, $level->name, $coursescoresmedia[$style->id], $categoryscoresmedia[$style->id], $totalscoresmedia[$style->id]));
    }
    lstest_print_table($table);
    echo "<BR>";
}

function lstest_print_answer_table($testid, $id, $userid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $zoom, $writemedia, $orderby="items") {
    if ( !isset($actualstudent) && !isset($coursemedia) && !isset($categorymedia) && !isset($totalmedia)) {
        $actualstudent = true;
        $coursemedia = true;
    }

    $items = get_records("lstest_items", "testsid", "$testid", "id asc");
    $firstitem = current($items);
    $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    $tablestyles = array();
    foreach ($styles as $style) {
        $tablestyles[$style->id] = array();
    }

    $counter = 1;
    $table->align = array("center", "center");
    if ($orderby == "styles") {
        $table->head = array(get_string("question","lstest")."<BR><A HREF=students.php?id=$id&userid=$userid&actualstudent=$actualstudent&coursemedia=$coursemedia&categorymedia=$categorymedia&totalmedia=$totalmedia&zoom=$zoom&writemedia=$writemedia&orderby=items><FONT SIZE=1>".get_string("orderbyitems","lstest")."</FONT></A>", get_string("style","lstest"));
    } else {
        $table->head = array(get_string("question","lstest")."<BR><A HREF=students.php?id=$id&userid=$userid&actualstudent=$actualstudent&coursemedia=$coursemedia&categorymedia=$categorymedia&totalmedia=$totalmedia&zoom=$zoom&writemedia=$writemedia&orderby=styles><FONT SIZE=1>".get_string("orderbystyles","lstest")."</FONT></A>", get_string("style","lstest"));
    }
    $table->size = array("10", "10");

    foreach ($answers as $answer) {
        array_push($table->head, $answer->name);
        array_push($table->size, "10");
        array_push($table->align, "center");
    }

    $studentscores = lstest_student_item_answers($testid, $userid);

    if ($orderby == "styles") {
        foreach ($items as $item) {
            $tablestyles[$item->stylesid][$item->id] = array();
            foreach ($answers as $answer) {
                array_push($tablestyles[$item->stylesid][$item->id], $studentscores[$item->id][$answer->id]);
            }
        }
        foreach ($styles as $style) {
            foreach ($tablestyles[$style->id] as $key => $answersss ) {
                $counter = $key - $firstitem->id + 1;
                $table->data[$counter] = array("<A HREF=\"itemstadistic.php?id=$id&item=$key\">$counter.- ".$items[$key]->question."</A>");
                array_push($table->data[$counter], $style->name);
                foreach ($answersss as $answer) {
                    if ($answer == "checked") {
                        array_push($table->data[$counter], get_string("yes"));
                    } else {
                        array_push($table->data[$counter], get_string("no"));
                    }
                }
            }
        }
    } else {
        foreach ($items as $item) {
            $table->data[$item->id] = array("<A HREF=\"itemstadistic.php?id=$id&item=$item->id\">$counter.- $item->question</A>");
            array_push($table->data[$item->id], $styles[$item->stylesid]->name);
            $counter++;
            foreach ($answers as $answer) {
                if ($studentscores[$item->id][$answer->id] == "checked") {
                    array_push($table->data[$item->id], get_string("yes"));
                } else {
                    array_push($table->data[$item->id], get_string("no"));
                }
            }
        }
    }

    lstest_print_table($table);
}

function lstest_completed_date($userid, $lstestid) {
    $styles = get_records("lstest_styles", "testsid", $lstestid);
    $onestyle = current($styles);
    $userscores = get_records_select("lstest_user_scores", "stylesid = '$onestyle->id' AND userid = '$userid'", "time desc", "*", "0", "1");
    $lastuserscore = current($userscores);
    return $lastuserscore->time;
}

function lstest_has_made_test($userid, $lstestid) {
    $styles = get_records("lstest_styles", "testsid", $lstestid);
    $onestyle = current($styles);
    if (count_records('lstest_user_scores', 'userid', $userid, 'stylesid', $onestyle->id) > 0) {
        return true;
    } else {
        return false;
    }
}

function lstest_student_scores($testid, $userid) {
    $styles = get_records("lstest_styles", "testsid", $testid, "id asc");
    $onestyle = current($styles);

    if (count_records("lstest_user_scores", "stylesid", "$onestyle->id", "userid", "$userid") > 0) {
        foreach ($styles as $style) {
            $scores = get_records_select("lstest_user_scores", "stylesid = '$style->id' AND userid = '$userid'", "time desc", "*", "0", "1");
            $score = current($scores);
            $stylescores[$score->stylesid] = $score->score;
        }
        return $stylescores;
    }
    else
    {
        return NULL;
    }
}

function lstest_course_scores($courseid, $testid) {
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    foreach ($styles as $style) {
        $resultscores[$style->id] = 0;
    }
    $studentsnum = 0;
    $students = get_course_students($courseid);
    if ($students) {
        foreach ($students as $student) {
            $studentscores = lstest_student_scores($testid, $student->id);
            if($studentscores != NULL) {
                foreach ($styles as $style) {
                    $resultscores[$style->id] += $studentscores[$style->id];
                }
                $studentsnum++;
            }
        }
    }
    if($studentsnum == 0) {
        return NULL;
    } else {
        foreach ($styles as $style) {
            $resultscores[$style->id] /= $studentsnum;
        }
        return $resultscores;
    }
}

function lstest_category_scores($category, $testid) {
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    foreach ($styles as $style) {
        $resultscores[$style->id] = 0;
    }
    $studentsnum = 0;
    $categorystudentids = lstest_category_students($testid, $category);
    foreach ($categorystudentids as $studentid) {
        $studentscores = lstest_student_scores($testid, $studentid);
        if($studentscores != NULL) {
            foreach ($styles as $style) {
                $resultscores[$style->id] += $studentscores[$style->id];
            }
            $studentsnum++;
        }
    }
    if($studentsnum == 0) {
        return NULL;
    } else {
        foreach ($styles as $style) {
            $resultscores[$style->id] /= $studentsnum;
        }
        return $resultscores;
    }
}



function lstest_all_users_scores($testid) {

    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    foreach ($styles as $style) {
        $resultscores[$style->id] = 0;
    }

    $usersnum = 0;

    $users = get_records("user");
    if ($users) {
        foreach ($users as $user) {
            $userscores = lstest_student_scores($testid, $user->id);
            if($userscores != NULL) {
                foreach ($styles as $style) {
                    $resultscores[$style->id] += $userscores[$style->id];
                }
                $usersnum++;
            }
        }
    }
    if($usersnum == 0) {
        return NULL;
    } else {
        foreach ($styles as $style) {
            $resultscores[$style->id] /= $usersnum;
        }
        return $resultscores;
    }
}



function lstest_print_scores($testid, $graph, $scores, $axissize, $minscore, $maxscore, $angleincrement, $colour) {

    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    $counter = 0;
    foreach ($styles as $style) {
        $stylestointegers[$counter++] = $style->id;
    }

    $angle = $angleincrement;

    for($i=0; $i<$counter; $i++) {

        $result = ($maxscore - $minscore)*abs($scores[$stylestointegers[$i]] - $minscore)*cos($angle);
        if($result != (float) 0) {
            $x1 = $axissize/($maxscore - $minscore)*abs($scores[$stylestointegers[$i]] - $minscore)*cos($angle);
            //$x1 = $axissize/$result;
        } else {
            $x1 = 1;
        }

        $result = ($maxscore - $minscore)*abs($scores[$stylestointegers[$i]] - $minscore)*sin($angle);
        if($result != (float) 0) {
            $y1 = $axissize/($maxscore - $minscore)*abs($scores[$stylestointegers[$i]] - $minscore)*sin($angle);
            //$y1 = $axissize/$result;
        } else {
            $y1 = 1;
        }

        $result = ($maxscore - $minscore)*abs($scores[$stylestointegers[($i+1)%$counter]] - $minscore)*cos($angle - $angleincrement);
        if($result != (float) 0) {
            $x2 = $axissize/($maxscore - $minscore)*abs($scores[$stylestointegers[($i+1)%$counter]] - $minscore)*cos($angle - $angleincrement);
            //$x2 = $axissize/$result;
        } else {
            $x2 = 0;
        }

        $result = ($maxscore - $minscore)*abs($scores[$stylestointegers[($i+1)%$counter]] - $minscore)*sin($angle - $angleincrement);
        if($result != (float) 0) {
            $y2 = $axissize/($maxscore - $minscore)*abs($scores[$stylestointegers[($i+1)%$counter]] - $minscore)*sin($angle - $angleincrement);
            //$y2 = $axissize/$result;
        } else {
            $y2 = 0;
        }
        $graph->line($graph->parameter['width']/2 + $x1, $graph->parameter['height']/2 + $y1, $graph->parameter['width']/2 + $x2, $graph->parameter['height']/2 + $y2,'brush', 'square', 0, $colour, 0);

        $angle -= $angleincrement;

    }
}


function lstest_create_message() {
    $message['points'] = 9;
    $message['angle'] = 0;
    $message['colour'] = 'black';
    $message['font'] = 'default.ttf';
    $message['boundary_box']['x'] = 0;
    $message['boundary_box']['y'] = 0;
    $message['boundary_box']['offsetX'] = 0;
    $message['boundary_box']['offsetY'] = 0;
    $message['boundary_box']['reference'] = 'left-top';
    $message['boundary_box']['height'] = 0;
    $message['boundary_box']['width'] = 0;
    return $message;
}

function lstest_init_graph_parameter() {
    $parameter['path_to_fonts'] = $CFG->dirroot."/lib/";      // path to fonts folder. don't forget *trailing* slash!!
                                           //   for WINDOZE this may need to be the full path, not relative.
    $parameter['title'] = ''; // text for graph title
    $parameter['inner_border'] = 'none';       // colour of border around actual graph, or 'none'.
    $parameter['x_axis_gridlines'] = 6;        // if set to a number then x axis is treated as numeric.
    $parameter['axis_colour'] = 'none';      // colour of axis text.
    $parameter['x_axis_angle'] = 0;           // rotation of axis text.
    $parameter['x_offset'] = 0;          // x axis tick offset from y axis as fraction of tick spacing.
    $parameter['y_ticks_colour'] = 'none';       // colour to draw y ticks, or 'none'
    $parameter['x_ticks_colour'] = 'none';       // colour to draw x ticks, or 'none'
    $parameter['y_grid'] = 'none';        // grid lines. set to 'line' or 'dash'...
    $parameter['x_grid'] = 'none';        //   or if set to 'none' print nothing.
    $parameter['legend'] = 'top-right';        // default. no legend.
                                          // otherwise: 'top-left', 'top-right', 'bottom-left', 'bottom-right',
                                          //   'outside-top', 'outside-bottom', 'outside-left', or 'outside-right'.
    $parameter['legend_colour'] = 'black';       // legend text colour.
    $parameter['legend_border'] = 'none';        // legend border colour, or 'none'.
    return $parameter;
}

function lstest_write_scores($testid, $graph, $scores, $minscore, $angleincrement, $spacebetweenticks, $separation="1", $colour="black") {
    $message = lstest_create_message();
    $angle = $angleincrement;
    $message['colour'] = $colour;
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    foreach ($styles as $style) {
        $xtickposition = $graph->parameter['width']/2 + ($scores[$style->id] - $minscore)*$spacebetweenticks*cos($angle);
        $ytickposition = $graph->parameter['height']/2 + ($scores[$style->id] - $minscore)*$spacebetweenticks*sin($angle);
        $message['boundary_box']['x'] = $xtickposition - 10 ;
        $message['boundary_box']['y'] = $graph->parameter['height'] - ($ytickposition - $separation*10);
        $message['text'] = $scores[$style->id];
        $graph->print_TTF($message);
        $angle -= $angleincrement;
    }
}


function lstest_write_names($testid, $graph, $axissize, $angleincrement) {
    $message = lstest_create_message();
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    $angle = $angleincrement;
    foreach ($styles as $style) {
        $xtickposition = $graph->parameter['width']/2 + $axissize*cos($angle);
        $ytickposition = $graph->parameter['height']/2 + $axissize*sin($angle);
        $realangle = abs($angle%(pi()*2));
        if ( (($realangle >= 0) && ($realangle <= pi()/2)) || (($realangle > pi()/2*3) && ($realangle < pi()*2)) ) {
            $message['boundary_box']['x'] = $xtickposition + 10*cos($angle);
            $message['boundary_box']['y'] = $graph->parameter['height'] - ($ytickposition + 10*sin($angle));
        }
        elseif (($realangle > pi()/2) && ($realangle <= pi()/2*3)) {
            $message['boundary_box']['x'] = $xtickposition + 10*cos($angle) - strlen($style->name)*5.2;
            $message['boundary_box']['y'] = $graph->parameter['height'] - ($ytickposition + 10*sin($angle));
        }
        $message['text'] = $style->name;
        $graph->print_TTF($message);
        $angle -= $angleincrement;
    }
}


function lstest_print_axis($testid, $graph, $axissize, $minscore, $maxscore, $angleincrement, $spacebetweenticks) {
    $ticksize = $axissize/60;
    $angle = $angleincrement;
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    $stylesnum = count($styles);
    foreach ($styles as $style) {
        $graph->line($graph->parameter['width']/2, $graph->parameter['height']/2, $graph->parameter['width']/2 + $axissize*cos($angle), $graph->parameter['height']/2 + $axissize*sin($angle), 'brush', 'square', 0, 'black', 0);
        //Dibujamos los ticks del eje
        $xtickposition = $graph->parameter['width']/2;
        $ytickposition = $graph->parameter['height']/2;
        if ($stylesnum == 2){
            $graph->line($xtickposition - $ticksize*cos($angle + pi()/2), $ytickposition - $ticksize*sin($angle + pi()/2), $xtickposition + $ticksize*cos($angle + pi()/2), $ytickposition + $ticksize*sin($angle + pi()/2), 'brush', 'square', 0, 'black', 0);
        }
        for($j=$minscore; $j<$maxscore; $j++) {
            $xtickposition += $spacebetweenticks*cos($angle);
            $ytickposition += $spacebetweenticks*sin($angle);
            $graph->line($xtickposition - $ticksize*cos($angle + pi()/2), $ytickposition - $ticksize*sin($angle + pi()/2), $xtickposition + $ticksize*cos($angle + pi()/2), $ytickposition + $ticksize*sin($angle + pi()/2), 'brush', 'square', 0, 'black', 0);
        }
        $angle -= $angleincrement;
    }
}


function lstest_print_legend(&$graph, $courseid, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $colour, $maxscore) {
    $graph->y_order = array();
    if ($actualstudent==true) {
        array_push($graph->y_order,'actualstudent');
        if ($courseid > 1) {
            $graph->y_format['actualstudent'] = array('colour' => $colour[0],'legend' => get_string("actualstudent", "lstest"));
        } else {
            $graph->y_format['actualstudent'] = array('colour' => $colour[0],'legend' => get_string("actualuser", "lstest"));
        }
        $graph->y_data['actualstudent'] = array();
    }
    if (($coursemedia==true) && ($courseid > 1))  {
        array_push($graph->y_order,'coursemedia');
        $graph->y_format['coursemedia'] = array('colour' => $colour[1],'legend' => get_string("coursemedia", "lstest"));
        $graph->y_data['coursemedia'] = array();
    }
    if (($categorymedia==true) && ($courseid > 1)) {
        array_push($graph->y_order,'categorymedia');
        $graph->y_format['categorymedia'] = array('colour' => $colour[2],'legend' => get_string("categorymedia", "lstest"));
        $graph->y_data['categorymedia'] = array();
    }
    if ($totalmedia==true) {
        array_push($graph->y_order,'totalmedia');
        $graph->y_format['totalmedia'] = array('colour' => $colour[3],'legend' => get_string("totalmedia", "lstest"));
        $graph->y_data['totalmedia'] = array();
    }
}

function lstest_print_items_table($testid, $courseid, $moduleid, $itemid="", $orderby="") {
    $resultanswers = lstest_course_item_answers($courseid , $testid);
    $items = get_records("lstest_items", "testsid", "$testid", "id asc");
    $firstitem = current($items);
    $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
    $answersnum = count($answers);
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    $tablestyles = array();
    foreach ($styles as $style) {
        $tablestyles[$style->id] = array();
    }
    $table->align = array("center", "center");
    $table->data[0] = array("", "");
    $table->headcolspan = array("1", "1", "$answersnum", "$answersnum", "$answersnum");
    if ($orderby == "styles") {
        $table->head = array(get_string("question","lstest")."<BR><a href=itemstadistic.php?id=$moduleid&item=$itemid&orderby=items><FONT SIZE=1>".get_string("orderbyitems","lstest")."</FONT></A>", get_string("style","lstest"));
    } else {
        $table->head = array(get_string("question","lstest")."<BR><a href=itemstadistic.php?id=$moduleid&item=$itemid&orderby=styles><FONT SIZE=1>".get_string("orderbystyles","lstest")."</FONT></A>", get_string("style","lstest"));
    }
    for($i=0; $i<3; $i++) {
        foreach ($answers as $answer) {
            array_push($table->align, "center");
            array_push($table->data[0], "<FONT size=1>$answer->name</FONT>");
        }
    }
    array_push($table->head, get_string("inthecourse", "lstest"));
    array_push($table->head, get_string("inthecategory", "lstest"));
    array_push($table->head, get_string("inmoodle", "lstest"));

    $resultanswers[0] = lstest_course_item_answers($courseid , $testid);
    $actualcourse = get_record("course", "id", "$courseid");
    $resultanswers[1] = lstest_category_item_answers($actualcourse->category , $testid);
    $resultanswers[2] = lstest_all_users_item_answers($testid);


    if ($orderby == "styles") {
        foreach ($items as $item) {
            $tablestyles[$item->stylesid][$item->id] = array();
            for ($i=0; $i<3; $i++) {
                foreach ($answers as $answer) {
                    array_push($tablestyles[$item->stylesid][$item->id], $resultanswers[$i][$item->id][$answer->id]);
                }
            }
        }
        foreach ($styles as $style) {
            foreach ($tablestyles[$style->id] as $key => $answers ) {
                $counter = $key - $firstitem->id + 1;
                $table->data[$counter] = array("<A HREF=itemstadistic.php?id=$moduleid&item=$key&orderby=$orderby>$counter.- ".$items[$key]->question."</A>");
                array_push($table->data[$counter], $style->name);
                foreach ($answers as $answer) {
                    array_push($table->data[$counter], $answer);
                }
            }
        }
    } else {
        $counter = 1;
        foreach ($items as $item) {
            $table->data[$item->id] = array("<A HREF=itemstadistic.php?id=$moduleid&item=$item->id&orderby=$orderby>$counter.- $item->question</A>");
            array_push($table->data[$item->id], $styles[$item->stylesid]->name);
            $counter++;
            for ($i=0; $i<3; $i++) {
                foreach ($answers as $answer) {
                    array_push($table->data[$item->id], $resultanswers[$i][$item->id][$answer->id]);
                }
            }
        }
    }
    lstest_print_table($table);
}

function lstest_student_item_answers($testid, $userid) {
    $onestyle = current(get_records("lstest_styles", "testsid", $testid, "id asc"));
    if (count_records("lstest_user_scores", "stylesid", $onestyle->id, "userid", $userid) > 0) {
        $items = get_records("lstest_items", "testsid", "$testid", "id asc");
        $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
        $answersnum = count($answers);
        foreach ($items as $item) {
            $useranswers = get_records_select("lstest_user_answers", "itemsid = '$item->id' AND userid = '$userid'", "time desc", "*", "0", "$answersnum");
            foreach ($useranswers as $useranswer) {
                if ( $useranswer->checked ) {
                    $itemanswers[$item->id][$useranswer->answersid] = "checked";
                } else {
                    $itemanswers[$item->id][$useranswer->answersid] = false;
                }
            }
        }
        return $itemanswers;
    }
    else
    {
        return NULL;
    }
}



function lstest_course_item_answers($courseid, $testid) {
    $items = get_records("lstest_items", "testsid", "$testid", "id asc");
    $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
    foreach ($items as $item) {
        foreach ($answers as $answer) {
            $resultanswers[$item->id][$answer->id] = 0;
        }
    }
    $students = get_course_students($courseid);
    if ($students) {
        foreach ($students as $student) {
            $studentscores = lstest_student_item_answers($testid, $student->id);
            if($studentscores != NULL) {
                foreach ($items as $item) {
                    foreach ($answers as $answer) {
                        if ($studentscores[$item->id][$answer->id] == "checked") {
                            $resultanswers[$item->id][$answer->id] += 1;
                        }
                    }
                }
            }
        }
    }
    return $resultanswers;
}


function lstest_category_item_answers($category , $testid) {

    $items = get_records("lstest_items", "testsid", "$testid", "id asc");
    $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
    foreach ($items as $item) {
        foreach ($answers as $answer) {
            $resultanswers[$item->id][$answer->id] = 0;
        }
    }

    $categorystudentids = array();

    $categorycourses = get_records("course", "category", "$category");
    if($categorycourses) {
        $categorystudentids = lstest_category_students($testid, $category);

        foreach ($categorystudentids as $studentid) {

            $studentscores = lstest_student_item_answers($testid, $studentid);
            if($studentscores != NULL) {

                foreach ($items as $item) {
                    foreach ($answers as $answer) {
                        if ($studentscores[$item->id][$answer->id] == "checked") {
                            $resultanswers[$item->id][$answer->id] += 1;
                        }
                    }
                }
            }
        }
    }
    return $resultanswers;
}



function lstest_all_users_item_answers($testid) {

    $items = get_records("lstest_items", "testsid", "$testid", "id asc");
    $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
    foreach ($items as $item) {
        foreach ($answers as $answer) {
            $resultanswers[$item->id][$answer->id] = 0;
        }
    }
    $users = get_records("user");
    foreach ($users as $user) {
        $studentscores = lstest_student_item_answers($testid, $user->id);
        if($studentscores != NULL) {
            foreach ($items as $item) {
                foreach ($answers as $answer) {
                    if ($studentscores[$item->id][$answer->id] == "checked") {
                        $resultanswers[$item->id][$answer->id] += 1;
                    }
                }
            }
        }
    }
    return $resultanswers;

}

function lstest_print_item_table($testid, $itemid, $courseid, $moduleid) {
    $students = get_course_students($courseid);
    $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
    $answersnum = count($answers);
    $table->align = array("center", "center", "center");
    $table->size = array("10", "10", "10");
    $table->head = array(get_string("student", "lstest"));
    foreach ($answers as $answer) {
        array_push($table->head, $answer->name);
    }
    if ($students) {
        $counter = 1;
        foreach ($students as $student) {
            $studentanswers = get_records_select("lstest_user_answers", "itemsid = '$itemid' AND userid = '$student->id'", "time desc", "*", "0", "$answersnum");
            if($studentanswers) {
                $user = get_record("user", "id", "$student->id");
                $table->data[$counter] = array("<A HREF=\"students.php?id=$moduleid&userid=$user->id\">".$user->firstname." ".$user->lastname."</A>");
                //$studentanswer = array_pop($studentanswers);
/*                $studentanswer = next($studentanswers);
                foreach ($answers as $answer) {
                    if ($answer->id == $studentanswer->answersid) {
                        array_push($table->data[$counter], get_string("yes"));
                    } else {
                        array_push($table->data[$counter], get_string("no"));
                    }
                }
                $counter++;
*/
                foreach ($studentanswers as $studentanswer) {
                    if ($studentanswer->checked) {
                        array_push($table->data[$counter], get_string("yes"));
                    } else {
                        array_push($table->data[$counter], get_string("no"));
                    }
                }
                $counter++;
            }
        }
    }
    lstest_print_table($table);

}

function lstest_course_students($testid, $courseid) {
    $usersids = array();
    $users = get_course_students($courseid);
    if ($users != NULL) {
        foreach ($users as $user) {
            if(lstest_student_scores($testid, $user->id) != NULL) {
                array_push($usersids, $user->id);
            }
        }
    }
    return $usersids;
}

function lstest_category_students($testid, $category) {
    $usersids = array();
    $categorycourses = get_records("course", "category", "$category");
    if ($categorycourses != NULL) {
        foreach ($categorycourses as $categorycourse) {
            $usersids = array_merge($usersids, lstest_course_students($testid, $categorycourse->id));
        }
        $usersids = array_unique($usersids);
    }
    return $usersids;
}



function lstest_all_students($testid) {
    $usersids = array();
    $users = get_records("user");
    if ($users != NULL) {
        foreach ($users as $user) {
            if(lstest_student_scores($testid, $user->id) != NULL) {
                array_push($usersids, $user->id);
            }
        }
    }
    return $usersids;
}

function lstest_media_scores($testid, $allstudentsids, $coursestudentsids, $categorystudentsids) {
    $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
    $resultscores = array();
    $resultscores['all'] = array();
    $resultscores['course'] = array();
    $resultscores['category'] = array();
    foreach ($styles as $style){
        $resultscores['course'][$style->id] = 0;
        $resultscores['category'][$style->id] = 0;
        $resultscores['all'][$style->id] = 0;
    }
    $coursestudentsnum = count($coursestudentsids);
    $categorystudentsnum = count($categorystudentsids);
    $allstudentsnum = count($allstudentsids);
    foreach ($allstudentsids as $studentid) {
        $studentscores = lstest_student_scores($testid, $studentid);
        foreach ($styles as $style) {
            $resultscores['all'][$style->id] += $studentscores[$style->id];
            if (in_array($studentid, $coursestudentsids)) {
                $resultscores['course'][$style->id] += $studentscores[$style->id];
            }
            if (in_array($studentid, $categorystudentsids)) {
                $resultscores['category'][$style->id] += $studentscores[$style->id];
            }
        }
    }
    foreach ($styles as $style) {
        if ($coursestudentsnum != 0) {
            $resultscores['course'][$style->id] = round($resultscores['course'][$style->id]/$coursestudentsnum,2);
        } else {
            $resultscores['course'][$style->id] = "";
        }
        if ($categorystudentsnum != 0){
            $resultscores['category'][$style->id] = round($resultscores['category'][$style->id]/$categorystudentsnum,2);
        } else {
            $resultscores['category'][$style->id] = "";
        }
        if ($allstudentsnum != 0) {
            $resultscores['all'][$style->id] = round($resultscores['all'][$style->id]/$allstudentsnum,2);
        } else {
            $resultscores['all'][$style->id] = "";
        }
    }
    return $resultscores;
}

//*********************************************************************************************************
//*********************************************************************************************************
//*****                             CONFIGURATION ZONE FUNCTIONS                                      *****
//*********************************************************************************************************
//*********************************************************************************************************

function lstest_get_test($testid) {
/*
Return general information related to a learning styles test
existing (if $testid contains a lstest id) or default
information (if $testid is empty).

@uses $CFG
@param int $testid lstest id or empty
@return array
*/
    global $CFG;

    if ( !empty($testid)) {
        $testrecord = get_record("lstest_tests", "id", $testid);
        $items = get_records("lstest_items", "testsid", "$testid", "id asc");
        $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
        $levels = get_records("lstest_levels", "testsid", "$testid", "id asc");
        $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");

        $test->id = $testid;
        $test->name = $testrecord->name;
        $test->lang = $testrecord->lang;
        $test->stylesnum = count($styles);
        $test->levelsnum = count($levels);
        $test->answersnum = count($answers);
        $test->itemsnum = count($items);
        $test->available = $testrecord->available;
        $test->redoallowed = $testrecord->redoallowed;
        $test->multipleanswer = $testrecord->multipleanswer;
        $test->notansweredquestion = $testrecord->notansweredquestion;
        $test->styledefined = $testrecord->styledefined;
    } else {
        $test->id = "";
        $test->name = "";
        $test->lang = $CFG->lang;
        $test->stylesnum = "4";
        $test->levelsnum = "5";
        $test->itemsnum = "80";
        $test->answersnum = "2";
        $test->available = "1";
        $test->redoallowed = "0";
        $test->multipleanswer = "0";
        $test->notansweredquestion = "0";
        $test->styledefined = "0";
    }
    return $test;
}

function lstest_get_test_submitted() {
    if (!empty($_POST)) {
        $data = (object)$_POST;

        $test->id = $data->id;
        $test->name = $data->name;
        $test->lang = $data->lang;
        $test->stylesnum = $data->stylesnum;
        $test->levelsnum = $data->levelsnum;
        $test->answersnum = $data->answersnum;
        $test->itemsnum = $data->itemsnum;
        $test->available = $data->available;
        $test->redoallowed = $data->redoallowed;
        $test->multipleanswer = $data->multipleanswer;
        $test->notansweredquestion = $data->notansweredquestion;
        $test->styledefined  = $data->styledefined;
        return $test;
    } else {
        return false;
    }
}

function lstest_submit_test($test) {
    echo "<input type='hidden' name='id' value='$test->id'>";
    echo "<input type='hidden' name='name' value='$test->name'>";
    echo "<input type='hidden' name='lang' value='$test->lang'>";
    echo "<input type='hidden' name='stylesnum' value='$test->stylesnum'>";
    echo "<input type='hidden' name='levelsnum' value='$test->levelsnum'>";
    echo "<input type='hidden' name='answersnum' value='$test->answersnum'>";
    echo "<input type='hidden' name='itemsnum' value='$test->itemsnum'>";
    echo "<input type='hidden' name='available' value='$test->available'>";
    echo "<input type='hidden' name='redoallowed' value='$test->redoallowed'>";
    echo "<input type='hidden' name='multipleanswer' value='$test->multipleanswer'>";
    echo "<input type='hidden' name='notansweredquestion' value='$test->notansweredquestion'>";
    echo "<input type='hidden' name='styledefined' value='$test->styledefined'>";
}

function lstest_get_styles($stylesnum, $testid) {
    if ( !empty($testid)) {
        $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($styles as $onestyle) {
            $stylenames[$i++] = $onestyle->name;
        }
        for ($i=1; $i<=$stylesnum; $i++) {
            $strstyle = "style".$i;
            $style->$strstyle = $stylenames[$i];
        }
    } else {
        for ($i=1; $i<=$stylesnum; $i++) {
            $strstyle = "style".$i;
            $style->$strstyle = "";
        }
    }
    return $style;
}



function lstest_submit_styles($stylesnum, $styles) {
    for ($i=1; $i<=$stylesnum; $i++) {
        $strstyle = "style".$i;
        echo "<input type='hidden' name='$strstyle' value='".$styles->$strstyle."'>";
    }
}



function lstest_get_styles_submitted($stylesnum) {
    if (!empty($_POST)) {
        $data = (object)$_POST;
        for ($i=1; $i<=$stylesnum; $i++) {
            $strstyle = "style".$i;
            $styles->$strstyle = $data->$strstyle;
        }
        return $styles;
    } else {
        return false;
    }
}



function lstest_get_levels($levelsnum, $testid) {
    if ( !empty($testid)) {
        $levels = get_records("lstest_levels", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($levels as $onelevel) {
            $levelnames[$i++] = $onelevel->name;
        }
        for ($i=1; $i<=$levelsnum; $i++) {
            $strlevel = "level".$i;
            $level->$strlevel = $levelnames[$i];
        }
    } else {
        for ($i=1; $i<=$levelsnum; $i++) {
            $strlevel = "level".$i;
            $level->$strlevel = "";
        }
    }
    return $level;
}



function lstest_submit_levels($levelsnum, $levels) {
    for ($i=1; $i<=$levelsnum; $i++) {
        $strlevel = "level".$i;
        echo "<input type='hidden' name='$strlevel' value='".$levels->$strlevel."'>";
    }
}



function lstest_get_levels_submitted($levelsnum) {
    if (!empty($_POST)) {
        $data = (object)$_POST;
        for ($i=1; $i<=$levelsnum; $i++) {
            $strlevel = "level".$i;
            $levels->$strlevel = $data->$strlevel;
        }
        return $levels;
    } else {
        return false;
    }
}



function lstest_get_thresholds($stylesnum, $levelsnum, $testid) {
    if ( !empty($testid)) {
        $styles = get_records("lstest_styles", "testsid", "$testid", "id asc");
        $levels = get_records("lstest_levels", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($styles as $onestyle) {
            $styleids[$i++] = $onestyle->id;
        }
        $i = 1;
        foreach ($levels as $onelevel) {
            $levelids[$i++] = $onelevel->id;
        }
        for ($i=1; $i<=$stylesnum; $i++) {
            for ($j=1; $j<=$levelsnum; $j++) {
                $strinfthreshold = "infthreshold".$i.$j;
                $strsupthreshold = "supthreshold".$i.$j;
                $threshold = get_record("lstest_thresholds", "stylesid", $styleids[$i], "levelsid", $levelids[$j]);
                $thresholds->$strinfthreshold = $threshold->infthreshold;
                $thresholds->$strsupthreshold = $threshold->supthreshold;
            }
        }
    } else {
        for ($i=1; $i<=$stylesnum; $i++) {
            for ($j=1; $j<=$levelsnum; $j++) {
                $strinfthreshold = "infthreshold".$i.$j;
                $strsupthreshold = "supthreshold".$i.$j;
                $thresholds->$strinfthreshold = 1;
                $thresholds->$strsupthreshold = 1;
            }
        }
    }
    return $thresholds;
}



function lstest_submit_thresholds($stylesnum, $levelsnum, $thresholds) {
    for ($i=1; $i<=$stylesnum; $i++) {
        for ($j=1; $j<=$levelsnum; $j++) {
            $strinfthreshold = "infthreshold".$i.$j;
            $strsupthreshold = "supthreshold".$i.$j;
            echo "<input type='hidden' name='$strinfthreshold' value='".$thresholds->$strinfthreshold."'>";
            echo "<input type='hidden' name='$strsupthreshold' value='".$thresholds->$strsupthreshold."'>";
        }
    }
}



function lstest_get_thresholds_submitted($stylesnum, $levelsnum) {
    if (!empty($_POST)) {
        $data = (object)$_POST;
        for ($i=1; $i<=$stylesnum; $i++) {
            for ($j=1; $j<=$levelsnum; $j++) {
                $strinfthreshold = "infthreshold".$i.$j;
                $strsupthreshold = "supthreshold".$i.$j;
                $thresholds->$strinfthreshold = $data->$strinfthreshold;
                $thresholds->$strsupthreshold = $data->$strsupthreshold;
            }
        }
        return $thresholds;
    } else {
        return false;
    }
}



function lstest_get_answers($answersnum, $testid) {

    if ( !empty($testid)) {
        $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($answers as $oneanswer) {
            $answernames[$i++] = $oneanswer->name;
        }
        for ($i=1; $i<=$answersnum; $i++) {
            $stranswer = "answer".$i;
            $answer->$stranswer = $answernames[$i];
        }
    } else {
        for ($i=1; $i<=$answersnum; $i++) {
            $stranswer = "answer".$i;
            $answer->$stranswer = "";
        }
    }
    return $answer;
}



function lstest_submit_answers($answersnum, $answers) {
    for ($i=1; $i<=$answersnum; $i++) {
        $stranswer = "answer".$i;
        echo "<input type='hidden' name='$stranswer' value='".$answers->$stranswer."'>";
    }
}



function lstest_get_answers_submitted($answersnum) {
    if (!empty($_POST)) {
        $data = (object)$_POST;
        for ($i=1; $i<=$answersnum; $i++) {
            $stranswer = "answer".$i;
            $answers->$stranswer = $data->$stranswer;
        }
        return $answers;
    } else {
        return false;
    }
}



function lstest_get_items($itemsnum, $answersnum, $testid, $styledefined) {
    if ( !empty($testid)) {
        $items = get_records("lstest_items", "testsid", "$testid", "id asc");
        $answers = get_records("lstest_answers", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($items as $oneitem) {
            $itemids[$i] = $oneitem->id;
            $itemquestions[$i] = $oneitem->question;
            $itemstylesids[$i++] = $oneitem->stylesid;
        }
        $i = 1;
        foreach ($answers as $oneanswer) {
            $answerids[$i++] = $oneanswer->id;
        }
        for ($i=1; $i<=$itemsnum; $i++) {
            $strquestion = "question".$i;
			if($styledefined == 0) {
            	$strstylesid = "stylesid".$i;
				$item->$strstylesid = $itemstylesids[$i];
			}
            $item->$strquestion = $itemquestions[$i];
            for ($j=1; $j<=$answersnum; $j++) {
                $strnocheckedscore = "nocheckedscore".$i.$j;
                $strcheckedscore = "checkedscore".$i.$j;
                $score = get_record("lstest_scores", "itemsid", $itemids[$i], "answersid", $answerids[$j]);
                $item->$strnocheckedscore = $score->nocheckedscore;
                $item->$strcheckedscore = $score->checkedscore;
				if($styledefined == 1) {
					$strstylesid = "stylesid".$i.$j;
					$item->$strstylesid = $score->stylesid;
				}
            }
        }
    } else {
        for ($i=1; $i<=$itemsnum; $i++) {
            $strquestion = "question".$i;
			if($styledefined == 0) {
            	$strstylesid = "stylesid".$i;
				$item->$strstylesid = "1";
			}
            $item->$strquestion = "";
            
            for ($j=1; $j<=$answersnum; $j++) {
                $strnocheckedscore = "nocheckedscore".$i.$j;
                $strcheckedscore = "checkedscore".$i.$j;
                $item->$strnocheckedscore = 0;
                $item->$strcheckedscore = 0;
				if($styledefined == 1) {
					$strstylesid = "stylesid".$i.$j;
					$item->$strstylesid = "1";
				}
            }
        }
    }
    return $item;
}



function lstest_submit_items($itemsnum, $answersnum, $items, $styledefined) {
    for ($i=1; $i<=$itemsnum; $i++) {
		if($styledefined == 0){
        	$strstylesid = "stylesid".$i;
			echo "<input type='hidden' name='$strstylesid' value='".$items->$strstylesid."'>";
        }
        $strquestion = "question".$i;
        echo "<input type='hidden' name='$strquestion' value='".$items->$strquestion."'>";

        for ($j=1; $j<=$answersnum; $j++) {
            $strnocheckedscore = "nocheckedscore".$i.$j;
            $strcheckedscore = "checkedscore".$i.$j;
            echo "<input type='hidden' name='$strnocheckedscore' value='".$items->$strnocheckedscore."'>";
            echo "<input type='hidden' name='$strcheckedscore' value='".$items->$strcheckedscore."'>";
			if($styledefined == 1){
				$strstylesid = "stylesid".$i.$j;
				echo "<input type='hidden' name='$strstylesid' value='".$items->$strstylesid."'>";
			}
        }
    }
}



function lstest_get_items_submitted($itemsnum, $answersnum, $styledefined) {
    if (!empty($_POST)) {
        $data = (object)$_POST;
        for ($i=1; $i<=$itemsnum; $i++) {
			if($styledefined == 0){
            	$strstylesid = "stylesid".$i;
	            $items->$strstylesid = $data->$strstylesid;
			}
            $strquestion = "question".$i;
            $items->$strquestion = $data->$strquestion;
            for ($j=1; $j<=$answersnum; $j++) {
                $strnocheckedscore = "nocheckedscore".$i.$j;
                $strcheckedscore = "checkedscore".$i.$j;
                $items->$strnocheckedscore = $data->$strnocheckedscore;
                $items->$strcheckedscore = $data->$strcheckedscore;
				if($styledefined == 1){
					$strstylesid = "stylesid".$i.$j;
					$items->$strstylesid = $data->$strstylesid;
				}
            }
        }
        return $items;
    } else {
        return false;
    }
}



function lstest_predominance_tables($testid, $courseid) {
    $styles =  get_records("lstest_styles", "testsid", $testid, "id asc");
    foreach ($styles as $style) {
        $usernumbers[$style->id] = 0;
    }
    $usernumbers['total'] = 0;
    $userids = lstest_course_students($testid, $courseid);
    $table->align = array("center");
    foreach ($styles as $style) {
        $table->data = array();
        print_heading(get_string('forstylepredominance', "lstest", $style->name));
        foreach ($userids as $userid) {
            $userstyles = lstest_user_predominance($testid, $userid);
            if (in_array($style->id, $userstyles)) {
                $user = get_record("user", "id", $userid);
                array_push($table->data, array($user->firstname." ".$user->lastname));
                $usernumbers[$style->id]++;
                $usernumbers['total']++;
            }
        }
        lstest_print_table($table);
        echo "<br>";
    }
    echo "<br>";
    print_heading_with_help(get_string("predominance", "lstest"), "predominancetable", "lstest");
    if ($users = get_course_students($courseid)) {
        $studentsnumber = count($users);
    } else {
        $studentsnumber = 0;
    }
    $studentswithtestnumber = count($userids);
    $table->align = array("center", "center");
    $table->head = array(get_string("studentsinthecourse", "lstest"), get_string("havemadethetest", "lstest"));
    $tabledata = array($studentsnumber, $studentswithtestnumber." (".($studentsnumber != 0 ? round(count($userids)/$studentsnumber*100) : 0)."%)");
    foreach ($styles as $style) {
        array_push($tabledata, $usernumbers[$style->id]." (".($studentswithtestnumber != 0 ? round($usernumbers[$style->id]/$studentswithtestnumber*100) : 0)."%)");
        array_push($table->head, $style->name);
        array_push($table->align, "center");
    }
    $table->data = array($tabledata);
    lstest_print_table($table);
}

/// START LSTEST CONDITIONAL
/*
function lstest_user_predominance($testid, $userid) {

    $styles =  get_records("lstest_styles", "testsid", $testid, "id asc");
    foreach ($styles as $style) {
        $userlevels[$style->id] = 0;
    }
    $levels =  get_records("lstest_levels", "testsid", $testid, "id asc");
    $counter = 1;
    foreach ($levels as $level) {
        $leveltoorder[$level->id] = $counter++;
    }
    foreach ($styles as $style) {
        $userscores = get_records_select("lstest_user_scores", "stylesid = '$style->id' AND userid = '$userid'", "time desc", "*", "0", "1");
        $userscore = current($userscores);
        $userlevels[$style->id] = $leveltoorder[$userscore->levelsid];
    }
    $result = array();
    foreach ($styles as $style) {
        $ispredominant = true;
        foreach ($userlevels as $userlevel) {
            $ispredominant = ($userlevels[$style->id] >= $userlevel);
            if (!$ispredominant) {
                break;
            }
        }
        if ($ispredominant) {
            array_push($result, $style->id);
        }
    }
    return $result;
}
*/



function lstest_user_predominance($testid, $userid) {
    $styles = get_records("lstest_styles", "testsid", $testid, "id asc");
    $levels = get_records("lstest_levels", "testsid", $testid, "id asc");
    $userlevels = array();
    foreach ($styles as $style) {
        if ($userscores = get_records_select("lstest_user_scores", "stylesid = '$style->id' AND userid = '$userid'", "time desc", "*", "0", "1")) {
            $userscore = current($userscores);
            $userlevels[$style->id] = $userscore->levelsid;
        }
    }
    if (!empty($userlevels)) {
        $result = array();
        foreach ($styles as $style) {
            $ispredominant = true;
            foreach ($userlevels as $userlevel) {
                $ispredominant = ($userlevels[$style->id] >= $userlevel);
                if (!$ispredominant) {
                    break;
                }
            }
            if ($ispredominant) {
                array_push($result, $style->id);
            }
        }
    } else {
        $result = false;
    }
    return $result;
}

/// END LSTEST CONDITIONAL


//*********************************************************************************************************
//*********************************************************************************************************
//*****                                 FOR MOODLE FUNCTIONS                                          *****
//*********************************************************************************************************
//*********************************************************************************************************



function lstest_add_instance($test) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.
    $test->timecreated = time();
    $test->timemodified = $test->timecreated;
    return insert_record("lstest", $test);
}



function lstest_update_instance($test) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.
    $test->timemodified = time();
    $test->id = $test->instance;
    return update_record("lstest", $test);
}



function lstest_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.
    if (! $test = get_record("lstest", "id", "$id")) {
        return false;
    }
    $result = true;
    if (! delete_records("lstest", "id", "$test->id")) {
        $result = false;
    }
    return $result;
}



function lstest_user_outline($course, $user, $mod, $test) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    return NULL;
}



function lstest_user_complete($course, $user, $mod, $test) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.
    return true;
}



function lstest_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in styles activities and print it out.
/// Return true if there was output, or false is there was none.
    global $CFG;
    return false;  //  True if anything was printed, otherwise false
}



function lstest_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...
    global $CFG;
    return true;
}



/*function lstest_grades($testid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.
    $return->grades = NULL;
    $return->maxgrade = NULL;
    return $return;
}*/



//*********************************************************************************************************
//*********************************************************************************************************
//*****                                      OTHER FUNCTIONS                                          *****
//*********************************************************************************************************
//*********************************************************************************************************



function lstest_print_table($table) {
    global $THEME;
    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = " align=\"$aa\"";
            } else {
                $align[$key] = "";
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = " width=\"$ss\"";
            } else {
                $size[$key] = "";
            }
        }
    }
    if (isset($table->headcolspan)) {
        foreach ($table->headcolspan as $key => $cc) {
            if ($cc) {
                $headcolspan[$key] = " colspan=\"$cc\"";
            } else {
                $headcolspan[$key] = "";
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = " nowrap=\"nowrap\" ";
            } else {
                $wrap[$key] = "";
            }
        }
    }
    if (empty($table->width)) {
        $table->width = "80%";
    }
    if (empty($table->cellpadding)) {
        $table->cellpadding = "5";
    }
    if (empty($table->cellspacing)) {
        $table->cellspacing = "1";
    }
    print_simple_box_start("center", "$table->width", "#ffffff", 0);
    echo "<table width=\"100%\" border=\"1\" valign=\"top\" align=\"center\" ";
//    echo "<table width=\"$table->width\" border=\"0\" valign=\"top\" align=\"center\" ";
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"generaltable\">\n";
    $countcols = 0;
    if (!empty($table->head)) {
        $countcols = count($table->head);
        echo "<tr>";
        foreach ($table->head as $key => $heading) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            }
            if (!isset($align[$key])) {
                $align[$key] = "";
            }
            if (!isset($headcolspan[$key])) {
                $headcolspan[$key] = "";
            }
            //echo "<th valign=\"middle\" ".$align[$key].$size[$key].$headcolspan[$key]." nowrap=\"nowrap\" class=\"generaltableheader\">$heading</th>";
            echo "<th valign=\"middle\" ".$align[$key].$size[$key].$headcolspan[$key]." class=\"generaltableheader\">$heading</th>";
        }
        echo "</tr>\n";
    }
    if (!empty($table->data)) {
        $countcols = count($table->align);
        foreach ($table->data as $row) {
            echo "<tr valign=\"middle\">";
            if ($row == "hr" and $countcols) {
                echo "<td colspan=\"$countcols\"><div class=\"tabledivider\"></div></td>";
            } else {  /// it's a normal row of data
                foreach ($row as $key => $item) {
                    if (!isset($size[$key])) {
                        $size[$key] = "";
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = "";
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = "";
                    }
                    echo "<td ".$align[$key].$size[$key].$wrap[$key]." class=\"generaltablecell\">$item</td>";
                }
            }
            echo "</tr>\n";
        }
    }
    echo "</table>\n";
    print_simple_box_end();
    return true;
}



?>
