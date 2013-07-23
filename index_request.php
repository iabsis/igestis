<?php

session_start();

define("GENERAL_INDEX_REQUEST_LAUNCHED", true);

include "includes/common_librairie.php";
include "includes/xajax/xajax.inc.php";
include "includes/applet_librairie.php";


// Initialization of the application
$application = new application();

function browse_emails($email)
{
  $objResponse = new xajaxResponse();

  $sql = "SELECT * FROM CORE_CONTACTS WHERE email LIKE '" . mysql_real_escape_string($email) . "'";
  $req = mysql_query($sql) or die("SQL Error!");

  $objResponse->addScript("validable=true;");

  $nb_enreg = mysql_num_rows($req);
  if($nb_enreg < 2)
  {
    $objResponse->addScript("document.getElementById('p_login').style.display='none';");
    $objResponse->AddAssign("login_list", "innerHTML","");
    return $objResponse;
  }
  else
  {
    $login_list = "<select name=\"login\" id=\"login\"><option value=\"\" selected=\"selected\">-- Sélectionnez --</option>";
    while($contact = mysql_fetch_array($req))
    {// Browse each users that have this email
      $login_list .= "<option value=\"" . $contact['login'] . "\">" . $contact['login'] . "</option>";
    }
    $login_list .= "</select>";

    $objResponse->addScript("document.getElementById('p_login').style.display='block';");
    $objResponse->AddAssign("login_list", "innerHTML",$login_list);
  }

  return $objResponse;
}

function launch_scanner($scanner, $form_name="", $return_field="", $subfolder="", $type="")
{
  $objResponse = new xajaxResponse();

  switch($type) {
    case "share" : $complete_folder = create_smb_url() . "/PARTAGES/" . urldecode($subfolder); break;
    case "perso" : $complete_folder = create_smb_url() . "/PERSO/" . urldecode($subfolder); break;
    default : $complete_folder = $_SESSION['ROOT_UPLOAD_FOLDER'] . "/" . urldecode($subfolder); break;
  }

  $file_name = date("Y-m-d-H-i-s") . ".jpg";
  if (ereg("\.\.\/|\/\.\.|^\.\.$", $complete_folder)) die("Please, don't try to hack me :)") ;

  if(!scanner($scanner,  $complete_folder, $file_name, "A4"))
  {// File don't exists (probably scanning failed
    new wizz("Unable to scan your file. Please wait a couple of minutes or select an other method", WIZZ_ERROR, $objResponse, 5);
    $objResponse->addScript("hide_progress_message();");
    return $objResponse;
  }

  if(!wizz::already_wizzed(WIZZ_ERROR)) new wizz("Le document a été numérisé avec succès.", WIZZ_SUCCESS, NULL, 5);

  // If file exists, return the file name to the form and force the form to be validate...
  $objResponse->addScript("valid_auto=true;");
  if($return_field) $objResponse->addAssign($return_field, "value", $file_name);
  if($form_name) $objResponse->addScript("document." . $form_name . ".submit()");
  return $objResponse;

}

function applet_save_position($id, $row, $column) {
  $objResponse = new xajaxResponse();

  global $application;
  if(!$application->is_loged) return $objResponse;

  if(ereg("widget widget-", $id)) $id = str_replace("widget widget-", "", $id);

  $applets = new applet_collection();
  $applets->set_position($application->userprefs['id'], $id, $row, $column);

  return $objResponse;
}


################### Fonction utilisées pour les applets #######################################################################
function applet_iabsis_news_update() {

  $objResponse = new xajaxResponse();

  global $application;
  if(!$application->is_loged) return $objResponse;
  $content = $application->get_html_content("plugins/iabsis_news_data.htm");

  $plugin = new applet_collection("iabsis_news.php");

  // Ajout des ingos du plugins dans la sortie HTML en cours
  get_iabsis_news($plugin, $application);

  // Génération du HTML
  $content = $application->show_content($content, true);

  // Envoie du HTML à l'applet
  $objResponse->addAssign("iabsis_news_content", "innerHTML", $content);

  // On relance le timer pour la prochaine mise à jour de l'applet
  $objResponse->AddScript("update_tot();");
  return $objResponse;
}

