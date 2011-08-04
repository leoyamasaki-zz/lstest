<?php

    require_once("../../config.php");
    require_once("lib.php");

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
        
    $test = lstest_get_test_submitted();
	$styledefined = $test->styledefined;
    $styles = lstest_get_styles_submitted($test->stylesnum);
    $levels = lstest_get_levels_submitted($test->levelsnum);
    $thresholds = lstest_get_thresholds_submitted($test->stylesnum, $test->levelsnum);
    $answers = lstest_get_answers_submitted($test->answersnum);
    $items = lstest_get_items_submitted($test->itemsnum, $test->answersnum, $styledefined);
    
    if ( (!empty($update)) && (count_records("lstest_tests", "name", "$test->name", "lang", "$test->lang") == 0)) {
        $add = $update;
        $onestyles = get_records("lstest_styles", "testsid", $test->id, "id asc");
        unset($test->id);
        unset($update);
        $i=1;
        foreach ($onestyles as $onestyle) {
            $oldstyles[$onestyle->id] = $i++;
        }
		if($styledefined == 0){
        	for($i=1; $i<=$test->itemsnum; $i++)
        	{
            	$strstylesid = "stylesid".$i;
            	$items->$strstylesid = $oldstyles[$items->$strstylesid];
        	}
		}
		else if($styledefined == 1) {
			//REVISAR SI HAY QUE CAMBIAR
		}
    }
    
    // Options
    if (!empty($_GET['show'])) { // Make a test to be available
        set_field("lstest_tests", "available", "1", "id", $_GET['show']);
    } else if (!empty($_GET['hide'])) { // Make a test to be unavailable
        set_field("lstest_tests", "available", "0", "id", $_GET['hide']);
    } else if (!empty($_GET['delete'])) { // Delete all information related to a test

        $delete = $_GET['delete'];
    
        if ( (!isset($_GET['confirm'])) || ($_GET['confirm'] != "yes") ) {
            $stradmin = get_string("administration");
            $strconfiguration = get_string("configuration");
            $strmanagemodules = get_string("managemodules");
            $strmodulename = get_string("modulename", "lstest");

            print_header("$site->shortname: $strmodulename: $strconfiguration", $site->fullname,
                  "<a href=\"$CFG->wwwroot/admin/index.php\">$stradmin</a> -> ".
                  "<a href=\"$CFG->wwwroot/admin/configure.php\">$strconfiguration</a> -> ".
                  "<a href=\"$CFG->wwwroot/admin/modules.php\">$strmanagemodules</a> -> $strmodulename");

            echo "<br><center>";
            notice_yesno(get_string("deleteconfirm", "lstest", get_field("lstest_tests", "name", "id", $delete)), "$CFG->wwwroot/mod/lstest/change.php?delete=$delete&confirm=yes&sesskey=$USER->sesskey", "$CFG->wwwroot/admin/module.php?module=lstest");
            echo "</center><br>";
            print_footer();
            exit;
        }else{
            delete_records("lstest_tests", "id", $delete);
            delete_records("lstest_styles", "testsid", $delete);
            delete_records("lstest_levels", "testsid", $delete);
            delete_records("lstest_items", "testsid", $delete);
            delete_records("lstest_thresholds", "testsid", $delete);
            delete_records("lstest_answers", "testsid", $delete);
            delete_records("lstest_scores", "testsid", $delete);
        }
    } else if (!empty($_GET['update'])) { // Update all information related to a test

        update_record("lstest_tests", $test);

        $onestyles = get_records("lstest_styles", "testsid", "$test->id", "id asc");
        $i = 1;
        foreach ($onestyles as $onestyle) {
            $styleids[$i++] = $onestyle->id;
        }
        for($i=1; $i<=$test->stylesnum; $i++)
        {
            $strstyle = "style".$i;
            $tablestyle->id = $styleids[$i];
            $tablestyle->name = $styles->$strstyle;
            $tablestyle->testsid = $test->id;
            update_record("lstest_styles", $tablestyle);
        }

        $onelevels = get_records("lstest_levels", "testsid", "$test->id", "id asc");
        $i = 1;
        foreach ($onelevels as $onelevel) {
            $levelids[$i++] = $onelevel->id;
        }
        for($i=1; $i<=$test->levelsnum; $i++)
        {
            $strlevel = "level".$i;
            $tablelevel->id = $levelids[$i];
            $tablelevel->name = $levels->$strlevel;
            $tablelevel->testsid = $test->id;
            update_record("lstest_levels", $tablelevel);
        }

        for($i=1; $i<=$test->stylesnum; $i++) {
            for($j=1; $j<=$test->levelsnum; $j++) {
                $strinfthreshold = "infthreshold".$i.$j;
                $strsupthreshold = "supthreshold".$i.$j;
                $threshold = get_record("lstest_thresholds", "stylesid", $styleids[$i], "levelsid", $levelids[$j]);
                $tablethreshold->id = $threshold->id;
                $tablethreshold->stylesid = $styleids[$i];
                $tablethreshold->levelsid = $levelids[$j];
                $tablethreshold->infthreshold = $thresholds->$strinfthreshold;
                $tablethreshold->supthreshold = $thresholds->$strsupthreshold;
                update_record("lstest_thresholds", $tablethreshold);
            }
        }

        $oneanswers = get_records("lstest_answers", "testsid", "$test->id", "id asc");
        $i = 1;
        foreach ($oneanswers as $oneanswer) {
            $answerids[$i++] = $oneanswer->id;
        }
        for($i=1; $i<=$test->answersnum; $i++)
        {
            $stranswer = "answer".$i;
            $answerid = "answerid".$i;
            $tableanswer->id = $answerids[$i];
            $tableanswer->name = $answers->$stranswer;
            $tableanswer->testsid = $test->id;
            update_record("lstest_answers", $tableanswer);
        }

        $oneitems = get_records("lstest_items", "testsid", "$test->id", "id asc");
        $i = 1;
        foreach ($oneitems as $oneitem) {
            $itemids[$i++] = $oneitem->id;
        }

        for($i=1; $i<=$test->itemsnum; $i++)
        {
            $strquestion = "question".$i;
            $tableitem->id = $itemids[$i];
            $tableitem->testsid = $test->id;
            $tableitem->question = $items->$strquestion;
			if($styledefined == 0){
            	$strstylesid = "stylesid".$i;
				$tableitem->stylesid = $items->$strstylesid;
			}
            update_record("lstest_items", $tableitem);
            for ($j=1; $j<=$test->answersnum; $j++) {
                $strnocheckedscore = "nocheckedscore".$i.$j;
                $strcheckedscore = "checkedscore".$i.$j;
                $score = get_record("lstest_scores", "itemsid", $itemids[$i], "answersid", $answerids[$j]);
                $tablescore->id = $score->id;
                $tablescore->itemsid = $itemids[$i];
                $tablescore->answersid = $answerids[$j];
                $tablescore->nocheckedscore = $items->$strnocheckedscore;
                $tablescore->checkedscore = $items->$strcheckedscore;
				if($styledefined == 1){
					$strstylesid = "stylesid".$i.$j;
					$tablescore->stylesid = $items->$strstylesid;
				}
                update_record("lstest_scores", $tablescore);
            }
        }
    } else if (!empty($_GET['add'])) { // Add to the DB all information related to a new test

        $testid = insert_record("lstest_tests", $test);

        for($i=1; $i<=$test->stylesnum; $i++)
        {
            $strstyle = "style".$i;
            $tablestyle->name = $styles->$strstyle;
            $tablestyle->testsid = $testid;
            insert_record("lstest_styles", $tablestyle);
        }

        for($i=1; $i<=$test->levelsnum; $i++)
        {
            $strlevel = "level".$i;
            $tablelevel->name = $levels->$strlevel;
            $tablelevel->testsid = $testid;
            insert_record("lstest_levels", $tablelevel);
        }

        $onestyles = get_records("lstest_styles", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($onestyles as $onestyle) {
            $styleids[$i++] = $onestyle->id;
        }
        $onelevels = get_records("lstest_levels", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($onelevels as $onelevel) {
            $levelids[$i++] = $onelevel->id;
        }
        for($i=1; $i<=$test->stylesnum; $i++) {
            for($j=1; $j<=$test->levelsnum; $j++) {
                $strinfthreshold = "infthreshold".$i.$j;
                $strsupthreshold = "supthreshold".$i.$j;
                $tablethreshold->stylesid = $styleids[$i];
                $tablethreshold->levelsid = $levelids[$j];
                $tablethreshold->infthreshold = $thresholds->$strinfthreshold;
                $tablethreshold->supthreshold = $thresholds->$strsupthreshold;
                insert_record("lstest_thresholds", $tablethreshold);
            }
        }

        for($i=1; $i<=$test->answersnum; $i++)
        {
            $stranswer = "answer".$i;
            $tableanswer->name = $answers->$stranswer;
            $tableanswer->testsid = $testid;
            insert_record("lstest_answers", $tableanswer);
        }

        $oneanswers = get_records("lstest_answers", "testsid", "$testid", "id asc");
        $i = 1;
        foreach ($oneanswers as $oneanswer) {
            $answerids[$i++] = $oneanswer->id;
        }
        
        for($i=1; $i<=$test->itemsnum; $i++)
        {
            $strquestion = "question".$i;
            $tableitem->testsid = $testid;
            $tableitem->question = $items->$strquestion;
			if($styledefined == 0){
				$strstylesid = "stylesid".$i;
				$tableitem->stylesid = $styleids[$items->$strstylesid];
			}
            $itemid = insert_record("lstest_items", $tableitem);
            for($j=1; $j<=$test->answersnum; $j++) {
                $strnocheckedscore = "nocheckedscore".$i.$j;
                $strcheckedscore = "checkedscore".$i.$j;
                $tablescore->itemsid = $itemid;
                $tablescore->answersid = $answerids[$j];
                $tablescore->nocheckedscore = $items->$strnocheckedscore;
                $tablescore->checkedscore = $items->$strcheckedscore;
				if($styledefined == 1){
					$strstylesid = "stylesid".$i.$j;
					$tablescore->stylesid = $items->$strstylesid;
				}
                insert_record("lstest_scores", $tablescore);
            }        
        }
    }

    redirect("$CFG->wwwroot/$CFG->admin/module.php?module=lstest");

?>
