<?php

    require_once("../../config.php");
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

$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";

$content .= "<TEST>\n\n";

$testid = $_GET['testid'];

$test = get_record("lstest_tests", "id", $testid);

$content .= "    <NAME>$test->name</NAME>\n\n";
$content .= "    <LANG>$test->lang</LANG>\n\n";
$content .= "    <AVAILABLE>$test->available</AVAILABLE>\n\n";
$content .= "    <REDOALLOWED>$test->redoallowed</REDOALLOWED>\n\n";
$content .= "    <MULTIPLEANSWER>$test->multipleanswer</MULTIPLEANSWER>\n\n";
$content .= "    <NOTANSWEREDQUESTION>$test->notansweredquestion</NOTANSWEREDQUESTION>\n\n";
$content .= "    <STYLEDEFINED>$test->styledefined</STYLEDEFINED>\n\n";

$styles = get_records("lstest_styles", "testsid", $testid, "id asc");
$content .= "    <STYLES>\n";
$order = 1;
foreach ($styles as $style) {
    $content .= "        <STYLE>$style->name</STYLE>\n";
    $stylestoorder[$style->id] = $order++;
}
$content .= "    </STYLES>\n\n";

$levels = get_records("lstest_levels", "testsid", $testid, "id asc");
$content .= "    <LEVELS>\n";
$order = 1;
foreach ($levels as $level) {
    $content .= "        <LEVEL>$level->name</LEVEL>\n";
    $levelstoorder[$level->id] = $order++;
}
$content .= "    </LEVELS>\n\n";


$answers = get_records("lstest_answers", "testsid", $testid, "id asc");
$content .= "    <ANSWERS>\n";
$order = 1;
foreach ($answers as $answer) {
    $content .= "        <ANSWER>$answer->name</ANSWER>\n";
    $answerstoorder[$answer->id] = $order++;
}
$content .= "    </ANSWERS>\n\n";


$items = get_records("lstest_items", "testsid", $testid, "id asc");
$content .= "    <ITEMS>\n";
$order = 1;
foreach ($items as $item) {
    $content .= "\n        <ITEM>\n";
	if($test->styledefined == 0){
    	$content .= "            <ITEMSTYLE>" . $stylestoorder[$item->stylesid] . "</ITEMSTYLE>\n";
	}
    $content .= "            <QUESTION>$item->question</QUESTION>\n";
    $content .= "        </ITEM>\n";
    $itemstoorder[$item->id] = $order++;
}
$content .= "    </ITEMS>\n\n";

$content .= "    <SCORES>\n";
foreach ($items as $item) {

    foreach ($answers as $answer) {
        $score = get_record("lstest_scores", "itemsid", $item->id, "answersid", $answer->id);
        $content .= "\n        <SCORE>\n";
        $content .= "            <SCOREITEM>".$itemstoorder[$item->id]."</SCOREITEM>\n";
        $content .= "            <SCOREANSWER>".$answerstoorder[$answer->id]."</SCOREANSWER>\n";
        $content .= "            <NOCHECKEDSCORE>".$score->nocheckedscore."</NOCHECKEDSCORE>\n";
        $content .= "            <CHECKEDSCORE>".$score->checkedscore."</CHECKEDSCORE>\n";
		if($test->styledefined == 1){
        	$content .= "            <STYLEID>".$stylestoorder[$score->stylesid]."</STYLEID>\n";
		}
        $content .= "        </SCORE>\n";
    }
}
$content .= "\n    </SCORES>\n\n";

$content .= "    <THRESHOLDS>\n";
$i = 1;
foreach ($styles as $style) {
    $j = 1;
    foreach ($levels as $level) {
        $threshold = get_record("lstest_thresholds", "stylesid", $style->id, "levelsid", $level->id);
        $content .= "\n        <THRESHOLD>\n";
        $content .= "            <THRESHOLDSTYLE>".$stylestoorder[$style->id]."</THRESHOLDSTYLE>\n";
        $content .= "            <THRESHOLDLEVEL>".$levelstoorder[$level->id]."</THRESHOLDLEVEL>\n";
        $content .= "            <INFTHRESHOLD>$threshold->infthreshold</INFTHRESHOLD>\n";
        $content .= "            <SUPTHRESHOLD>$threshold->supthreshold</SUPTHRESHOLD>\n";
        $content .= "        </THRESHOLD>\n";
        $j++;
    }
    $i++;
}
$content .= "\n    </THRESHOLDS>\n\n";



$content .= "</TEST>\n";

$filename = tempnam("/tmp", "lstest");
$file = fopen($filename, "w");
fwrite($file, $content);
fclose($file);

send_file("$filename", "test.xml");


?>
