<?php

session_start();

include "../../config.php";
include "../common_librairie.php";

// Initialization of the application
$application = new application();

if(!$application->is_loged || $application->userprefs['user_type'] != "employee") die();
$content = $_GET['term'];

$sql = "SELECT * FROM CORE_USERS WHERE is_active=1 AND user_type='client' AND (id LIKE '%" . mysql_real_escape_string($content) . "%' OR user_label LIKE '%" . mysql_real_escape_string($content) . "%') ORDER BY user_label LIMIT 10" ;
$req = mysql_query($sql) or die(mysql_error() . $sql);

$users_list = array();
while($client = mysql_fetch_array($req))
{
    $users_list[] = array("label" =>$client['user_label'], "id" => $client['id']) ;
}
echo json_encode($users_list);
