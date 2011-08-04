<?php

    require_once("../../config.php");
    require_once("lib.php");
    require($CFG->libdir.'/filelib.php');

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

$filename = $_FILES['userfile']['tmp_name'];
if (filesize($filename) > 0) {
    $file = fopen($filename, "r");
    $originalcontent = fread($file, filesize($filename));
    fclose($file);
} else {
    error(get_string("nofile", "lstest"), $_SERVER["HTTP_REFERER"]);
}

// Obtains the test information
if (eregi("((<NAME>)([^<>]*)(</NAME>))",$originalcontent,$result)) {
    $test->name = $result[3];
} else {
    error(get_string("withoutname", "lstest"), $_SERVER["HTTP_REFERER"]);
}
if (eregi("((<LANG>)([^<>]*)(</LANG>))",$originalcontent,$result)) {
    $test->lang = $result[3];
} else {
    error(get_string("withoutlang", "lstest"), $_SERVER["HTTP_REFERER"]);
}
if ( count_records("lstest_tests", "name", "$test->name", "lang", "$test->lang") > 0 ) {
    error(get_string("testexists", "lstest"), $_SERVER["HTTP_REFERER"]);
}
if (eregi("((<AVAILABLE>)([^<>]*)(</AVAILABLE>))",$originalcontent,$result)) {
    $test->available = $result[3];
} else {
    $test->available = 1;
}
if (eregi("((<REDOALLOWED>)([^<>]*)(</REDOALLOWED>))",$originalcontent,$result)) {
    $test->redoallowed = $result[3];
} else {
    $test->redoallowed = 0;
}
if (eregi("((<MULTIPLEANSWER>)([^<>]*)(</MULTIPLEANSWER>))",$originalcontent,$result)) {
    $test->multipleanswer = $result[3];
} else {
    $test->multipleanswer = 0;
}
if (eregi("((<NOTANSWEREDQUESTION>)([^<>]*)(</NOTANSWEREDQUESTION>))",$originalcontent,$result)) {
    $test->notansweredquestion = $result[3];
} else {
    $test->notansweredquestion = 0;
}

if (eregi("((<STYLEDEFINED>)([^<>]*)(</STYLEDEFINED>))",$originalcontent,$result)) {
    $test->styledefined = $result[3];
} else {
    $test->styledefined = 0;
}

if (eregi("((<STYLES>)((.|\s)*)(</STYLES>))",$originalcontent,$result) && eregi("((<STYLE>)([^<>]*)(</STYLE>))",$result[3])) {
    $stylescontent = $result[3];
} else {
    error(get_string("withoutstyles", "lstest"), $_SERVER["HTTP_REFERER"]);
}

if( eregi("((<LEVELS>)((.|\s)*)(</LEVELS>))",$originalcontent,$result) && eregi("((<LEVEL>)([^<>]*)(</LEVEL>))",$result[3])) {
    $levelscontent = $result[3];
} else {
    error(get_string("withoutlevels", "lstest"), $_SERVER["HTTP_REFERER"]);
}

if (eregi("((<THRESHOLDS>)((.|\s)*)(</THRESHOLDS>))",$originalcontent,$result) && stristr($result[3], "<THRESHOLD>")) {
    $thresholdscontent = $result[3];
} else {
    error(get_string("withoutthresholds", "lstest"), $_SERVER["HTTP_REFERER"]);
}

if (eregi("((<ANSWERS>)((.|\s)*)(</ANSWERS>))",$originalcontent,$result) && eregi("((<ANSWER>)([^<>]*)(</ANSWER>))",$result[3])) {
    $answerscontent = $result[3];
} else {
    error(get_string("withoutanswers", "lstest"), $_SERVER["HTTP_REFERER"]);
}

if (eregi("((<ITEMS>)((.|\s)*)(</ITEMS>))",$originalcontent,$result) && stristr($result[3], "<ITEM>")) {
    $itemscontent = $result[3];
} else {
    error(get_string("withoutitems", "lstest"), $_SERVER["HTTP_REFERER"]);
}

if (eregi("((<SCORES>)((.|\s)*)(</SCORES>))",$originalcontent,$result) && stristr($result[3], "<SCORE>")) {
    $scorescontent = $result[3];
} else {
    error(get_string("withoutscores", "lstest"), $_SERVER["HTTP_REFERER"]);
}


$testid = insert_record("lstest_tests", $test);

$i = 1;
while (eregi("((<STYLE>)([^<>]*)(</STYLE>))",$stylescontent,$result)) {
    $styles[$i]->name = $result[3];
    $styles[$i]->testsid = $testid;
    $styles[$i]->id = insert_record("lstest_styles", $styles[$i]);

    $stylescontent = spliti("((<STYLE>)([^<>]*)(</STYLE>))",$stylescontent,2);
    $stylescontent = $stylescontent[1];

    $i++;
}