function applet_my_favourites_update() {

  $objResponse = new xajaxResponse();

  global $application;
  $user_rights = $application->module_access("CORE");
  if($user_rights != "ADMIN" && $user_rights != "TECH") return $objResponse;
  $content = $application->get_html_content("plugins/iabsis_my_favourites_data.htm");

  $plugin = new applet_collection("iabsis_my_favourites.php");

  // Ajout des ingos du plugins dans la sortie HTML en cours
  get_favourites($plugin, $application);

  // Génération du HTML
  $content = $application->show_content($content, true);

  // Envoie du HTML à l'applet
  $objResponse->addAssign("iabsis_my_favourites_content", "innerHTML", $content);


  return $objResponse;
}

function check_if_folder_exists($login) {
  $objResponse = new xajaxResponse();
  global $application;

  if($login == "") die("Incorrect login");
  if(!is_dir("/home/" . $login)) {
    $objResponse->addScript('validable=true;document.employee.submit();');
  }
  else {
    $objResponse->addScript('validable=true;did_move_personnal_folder()');
  }

  return $objResponse;
}

function mini_header($is_mini) {
  $objResponse = new xajaxResponse();
  global $application;
  $sql = "REPLACE INTO CORE_USERPREFS (`contact_id`, `option`, `value`)
      VALUES ('" . $application->userprefs['id'] . "', 'header_mini','" . (int)$is_mini . "')";
  mysql_query($sql) or die(mysql_error() . $sql);
  return $objResponse;
}

function iabsis_set_my_favourites($action, $id, $title, $link, $visibility) {
  global $application;
  $objResponse = new xajaxResponse();
  $user_rights = $application->module_access("CORE");
  if($user_rights != "ADMIN" && $user_rights != "TECH") return $objResponse;
  if($user_rights != "ADMIN") $visibility = "PRIVATE";

  switch($action) {
    case "new" :
      $sql = "INSERT INTO APPLET_MY_FAVOURITES (title, link, visibility, contact_id) VALUES (
          '" . mysql_real_escape_string($title) . "',
              '" . mysql_real_escape_string($link) . "',
                  '" . mysql_real_escape_string($visibility) . "',
                      '" . $application->userprefs['id'] . "')";
      mysql_query($sql) or die(mysql_error() . $sql);
      break;

    case "edit" :
      $sql = "UPDATE APPLET_MY_FAVOURITES SET
          title='" . mysql_real_escape_string($title) . "',
              link='" . mysql_real_escape_string($link) . "',
                  visibility='" . mysql_real_escape_string($visibility) . "'
                      WHERE id='" . (int)$id . "' AND contact_id='" . $application->userprefs['id'] . "'";
      mysql_query($sql) or die(mysql_error() . $sql);
      break;
  }


  return applet_my_favourites_update();
}

function iabsis_my_favourites_del($id) {
  global $application;
  $user_rights = $application->module_access("CORE");
  if($user_rights != "ADMIN" && $user_rights != "TECH") return $objResponse;

  $sql = "DELETE FROM APPLET_MY_FAVOURITES WHERE contact_id='" . $application->userprefs['id'] . "' AND id='" . $id . "'";
  mysql_query($sql) or die(mysql_error() . $sql);

  return applet_my_favourites_update();
}


function get_log_values($table_span, $line_number) {
  global $application;
  $objResponse = new xajaxResponse();

  if($application->userprefs['login'] != CORE_ADMIN) {
    new wizz("Vus n'avez pas accès à cette page", WIZZ_ERROR, $objResponse);
    return $objResponse;
  }

  $cpt = 0;
  $content = $application->get_html_content("gestion_show_log_table.htm");
  $handle = popen("tac /var/log/igestis/igestis.log", "r");
  if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
      $application->add_block("LOG_FILE", array(
          "CLASS" =>($cpt%2 ? "ligne1" : "ligne2"),
          "line" => $buffer
      ));

      if(++$cpt > $line_number) break;
    }
    @pclose($handle);
  }

  $objResponse->addAssign($table_span, "innerHTML", $application->show_content($content, true));
  return $objResponse;
}





require_once("index_common.php");

// Inclusion de tous les fichiers index_request.php de chaque modules si existants :
if(is_array($application->installed_modules))
{
  while(list($key,$value) = each($application->installed_modules))
  {
    if(is_file(SERVER_FOLDER."/".APPLI_FOLDER."/modules/".$key."/index_request.php"))
    {
      require_once SERVER_FOLDER."/".APPLI_FOLDER."/modules/".$key."/index_request.php";
    }

    if(is_file(SERVER_FOLDER."/".APPLI_FOLDER."/modules/".$key."/index_common.php"))
    {
      require_once SERVER_FOLDER."/".APPLI_FOLDER."/modules/".$key."/index_common.php";
    }
  }
}

$xajax->processRequests();
?>