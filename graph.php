<?php

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/graphlib.php");

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID
    $sid = optional_param('sid', 0, PARAM_INT);        // Course Module ID
    $actualstudent = optional_param('actualstudent', false, PARAM_BOOL);
    $coursemedia = optional_param('coursemedia', false, PARAM_BOOL);
    $categorymedia = optional_param('categorymedia', false, PARAM_BOOL);
    $totalmedia = optional_param('totalmedia', false, PARAM_BOOL);
    $zoom = optional_param('zoom', false, PARAM_BOOL);
    $writemedia = optional_param('writemedia', false, PARAM_BOOL);

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
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
       
    $graph = new graph(650,550);
    $graph->x_data = array();
    $graph->y_data = array();
    $graph->y_tick_labels = array();
    $graph->parameter['x_offset'] = 0;
    $graph->parameter['path_to_fonts'] = $CFG->dirroot."/lib/";
    $graph->parameter['lang_decode'] = '';
    $graph->parameter['lang_transcode'] = strtolower(get_string('thischarset'));
    $graph->parameter['legend'] = 'top-right';
    $graph->parameter['legend_colour'] = 'black';       // legend text colour.
    $graph->parameter['legend_border'] = 'none';        // legend border colour, or 'none'.
    $graph->parameter['title'] = '';                    // text for graph title
    $graph->parameter['inner_border'] = 'none';       // colour of border around actual graph, or 'none'.
    $graph->parameter['y_ticks_colour'] = 'none';       // colour to draw y ticks, or 'none'
    $graph->parameter['x_ticks_colour'] = 'none';       // colour to draw x ticks, or 'none'
    $graph->parameter['y_grid'] = 'none';        // grid lines. set to 'line' or 'dash'...
    $graph->parameter['x_grid'] = 'none';        //   or if set to 'none' print nothing.
    $graph->parameter['axis_colour'] = 'none';      // colour of axis text.
    $graph->parameter['y_axis_gridlines']= 0;
    $graph->parameter['x_axis_gridlines']= 0;
    
    
    $colour[0] = 'red';
    $colour[1] = 'blue';
    $colour[2] = 'lime';
    $colour[3] = 'fuchsia';
    
    
    $lstestid = $lstest->testsid;
    $styles = get_records("lstest_styles", "testsid", "$lstestid", "id asc");
    $stylesnum = count($styles);
    $angleincrement = 2*pi()/$stylesnum;
    if ($zoom == true) {
        $axissize = ( $graph->parameter['width'] < $graph->parameter['height'] ? $graph->parameter['width']/2 : $graph->parameter['height']/2 ) - 3;
    } else {
        $axissize = ( $graph->parameter['width'] < $graph->parameter['height'] ? $graph->parameter['width']/2 : $graph->parameter['height']/2 ) - 100;
    }
    $maxscore = -9000;
    $minscore = 9000;
    foreach ($styles as $style) {
        $thresholds = get_records("lstest_thresholds", "stylesid", "$style->id");
        foreach ($thresholds as $threshold) {
            if ($maxscore < $threshold->supthreshold) {
                $maxscore = $threshold->supthreshold;
            }
            if ($minscore > $threshold->infthreshold) {
                $minscore = $threshold->infthreshold;
            }
        }
    }
    
    $spacebetweenticks = $axissize / ($maxscore - $minscore);

    lstest_print_axis($lstestid, $graph, $axissize, $minscore, $maxscore, $angleincrement, $spacebetweenticks);
    
    lstest_write_names($lstestid, $graph, $axissize, $angleincrement);

    $coursestudentsids = lstest_course_students($lstestid, $course->id);
    $categorystudentsids = lstest_category_students($lstestid, $course->category);
    $allstudentsids = lstest_all_students($lstestid);
    $scores = lstest_media_scores($lstestid, $allstudentsids, $coursestudentsids, $categorystudentsids);
    
    if ($totalmedia==true) {
        lstest_print_scores($lstestid, $graph, $scores['all'], $axissize, $minscore, $maxscore, $angleincrement, $colour[3]);
        if ($writemedia==true) {
            lstest_write_scores($lstestid, $graph, $scores['all'], $minscore, $angleincrement, $spacebetweenticks, "4", $colour[3]);
        }
    }
    
    if ( ($cm->course > 1) && ($categorymedia==true) && (count($categorystudentsids) > 0) ) {
        lstest_print_scores($lstestid, $graph, $scores['category'], $axissize, $minscore, $maxscore, $angleincrement, $colour[2]);
        if ($writemedia==true) {
            lstest_write_scores($lstestid, $graph, $scores['category'], $minscore, $angleincrement, $spacebetweenticks, "3", $colour[2]);
        }
    }
        
    if ( ($cm->course > 1) && ($coursemedia==true) && (count($coursestudentsids) > 0) ) {
        lstest_print_scores($lstestid, $graph, $scores['course'], $axissize, $minscore, $maxscore, $angleincrement, $colour[1]);
        if ($writemedia==true) {
            lstest_write_scores($lstestid, $graph, $scores['course'], $minscore, $angleincrement, $spacebetweenticks, "2", $colour[1]);
        }
    }
                    
    if ($actualstudent == true) {
        $stylescores = lstest_student_scores($lstestid, $sid);
        lstest_print_scores($lstestid, $graph, $stylescores, $axissize, $minscore, $maxscore, $angleincrement, $colour[0]);
        if ($writemedia==true) {
            lstest_write_scores($lstestid, $graph, $stylescores, $minscore, $angleincrement, $spacebetweenticks, "1", $colour[0]);
        } else {
            lstest_write_scores($lstestid, $graph, $stylescores, $minscore, $angleincrement, $spacebetweenticks, "1", "black");        
        }
    }
        
    lstest_print_legend($graph, $cm->course, $actualstudent, $coursemedia, $categorymedia, $totalmedia, $colour, $maxscore);

    $graph->draw();
    
?>