$i = 1;
while (eregi("((<LEVEL>)([^<>]*)(</LEVEL>))",$levelscontent,$result)) {
    $levels[$i]->name = $result[3];
    $levels[$i]->testsid = $testid;
    $levels[$i]->id = insert_record("lstest_levels", $levels[$i]);

    $levelscontent = spliti("((<LEVEL>)([^<>]*)(</LEVEL>))",$levelscontent,2);
    $levelscontent = $levelscontent[1];

    $i++;
}

$i = 1;
while (eregi("((<ANSWER>)([^<>]*)(</ANSWER>))",$answerscontent,$result)) {
    $answers[$i]->name = $result[3];
    $answers[$i]->testsid = $testid;
    $answers[$i]->id = insert_record("lstest_answers", $answers[$i]);

    $answerscontent = spliti("((<ANSWER>)([^<>]*)(</ANSWER>))",$answerscontent,2);
    $answerscontent = $answerscontent[1];

    $i++;
}

$i = 1;
while (stristr($itemscontent, "<ITEM>")) {

//
	if($test->styledefined == 0){
    	$aux = spliti("<ITEMSTYLE>",$itemscontent,2);
    	$result = spliti("</ITEMSTYLE>",$aux[1],2);
    	$items[$i]->stylesid = $styles[$result[0]]->id;
	}
//

    $aux = spliti("<QUESTION>",$itemscontent,2);
    $result = spliti("</QUESTION>",$aux[1],2);
    $items[$i]->question = $result[0];

    $items[$i]->testsid = $testid;

    $items[$i]->id = insert_record("lstest_items", $items[$i]);

    $aux = spliti("</ITEM>",$itemscontent,2);
    $itemscontent = $aux[1];

    $i++;
}

$i = 1;
while (stristr($scorescontent, "<SCORE>")) {

    $aux = spliti("<SCOREITEM>",$scorescontent,2);
    $result = spliti("</SCOREITEM>",$aux[1],2);
    $scores[$i]->itemsid = $items[$result[0]]->id;

    $aux = spliti("<SCOREANSWER>",$scorescontent,2);
    $result = spliti("</SCOREANSWER>",$aux[1],2);
    $scores[$i]->answersid = $answers[$result[0]]->id;

    $aux = spliti("<NOCHECKEDSCORE>",$scorescontent,2);
    $result = spliti("</NOCHECKEDSCORE>",$aux[1],2);
    $scores[$i]->nocheckedscore = $result[0];

    $aux = spliti("<CHECKEDSCORE>",$scorescontent,2);
    $result = spliti("</CHECKEDSCORE>",$aux[1],2);
    $scores[$i]->checkedscore = $result[0];
	//
		if($test->styledefined == 1){
	    	$aux = spliti("<STYLEID>",$itemscontent,2);
	    	$result = spliti("</STYLEID>",$aux[1],2);
	    	$scores[$i]->stylesid = $styles[$result[0]]->id;
		}
	//
    $scores[$i]->id = insert_record("lstest_scores", $scores[$i]);

    $aux = spliti("</SCORE>",$scorescontent,2);
    $scorescontent = $aux[1];

    $i++;
}

$i = 1;
while (stristr($thresholdscontent, "<THRESHOLD>")) {

    $aux = spliti("<THRESHOLDSTYLE>",$thresholdscontent,2);
    $result = spliti("</THRESHOLDSTYLE>",$aux[1],2);
    $thresholds[$i]->stylesid = $styles[$result[0]]->id;

    $aux = spliti("<THRESHOLDLEVEL>",$thresholdscontent,2);
    $result = spliti("</THRESHOLDLEVEL>",$aux[1],2);
    $thresholds[$i]->levelsid = $levels[$result[0]]->id;

    $aux = spliti("<INFTHRESHOLD>",$thresholdscontent,2);
    $result = spliti("</INFTHRESHOLD>",$aux[1],2);
    $thresholds[$i]->infthreshold = $result[0];

    $aux = spliti("<SUPTHRESHOLD>",$thresholdscontent,2);
    $result = spliti("</SUPTHRESHOLD>",$aux[1],2);
    $thresholds[$i]->supthreshold = $result[0];

    $thresholds[$i]->id = insert_record("lstest_thresholds", $thresholds[$i]);

    $aux = spliti("</THRESHOLD>",$thresholdscontent,2);
    $thresholdscontent = $aux[1];

    $i++;
}

redirect("$CFG->wwwroot/$CFG->admin/module.php?module=lstest", get_string("changessaved"), 1);

?>