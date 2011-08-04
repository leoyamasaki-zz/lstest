<?PHP

    require_once('../../config.php');
    require_once('lib.php');

// Make sure this is a legitimate posting

    if (isguest()) {
        error("Guests are not allowed to answer learning styles test", $_SERVER["HTTP_REFERER"]);
    }

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID
    
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $lstest = get_record("lstest", "id", $cm->instance)) {
        error("Learning styles test ID was incorrect");
    }

    if (!$formdata = data_submitted()) {
        error("You are not supposed to use this script like that.");
    }

    $timenow = time();

    $test = get_record("lstest_tests", "id", "$lstest->testsid");
    $styles = get_records("lstest_styles", "testsid", "$test->id", "id asc");
    //$stylesnum = count($styles);
    $items = get_records("lstest_items", "testsid", "$test->id", "id asc");
    //$firstitem = current($items);
    $answers = get_records("lstest_answers", "testsid", "$test->id", "id asc");

    foreach ($styles as $style) {
        $result["$style->id"] = 0;
    }
        
    if ($test->multipleanswer) { //respuesta multiple
        foreach ($items as $item) {
            $stritemid = "itemid".$item->id;
            foreach ($answers as $answer) {
                $stranswer = "answer".$item->id.$answer->id;
                $score = get_record("lstest_scores", "itemsid", $_POST["$stritemid"], "answersid", $answer->id);

                if ( !isset($_POST["$stranswer"]) ) {
                    $_POST["$stranswer"] = 0;
                }
                                    
                if ($_POST["$stranswer"]) {
                    $result["$item->stylesid"] += $score->checkedscore;
                } else {
                    $result["$item->stylesid"] += $score->nocheckedscore;
                }
                                
                $newdata2->time = $timenow;
                $newdata2->userid = $USER->id;
                $newdata2->itemsid = $_POST["$stritemid"];
                $newdata2->answersid = $answer->id;
                $newdata2->checked = ( ($_POST["$stranswer"]) ? true : false );
                insert_record("lstest_user_answers", $newdata2);
            }
        }
    } else {
        foreach ($items as $item) {
            $stranswer = "answer".$item->id;
            $stritemid = "itemid".$item->id;
            $scores = get_records("lstest_scores", "itemsid", $_POST["$stritemid"], "id asc");

            if ( !isset($_POST["$stranswer"]) ) {
                $_POST["$stranswer"] = 0;
            }
            
            foreach($scores as $score) {

                if ($_POST["$stranswer"] == $score->answersid) {
                    $result["$item->stylesid"] += $score->checkedscore;
                }
                else
                {
                    $result["$item->stylesid"] += $score->nocheckedscore;
                }
            }
            
            foreach ($answers as $answer) {
                $newdata2->time = $timenow;
                $newdata2->userid = $USER->id;
                $newdata2->itemsid = $_POST["$stritemid"];
                $newdata2->answersid = $answer->id;
                $newdata2->checked = ( ($answer->id==$_POST["$stranswer"]) ? true : false );
                insert_record("lstest_user_answers", $newdata2);
            }    
        }
    }    
        
    
    
    foreach ($styles as $style) {

        $thresholds = get_records("lstest_thresholds", "stylesid", "$style->id", "id asc");
        
        foreach ($thresholds as $threshold) {
            if(($result["$style->id"]>=$threshold->infthreshold)&&
                ($result["$style->id"]<=$threshold->supthreshold)) {
                $newdata->levelsid = $threshold->levelsid;
            }
        }

        $newdata->time = $timenow;
        $newdata->userid = $USER->id;
        $newdata->stylesid = $style->id;
        $newdata->score = $result["$style->id"];

        insert_record("lstest_user_scores", $newdata);

    }
    
    redirect($_SERVER["HTTP_REFERER"], get_string("changessaved"), 1);
    exit;

?>
