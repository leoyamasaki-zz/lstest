<?php

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);        // Course Module ID
    $item = optional_param('item', 0, PARAM_INT);        // Course Module ID
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

/// Print the page header
    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($lstest->name), "", $navigation);

    lstest_print_result_menu($course->id, $USER->id, $id);

    echo "<BR>";

    if (isset($item) and $item) {

        $firstitems = get_records_select("lstest_items", "testsid = '$lstest->testsid'", "id asc", "*", "0", "1");
        $firstitem = array_pop($firstitems);
        $itemrecord = get_record("lstest_items", "id", $item);
        $stylerecord = get_record("lstest_styles", "id", $itemrecord->stylesid);
        $a->number = $item - $firstitem->id + 1;
        $a->statement = $itemrecord->question;
        $a->style = $stylerecord->name;
        print_heading_with_help(get_string("itemanswers","lstest", $a), "itemtable", "lstest");
        lstest_print_item_table($lstest->testsid, $item, $course->id, $id);
        echo "<BR>";

    } else {
        $item = false;
    }

    print_heading_with_help(get_string("itemstadistics","lstest"), "itemstable", "lstest");

    if (! isset($orderby) ) {
        $orderby = 'items';
    }

    lstest_print_items_table($lstest->testsid, $course->id, $id, $item, $orderby);

    echo "<BR>";

    print_footer($course);

?>


