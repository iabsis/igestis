<?php


require_once(SERVER_FOLDER . "/" . APPLI_FOLDER . "/includes/xajax/xajax.inc.php");
$xajax = new xajax(SERVER_ADDRESS . "/index_request.php");


// Scanner functions
$xajax->registerFunction("launch_scanner");
$xajax->registerFunction("browse_emails");

// Applets functions
$xajax->registerFunction("applet_iabsis_news_update");
$xajax->registerFunction("applet_my_favourites_update");
$xajax->registerFunction("iabsis_set_my_favourites");
$xajax->registerFunction("iabsis_my_favourites_del");
$xajax->registerFunction("applet_save_position");
$xajax->registerFunction("get_log_values");



// Misc. functions
$xajax->registerFunction("check_if_folder_exists");
$xajax->registerFunction("mini_header");

function is_header_set_has_mini() {
    global $application;
    
    $sql = "SELECT * FROM CORE_USERPREFS WHERE `option`='header_mini' AND `contact_id`='" . $application->userprefs['id'] . "'";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    $data = mysql_fetch_array($req);

    if($data['value'] == "1") return true;

    return false;
}

?>