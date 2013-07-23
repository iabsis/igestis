<?php
/**
 * DEBUG_MODE should always be false on the production serveur
 */
define("DEBUG_MODE", false);

function messageOnError()
{
  if(!headers_sent() && error_get_last() !== NULL) {
    ob_start();
    var_dump(error_get_last());
    $ErrorContent = ob_get_clean();
    if(!DEBUG_MODE) $ErrorContent = "";
    $html = file_get_contents(__DIR__ . "/error500.html");
    $html = str_replace("{ErrorContent}", "<pre>" . $ErrorContent . "</pre>", $html);
    die($html);
  }
}
register_shutdown_function('messageOnError');
error_reporting(0);

// Start the igestis session
session_start();

// Include common file. This one will include all the necessary files and invoke the autoloaders
include __DIR__ . "/includes/common_librairie.php";

// Include xajax library only for old modules
include __DIR__ . "/includes/xajax/xajax.inc.php";

Igestis\Utils\Debug::addLog("Loading librairies completed");

// Is the index file launched ?
define("INDEX_LAUNCHED", true);

$xajax = new xajax(SERVER_ADDRESS . "/index_request.php");
require_once(SERVER_FOLDER . "/" . APPLI_FOLDER . "/index_common.php");

Igestis\Utils\Debug::addLog("Xajax for the old modules initialized");

// Initialization of the application
$application = new application();

Igestis\Utils\Debug::addLog("Application object instancied");
$user_rights = $application->module_access("CORE");
new IgestisParseRequest($application);



// Manage all remaining scripts (to keep compatibility with the old version of igestis and modules)
if (!$application->security->is_loged()) {// Loged or not loged, that's the question.
  $application->login_form();
}
switch ($_GET['Page']) {
  case "" :
    $application->set_page_title("{LANG_HOME_PAGE_Title}");
    include "gestion_home_page.php";
    break;
  case "profile" :
    $application->set_page_title("{LANG_MY_PROFILE_TITLE}");
    include "my_profile.php";
    break;
  case "companies" :
    $application->set_page_title("{LANG_Companies_managment}");
    if ($user_rights != "ADMIN" && $user_rights != "TECH")
      $application->message_die("{LANG_Not_Access_to_thie_page}");
    include "gestion_companies.php";
    break;
  case "clients" :
    $application->set_page_title("{LANG_Customers_managment}");
    if ($user_rights != "ADMIN" && $user_rights != "TECH")
      $application->message_die("{LANG_Not_Access_to_thie_page}");
    include "gestion_clients.php";
    break;
  case "employees" :
    $application->set_page_title("{LANG_Employee_managment}");
    if ($user_rights != "ADMIN" && $user_rights != "TECH")
      $application->message_die("{LANG_Not_Access_to_thie_page}");
    include "gestion_employees.php";
    break;
  case "import_contacts" :
    $application->set_page_title("{LANG_IMPORT_CORE_CONTACTS_TITLE}");
    if ($user_rights != "ADMIN" && $user_rights != "TECH")
      $application->message_die("{LANG_Not_Access_to_thie_page}");
    include "import_contacts.php";
    break;
  case "import_contacts_result" :
    $application->set_page_title("{LANG_IMPORT_CORE_CONTACTS__TITLE}");
    if ($user_rights != "ADMIN" && $user_rights != "TECH")
      $application->message_die("{LANG_Not_Access_to_thie_page}");
    include "import_contacts_result.php";
    break;
  case "module" :
    if (!eregi("^[a-z0-9\_]+$", $_GET['module_name']))
      $application->message_die("{LANG_Incorrect_module_name}");
    $module_folder = SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/" . $_GET['module_name'] . "/";

    if (!is_file($module_folder . "config.php"))
      $application->message_die("Module not found !");
    include $module_folder . "config.php";

    $config = ${
      $_GET['module_name']};

      // FIchier à include :
      if (!is_file($module_folder . $config['module_menu_name']['script_file'][(int) $_GET['rubrique']]))
        $application->message_die("{LANG_Script_not_found}");
      include $module_folder . $config['module_menu_name']['script_file'][(int) $_GET['rubrique']];

      break;

  case "show_log" :
    $application->set_page_title("{LANG_Show_igestis_logs}");
    if ($application->userprefs['login'] != CORE_ADMIN)
      $application->message_die("{LANG_Not_Access_to_thie_page}");
    include "gestion_show_log.php";
    break;

  case "test" :
    $application->set_page_title("{LANG_Show_igestis_logs}");
    include "test.php";
    break;

  case "logout" :
    session_destroy();
    setcookie("sess_login", "", time() - 3600);
    setcookie("sess_password", "", time() - 3600);
    header("location:index.php");
    exit;
    break;
  default :
    $application->set_page_title("{LANG_Unknown_module}");
    $application->message_die("{LANG_Unknown_module}");
    break;
}
