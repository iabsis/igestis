<?php

session_start();

define("UPDATEDB_FILE", true);

include "config.php";
include "includes/common_librairie.php";

// Initialization of the application
$application = new application();

// Here, the code that don't require to be loged ////////////////////////////////////
if($_POST['section'] == "lost_password")
{
  if($_POST['new_password'] != $_POST['new_password_confirm']) $application->message_die("La confirmation ne correspond pas au mot de passe saisi");
  if(strlen($_POST['new_password']) < 5) $application->message_die("Le mot de passe doit faire au moins 5 caractères");
  if(!ereg("^[A-Za-z0-9]{50}$", $_POST['reset_key'])) $application->message_die("C'est pas joli le hacking :)");

  $sql = "SELECT * FROM CORE_CONTACTS WHERE change_password_request_id='" . $_POST['reset_key'] . "'";
  $req = mysql_query($sql) or $application->message_die("Error sql !");
  $contact = mysql_fetch_array($req);

  $sql = "UPDATE CORE_CONTACTS SET
      change_password_request_id = '',
      change_password_request_date = '0000-00-00 00:00',
      password='" . md5($_POST['new_password']) . "',
          nb_login_try = 0,
          last_login_try =  '0000-00-00 00:00'
          WHERE change_password_request_id='" . $_POST['reset_key'] . "'";
  mysql_query($sql) or $application->message_die("Error sql !");

  update_ldap_user($contact['login'], $_POST['new_password']);

  $application->message_die("Votre mot de passe a bien été changé.  <a href=\"" . SERVER_ADDRESS . "\">[Retour à l'index]</a>", false, MANAGIS_MESSAGE_INFO);
}

if($_POST['section'] != "bug_report") {
  $_GET = _mysql_real_escape_string($_GET, array("password1", "password2"));
  $_POST = _mysql_real_escape_string($_POST, array("password1", "password2"));
  $_COOKIE = _mysql_real_escape_string($_COOKIE, array("password1", "password2"));
}

/////////////////////////////////////////////////////////////////////////////////////

if(!$application->is_loged)
{// Loged or not loged, that's the question.
$application->login_form();
}

########################  !! Access to this page !! ###############################

##################################################################################
function get_contact_list($client_id)
{// Return the contact list of the user ID sent in param1
global $application;

$sql = "SELECT * FROM CORE_CONTACTS WHERE active=1 AND user_id='" . $client_id . "' ORDER BY main_contact DESC, lastname ASC, firstname ASC" ;
$req = mysql_query($sql) or $application->message_die("Unable to brows the contact database");

$contacts_list = "<table class=\"normal_table\" width=\"450px\">
    <tr>
    <th>" . $application->LANG['LANG_Lastname'] . "</th>
        <th>" . $application->LANG['LANG_Firstname'] . "</th>
            <th>" . $application->LANG['LANG_Phone'] . "</th>
                <th>" . $application->LANG['LANG_Actions'] . "</th>
                    </tr>";

while($contact = mysql_fetch_array($req))
{
  ($cpt++%2 == 0) ? $class = " class=\"ligne1\"" : $class = " class=\"ligne2\"" ;
  $link_del = "";
  if(!$contact['main_contact']) $link_del = "<a href=\"javascript:del_contact('" . $contact['id'] . "')\"><img src=\"" . $application->get_template_url() . "/images/del.gif\" /></a>";

  $contacts_list .= "<tr $class>";
  $contacts_list .= "<td>" . $contact['lastname'] . "</td>";
  $contacts_list .= "<td>" . $contact['firstname'] . "</td>";
  $contacts_list .= "<td>" . $contact['phone1'] . "</td>";
  $contacts_list .= "<td><a href=\"javascript:popup(640, 480, 'contact.php?action=edit&id=" . $contact['id'] . "', 'no', 'yes', 'no')\"><img src=\"" . $application->get_template_url() . "/images/write.png\" /></a>
  $link_del</td>" ;
  $contacts_list .= "<tr>";
}

$contacts_list .= "</table>";

$contacts_list = str_replace("\n", "", $contacts_list);
$contacts_list = str_replace("\r", "", $contacts_list);

return $contacts_list ;
}
##################################################################################

function manage_users_rights ($user_id)
{// Only accessible for the admin user
global $application;

// If not admin, quit the function
if($application->module_access("CORE") != "ADMIN") return false;

// In the first time, we will delete the existants  rights
$sql = "DELETE FROM CORE_USERS_RIGHTS WHERE user_id='" . $user_id . "'" ;
mysql_query($sql) or $application->message_die(mysql_error(), true);

// Rights for the igestis core
$sql = "INSERT INTO CORE_USERS_RIGHTS( `module_name` , `right_code` , `user_id` )
    VALUES (
    'CORE',
    '" . $_POST['core_rights'] . "',
        '" . $user_id . "')";
mysql_query($sql) or $application->message_die(mysql_error(), true);

// If there are not installed module, quit the function
if(!is_array($application->installed_modules))return false;

while(list($module, $value) = each($application->installed_modules))
{// Look each modules right
if(!isset($_POST[$module . '_rights'])) continue;

$sql = "INSERT INTO CORE_USERS_RIGHTS( `module_name` , `right_code` , `user_id` )
    VALUES (
    '" . strtoupper($module) . "',
        '" . $_POST[$module . '_rights'] . "',
            '" . $user_id . "')";
mysql_query($sql) or $application->message_die(mysql_error(), true);
}
}

##################################################################################

if($_POST['section'] == "company" || $_GET['section'] == "company")
{// Managing companies
$user_rights = $application->module_access("CORE");

if($user_rights != "ADMIN")
{
  $application->message_die("{LANG_Not_Access_to_thie_page}", false, MANAGIS_MESSAGE_INFO);
}

if($_POST['action'] == "new")
{// New company
$sql = "INSERT INTO `CORE_COMPANIES` (
    `id` ,
    `tva_rating` ,
    `name` ,
    `address1` ,
    `address2` ,
    `postal_code` ,
    `city` ,
    `phone1` ,
    `phone2` ,
    `mobile` ,
    `fax` ,
    `email` ,
    `site_web` ,
    `tva_number` ,
    `banque` ,
    `iban` ,
    `rib` ,
    `rcs` ,
    `symbol_money`)
    VALUES (
    NULL ,
    '" . numeric_format($_POST['tva_rating']) . "',
        '" . $_POST['name'] . "',
            '" . $_POST['address1'] . "',
                '" . $_POST['address2'] . "',
                    '" . $_POST['postal_code'] . "',
                        '" . $_POST['city'] . "',
                            '" . $_POST['phone1'] . "',
                                '" . $_POST['phone2'] . "',
                                    '" . $_POST['mobile'] . "',
                                        '" . $_POST['fax'] . "',
                                            '" . $_POST['email'] . "',
                                                '" . $_POST['site_web'] . "',
                                                    '" . $_POST['tva_number'] . "',
                                                        '" . $_POST['banque'] . "',
                                                            '" . $_POST['iban'] . "',
                                                                '" . $_POST['rib'] . "',
                                                                    '" . $_POST['rcs'] . "',
                                                                        '" . $_POST['symbol_money'] . "')";
mysql_query($sql) or $application->message_die(mysql_error());
die("<script type=\"text/javascript\">window.opener.location.reload(true);window.close()</script>");
exit;
}

if($_POST['action'] == "edit")
{// Edit company
$sql = "UPDATE `CORE_COMPANIES` SET
    `tva_rating`='" . numeric_format($_POST['tva_rating']) . "',
        `name`= '" . $_POST['name'] . "',
            `address1`= '" . $_POST['address1'] . "',
                `address2`= '" . $_POST['address2'] . "',
                    `postal_code`='" . $_POST['postal_code'] . "',
                        `city`= '" . $_POST['city'] . "',
                            `phone1`= '" . $_POST['phone1'] . "',
                                `phone2`= '" . $_POST['phone2'] . "',
                                    `mobile`= '" . $_POST['mobile'] . "',
                                        `fax`= '" . $_POST['fax'] . "',
                                            `email`= '" . $_POST['email'] . "',
                                                `site_web`= '" . $_POST['site_web'] . "',
                                                    `tva_number`= '" . $_POST['tva_number'] . "',
                                                        `banque`= '" . $_POST['banque'] . "',
                                                            `iban`= '" . $_POST['iban'] . "',
                                                                `rib`= '" . $_POST['rib'] . "',
                                                                    `rcs`= '" . $_POST['rcs'] . "',
                                                                        `symbol_money` = '" . $_POST['symbol_money'] . "'
                                                                            WHERE id='" . $_POST['company_id'] . "'";
mysql_query($sql) or $application->message_die(mysql_error());
die("<script type=\"text/javascript\">window.opener.location.reload(true);window.close()</script>");
exit;
}

if($_GET['action'] == "del")
{// Delete company
$sql = "DELETE FROM CORE_COMPANIES WHERE id='" . (int)$_GET['id'] . "'" ;
$req = mysql_query($sql) or die(mysql_error() . $sql);
header("location:" . $_SERVER['HTTP_REFERER']) ;
exit;
}
}

if($_POST['section'] == "client" || $_GET['section'] == "client")
{// Managing clients

$user_rights = $application->module_access("CORE");

if($user_rights != "ADMIN" && $user_rights != "TECH")
{
  $application->message_die("{LANG_Not_Access_to_thie_page}", false, MANAGIS_MESSAGE_INFO);
}

if($_POST['action'] == "new")
{// New client
if(!preg_match("/^[a-z][-a-z0-9]*$/", $_POST['login'])) $application->message_die("{LANG_Incorrect_login_format}");
if($_POST['client_type_code'] == "PART" )
{
  $_POST['user_label'] = $_POST['lastname'] ." ". $_POST['firstname'];
}

$sql = "SELECT * FROM CORE_CONTACTS WHERE login LIKE '" . $_POST['login'] . "'";
$req = mysql_query($sql) or die(mysql_error() . $sql);
if(mysql_num_rows($req)) $application->message_die("Login déjà utilisé");

$sql = "INSERT INTO CORE_USERS (client_type_code, user_label, user_type, tva_number, account_code, tva_invoice, rib) VALUES (
    '" . $_POST['client_type_code'] . "',
        '" . $_POST['user_label'] . "',
            'client',
            '" . $_POST['tva_number'] . "',
                '" . $_POST['account_code'] . "',
                    '" . $_POST['tva_invoice'] . "',
                        '" . $_POST['rib'] . "')";
$req = mysql_query($sql) or $application->message_die(mysql_error());
$id_client = mysql_insert_id();

if($_POST['password1'] == "") $_POST['password1'] = super_randomize(9);

$sql = "INSERT INTO CORE_CONTACTS (user_id, login, password, civility_code, firstname, lastname, email, language_code, address1, address2, postal_code, city, country, phone1, phone2, mobile, fax, main_contact) VALUES (
    '" . $id_client . "',
        '" . $_POST['login'] . "',
            '" . md5($_POST['password1']) . "',
                '" . $_POST['civility_code'] . "',
                    '" . $_POST['firstname'] . "',
                        '" . $_POST['lastname'] . "',
                            '" . $_POST['email'] . "',
                                '" . $_POST['language'] . "',
                                    '" . $_POST['address1'] . "',
                                        '" . $_POST['address2'] . "',
                                            '" . $_POST['postal_code'] . "',
                                                '" . $_POST['city'] . "',
                                                    '" . $_POST['country'] . "',
                                                        '" . $_POST['phone1'] . "',
                                                            '" . $_POST['phone2'] . "',
                                                                '" . $_POST['mobile'] . "',
                                                                    '" . $_POST['fax'] . "',
                                                                        '1')";
$req = mysql_query($sql) or $application->message_die(mysql_error());

update_ldap_user($_POST['login'], $_POST['password1']);

die("<script type=\"text/javascript\">window.opener.location.reload(true);window.close()</script>");
exit;

}


if($_POST['action'] == "edit")
{// Edit a client
$sql = "UPDATE CORE_USERS SET
    client_type_code = '" . $_POST['client_type_code'] . "',
        user_label = '" . $_POST['user_label'] . "',
            tva_number = '" . $_POST['tva_number'] . "',
                account_code = '" . $_POST['account_code'] . "',
                    tva_invoice = '" . $_POST['tva_invoice'] . "',
                        rib = '" . $_POST['rib'] . "'
                            WHERE id='" . (int)$_POST['client_id'] . "'";
$req = mysql_query($sql) or $application->message_die(mysql_error());

$sql = "SELECT * FROM CORE_CONTACTS WHERE user_id='" . (int)$_POST['client_id'] . "' AND main_contact=1";
$req = mysql_query($sql) or $application->message_die(mysql_error());
$contact = mysql_fetch_array($req);
update_ldap_user($contact['login'], "");
die("<script type=\"text/javascript\">window.opener.location.reload(true);window.close()</script>");
exit;

}

if($_GET['action'] == "del")
{// Delete company

$ds=ldap_connect("localhost");
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_bind($ds, LDAP_LOGIN, LDAP_PASSWORD) or die("Erreur de connexion à LDAP !!!!");

$sql = "SELECT * FROM CORE_CONTACTS WHERE user_id='" . (int)$_GET['id'] . "'" ;
$req = mysql_query($sql) or die(mysql_error() . $sql);
while($contact = mysql_fetch_array($req)) {
  $result = @ldap_search($ds, "ou=Customers,dc=domaine,dc=local","(uid=" . $contact['login'] . ")");
  $entries = @ldap_get_entries($ds, $result);
  if ($entries["count"]) {
    // User doesnt exists
    $r = ldap_delete($ds, "uid=" . $contact['login'] . ",ou=Customers,dc=domaine,dc=local");
  }
}
ldap_close($ds);

$sql = "UPDATE CORE_CONTACTS SET login='' WHERE user_id='" . $_GET['id'] . "'";
mysql_query($sql) or die(mysql_error() . $sql);

$sql = "UPDATE CORE_USERS SET is_active=0 WHERE id='" . $_GET['id'] . "'" ;
$req = mysql_query($sql) or die(mysql_error() . $sql);

header("location:" . $_SERVER['HTTP_REFERER']) ;
exit;
}

}

if($_POST['section'] == "contact" || $_GET['section'] == "contact")
{// Manage the contacts

$user_rights = $application->module_access("CORE");

if($user_rights != "ADMIN" && $user_rights != "TECH")
{
  $application->message_die("{LANG_Not_Access_to_thie_page}", false, MANAGIS_MESSAGE_INFO);
}

if($_POST['action'] == "new")
{// New contact
if(!preg_match("/^[a-z][-a-z0-9]*$/", $_POST['login'])) $application->message_die("{LANG_Incorrect_login_format}", true);
$sql = "SELECT * FROM CORE_CONTACTS WHERE login LIKE '" . $_POST['login'] . "'";
$req = mysql_query($sql) or die(mysql_error() . $sql);
if(mysql_num_rows($req)) $application->message_die("Login déjà utilisé");

if($_POST['main_contact'] == 1)
{// If the new contact become the new main contact, we must edit all the others contacts
$sql = "SELECT * FROM CORE_CONTACTS WHERE id='" . (int)$_POST['id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
$contact = mysql_fetch_array($req);
	
$sql = "UPDATE CORE_CONTACTS SET main_contact=0 WHERE user_id='" . $contact['user_id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
}

$sql = "INSERT INTO CORE_CONTACTS (user_id, login, password, civility_code, firstname, lastname, email, language_code, address1, address2, postal_code, city, country, phone1, phone2, mobile, fax, main_contact) VALUES (
    '" . (int)$_POST['id'] . "',
        '" . $_POST['login'] . "',
            '" . md5($_POST['password1']) . "',
                '" . $_POST['civility_code'] . "',
                    '" . $_POST['firstname'] . "',
                        '" . $_POST['lastname'] . "',
                            '" . $_POST['email'] . "',
                                '" . $_POST['language'] . "',
                                    '" . $_POST['address1'] . "',
                                        '" . $_POST['address2'] . "',
                                            '" . $_POST['postal_code'] . "',
                                                '" . $_POST['city'] . "',
                                                    '" . $_POST['country'] . "',
                                                        '" . $_POST['phone1'] . "',
                                                            '" . $_POST['phone2'] . "',
                                                                '" . $_POST['mobile'] . "',
                                                                    '" . $_POST['fax'] . "',
                                                                        '" . $_POST['main_contact'] . "')";
$req = mysql_query($sql) or $application->message_die(mysql_error());

update_ldap_user($_POST['login'], $_POST['password1']);

die("<script language=\"javascript\">window.opener.document.getElementById('contact_list').innerHTML=\"" . str_replace("\"", "\\\"", get_contact_list($_POST['id'])) ."\"; window.close()</script>");
}

if($_POST['action'] == "edit")
{// Edit contact

$sql = "SELECT CORE_CONTACTS.*, CORE_USERS.client_type_code
    FROM CORE_CONTACTS, CORE_USERS
    WHERE
    CORE_CONTACTS.user_id=CORE_USERS.id AND
    CORE_CONTACTS.id='" . (int)$_POST['id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
$contact = mysql_fetch_array($req);

$sql = "SELECT * FROM CORE_CONTACTS WHERE login NOT LIKE '" . $contact['login'] . "' AND login LIKE '" . $_POST['login'] . "'";
$req = mysql_query($sql) or die(mysql_error() . $sql);
if(mysql_num_rows($req)) $application->message_die("Login déjà utilisé");

if($_POST['main_contact'] == 1)
{// If the new contact become the new main contact, we must edit all the others contacts
$sql = "UPDATE CORE_CONTACTS SET main_contact=0 WHERE user_id='" . $contact['user_id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
	
$main_contact_update = "main_contact='" . $_POST['main_contact'] . "',";
	
if($contact['client_type_code'] == "PART") {
  $sql = "UPDATE CORE_USERS SET user_label='" . $_POST['lastname'] . " " . $_POST['firstname'] . "'
      WHERE id='" . $contact['user_id'] . "'";
  mysql_query($sql) or die(mysql_error() . $sql);
}
}


$update_pwd = "";
if($_POST['password1']) $update_pwd = "password='" . md5($_POST['password1']) . "',";

$sql = "UPDATE CORE_CONTACTS SET
$update_pwd
civility_code='" . $_POST['civility_code'] . "',
    firstname='" . $_POST['firstname'] . "',
        lastname='" . $_POST['lastname'] . "',
            email='" . $_POST['email'] . "',
                language_code='" . $_POST['language'] . "',
                    address1='" . $_POST['address1'] . "',
                        address2='" . $_POST['address2'] . "',
                            postal_code='" . $_POST['postal_code'] . "',
                                city='" . $_POST['city'] . "',
                                    country='" . $_POST['country'] . "',
                                        phone1='" . $_POST['phone1'] . "',
                                            phone2='" . $_POST['phone2'] . "',
                                                mobile='" . $_POST['mobile'] . "',
                                                $main_contact_update
                                                fax='" . $_POST['fax'] . "'
                                                    WHERE id='" . (int)$_POST['id'] . "'";
$req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);

update_ldap_user($_POST['login'], $_POST['password1']);

die("<script language=\"javascript\">window.opener.document.getElementById('contact_list').innerHTML=\"" . str_replace("\"", "\\\"", get_contact_list($contact['user_id'])) ."\"; window.close()</script>");

}

if($_GET['action'] == "del")
{// Delete a contact
$sql = "SELECT * FROM CORE_CONTACTS WHERE id='" . $_GET['id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
$data = mysql_fetch_array($req);

$ds=ldap_connect("localhost");
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_bind($ds, LDAP_LOGIN, LDAP_PASSWORD) or die("Erreur de connexion à LDAP !!!!");

$result = ldap_search($ds, "ou=Customers,dc=domaine,dc=local","(uid=" . $data['login'] . ")");
$entries = ldap_get_entries($ds, $result);
if ($entries["count"]) {
  // User doesnt exists
  $r = ldap_delete($ds, "uid=" . $data['login'] . ",ou=Customers,dc=domaine,dc=local");
}

ldap_close($ds);

if(!$data['main_contact'])
{
  $sql = "UPDATE CORE_CONTACTS SET login=\"\", active=0 WHERE id='" . $_GET['id'] . "'" ;
  $req = mysql_query($sql) or $application->message_die(mysql_error());
}

header("location:" . $_SERVER['HTTP_REFERER']) ;
exit;
}
}

if($_POST['section'] == "employee" || $_GET['section'] == "employee")
{// Manage the employees

$user_rights = $application->module_access("CORE");

if($user_rights != "ADMIN")
{
  $application->message_die("{LANG_Not_Access_to_thie_page}", false, MANAGIS_MESSAGE_INFO);
}

if($_POST['action'] == "new")
{// New employee
if(!preg_match("/^[a-z][-a-z0-9]*$/", $_POST['login'])) $application->message_die("{LANG_Incorrect_login_format}");

$sql = "SELECT * FROM CORE_CONTACTS WHERE id='" . (int)$_POST['id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
$contact = mysql_fetch_array($req);

$sql = "INSERT INTO CORE_USERS (company_id, user_label, user_type, rib) VALUES (
    '" . $_POST['company_id'] . "',
        '" . $_POST['lastname'] . " " . $_POST['firstname'] . "',
            'employee',
            '" . $_POST['rib'] . "')";
$req = mysql_query($sql) or $application->message_die(mysql_error());
$employee_id = mysql_insert_id();

$sql = "INSERT INTO CORE_CONTACTS (user_id, login, password, civility_code, firstname, lastname, email, language_code, address1, address2, postal_code, city, country, phone1, phone2, mobile, fax, main_contact) VALUES (
    '" . $employee_id . "',
        '" . $_POST['login'] . "',
            '" . md5($_POST['password1']) . "',
                '" . $_POST['civility_code'] . "',
                    '" . $_POST['firstname'] . "',
                        '" . $_POST['lastname'] . "',
                            '" . $_POST['email'] . "',
                                '" . $_POST['language'] . "',
                                    '" . $_POST['address1'] . "',
                                        '" . $_POST['address2'] . "',
                                            '" . $_POST['postal_code'] . "',
                                                '" . $_POST['city'] . "',
                                                    '" . $_POST['country'] . "',
                                                        '" . $_POST['phone1'] . "',
                                                            '" . $_POST['phone2'] . "',
                                                                '" . $_POST['mobile'] . "',
                                                                    '" . $_POST['fax'] . "',
                                                                        '1')";
$req = mysql_query($sql) or $application->message_die(mysql_error());
update_ldap_user($_POST['login'], $_POST['password1'], $_POST['move_folder']);

// Apply right to the user
manage_users_rights($employee_id);

die("<script type=\"text/javascript\">window.opener.location.reload(true);window.close()</script>");
exit;

}

if($_POST['action'] == "edit")
{
  $sql = "SELECT * FROM CORE_CONTACTS WHERE user_id='" . (int)$_POST['employee_id'] . "'" ;
  $req = mysql_query($sql) or $application->message_die(mysql_error());
  $contact = mysql_fetch_array($req);

  $sql = "SELECT * FROM CORE_USERS WHERE CORE_USERS.id='" . (int)$_POST['employee_id'] . "'" ;
  $req = mysql_query($sql) or $application->message_die(mysql_error());
  $user = mysql_fetch_array($req);

  $sql = "UPDATE CORE_USERS SET company_id='" . $_POST['company_id'] . "', user_label='" . $_POST['lastname'] . " " . $_POST['firstname'] . "', rib='" . $_POST['rib'] . "' WHERE id='" . (int)$_POST['employee_id'] . "'" ;
  $req = mysql_query($sql) or $application->message_die(mysql_error());

  $upate_password = "" ;
  if($_POST['password1']) $upate_password = "password = '" . md5($_POST['password1']) . "',";


  $sql = "UPDATE CORE_CONTACTS SET
      " . $upate_password . "
          civility_code='" . $_POST['civility_code'] . "',
              firstname='" . $_POST['firstname'] . "',
                  lastname='" . $_POST['lastname'] . "',
                      email='" . $_POST['email'] . "',
                          language_code='" . $_POST['language'] . "',
                              address1='" . $_POST['address1'] . "',
                                  address2='" . $_POST['address2'] . "',
                                      postal_code='" . $_POST['postal_code'] . "',
                                          city='" . $_POST['city'] . "',
                                              country_code='" . $_POST['country'] . "',
                                                  phone1='" . $_POST['phone1'] . "',
                                                      phone2='" . $_POST['phone2'] . "',
                                                          mobile='" . $_POST['mobile'] . "',
                                                              fax='" . $_POST['fax'] . "'
                                                                  WHERE user_id='" . $user['id'] . "'" ;
  $req = mysql_query($sql) or $application->message_die(mysql_error());

  update_ldap_user($_POST['login'], $_POST['password1']);

  // Mise à jour de roundcube
  $sql = "UPDATE ROUNDCUBE_identities SET email='" . $_POST['email'] . "'
      WHERE user_id IN(SELECT user_id FROM ROUNDCUBE_users WHERE username='" . $_POST['login'] . "') AND
          email LIKE '" . $contact['email'] . "'";
  $req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);

  // Apply right to the user
  manage_users_rights($user['id']);

  die("<script type=\"text/javascript\">window.opener.location.reload(true);window.close()</script>");
  exit;

}



if($_GET['action'] == "del")
{// Delete an employee
$sql = "SELECT * FROM CORE_CONTACTS WHERE user_id='" . (int)$_GET['id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error());
$data = mysql_fetch_array($req);

$ds=ldap_connect("localhost");
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_bind($ds, LDAP_LOGIN, LDAP_PASSWORD) or die("Erreur de connexion à LDAP !!!!");

$result = ldap_search($ds, "ou=Users," . LDAP_BASE,"(uid=" . $data['login'] . ")");
$entries = ldap_get_entries($ds, $result);
if ($entries["count"]) {
  // User doesnt exists
  $r = ldap_delete($ds, "uid=" . $data['login'] . ",ou=Users," . LDAP_BASE);
}

ldap_close($ds);

$sql = "UPDATE CORE_CONTACTS SET login='', active=0 WHERE user_id='" . (int)$_GET['id'] . "'" ;
$req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);

header("location:" . $_SERVER['HTTP_REFERER']) ;
exit;
}
}

if($_POST['section'] == "import_contacts")
{// Section d'impor des contacts

$user_rights = $application->module_access("CORE");
if($user_rights != "ADMIN" && $user_rights != "TECH")
{
  $application->message_die("{LANG_Not_Access_to_thie_page}", false, MANAGIS_MESSAGE_INFO);
}

if(!is_array($_POST['user'])) {
  new wizz("Aucun contacts n'a été sélectionné.");
  header("location:index.php?Page=import_contacts");
  exit;
}

$cpt_added = $cpt_updated = 0;

foreach($_POST['user'] as $user) {
  $sql = "SELECT * FROM TMP_CORE_CONTACTS_IMPORT WHERE id='" . $user . "' AND token='" . mysql_real_escape_string($_POST['token']) . "'";
  $req = mysql_query($sql) or die(mysql_error() . $sql);
  $user = mysql_fetch_array($req);

  if(!$user) continue;

  $sql = "SELECT * FROM CORE_CONTACTS WHERE login LIKE '" . mysql_real_escape_string($user['login']) . "'";
  $req = mysql_query($sql) or die(mysql_eror() . $sql);

  if(mysql_num_rows($req)) {
    // Update de l'utilisateur

    $sql = "SELECT CORE_CONTACTS.*, CORE_USERS.user_type
        FROM CORE_CONTACTS
        LEFT JOIN CORE_USERS ON CORE_USERS.id=CORE_CONTACTS.user_id
        WHERE login LIKE '" . mysql_real_escape_string($user['login']) . "'";
    $req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);
    $existing_user = mysql_fetch_array($req);

    if(!$existing_user) continue;
    if($existing_user['user_type'] != "client") {
      new wizz(sprintf("{LANG_User_X_Already_exists_as_employee}", $existing_user['login']), WIZZ_WARNING);
      continue;
    }

    $cpt_updated++;

    $sql = "UPDATE CORE_USERS
        SET
        user_label='" . mysql_real_escape_string($user['user_label']) . "',
            tva_number='" . mysql_real_escape_string($user['tva_number']) . "',
                tva_invoice='1'
                WHERE id='" . $existing_user['user_id'] . "'";
    $req = mysql_query($sql) or die(mysql_error() . $sql);

    $password = super_randomize(10);

    $sql = "UPDATE CORE_CONTACTS
        SET
        email='" . mysql_real_escape_string($user['email']) . "',
            address1='" . mysql_real_escape_string($user['address1']) . "',
                address2='" . mysql_real_escape_string($user['address2']) . "',
                    postal_code='" . mysql_real_escape_string($user['postal_code']) . "',
                        city='" . mysql_real_escape_string($user['city']) . "',
                            phone1='" . mysql_real_escape_string($user['phone1']) . "',
                                mobile='" . mysql_real_escape_string($user['mobile']) . "',
                                    fax='" . mysql_real_escape_string($user['fax']) . "'
                                        WHERE user_id='" . $existing_user['user_id'] . "' AND main_contact=1";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
  }
  else {
    // Ajout de l'utilisateur
    if(!$user['login']) continue;
    $cpt_added++;

    $sql = "INSERT INTO CORE_USERS (user_label, user_type, tva_number, tva_invoice)
        VALUES (
        '" . mysql_real_escape_string($user['user_label']) . "',
            'client',
            '" . mysql_real_escape_string($user['tva_number']) . "',
                '1')";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    $user_id = mysql_insert_id();

    $password = super_randomize(10);

    $sql = "INSERT INTO CORE_CONTACTS (user_id, login, password, email, language_code, address1, address2, postal_code, city, phone1, mobile, fax, main_contact, active)
        VALUES (
        '" . $user_id . "',
            '" . mysql_real_escape_string($user['login']) . "',
                '" . $password . "',
                    '" . mysql_real_escape_string($user['email']) . "',
                        'FR',
                        '" . mysql_real_escape_string($user['address1']) . "',
                            '" . mysql_real_escape_string($user['address2']) . "',
                                '" . mysql_real_escape_string($user['postal_code']) . "',
                                    '" . mysql_real_escape_string($user['city']) . "',
                                        '" . mysql_real_escape_string($user['phone1']) . "',
                                            '" . mysql_real_escape_string($user['mobile']) . "',
                                                '" . mysql_real_escape_string($user['fax']) . "',
                                                    '1',
                                                    '1')";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
  }

  update_ldap_user(mysql_real_escape_string($user['login']));
}

$sql = "DELETE FROM TMP_CORE_CONTACTS_IMPORT WHERE token='" . mysql_real_escape_string($_POST['token']) . "' OR (TO_DAYS(NOW()) -TO_DAYS(created_at)) > 5";
mysql_query($sql) or $application->message_die(mysql_error() . $sql);

new wizz(sprintf($application->LANG['LANG_Contacts_updated'], $cpt_added, $cpt_updated), WIZZ_SUCCESS, null);
header("location:index.php?Page=clients");
exit;
/*if(!is_file($_FILES['import_file']['tmp_name']))
 {
$application->message_die("Fichier d'import introuvable.");
}
$file = fopen($_FILES['import_file']['tmp_name'], "r");

while (!feof($file))
{// Lecture du fichier ligne par ligne
$line = stream_get_line($file, 1000000, "\n");

$user = explode(";", $line);

// Sécurité rudimentaire pour vérifier si le nombre de colonnes correspond bien avec ce que l'on attend
if(count($user) != 14) { header("location:index.php?Page=import_contacts&message=wrong_file_format"); exit; }

$sql = "SELECT login FROM CORE_CONTACTS WHERE login LIKE '" . mysql_real_escape_string($user[0]) . "'" ;
$req = mysql_query($sql) or die(mysql_error() . $sql);

// SI l'utilisateur existe deja, on ne l'importe pas
if(mysql_num_rows($req)) continue;


$sql = "INSERT INTO CORE_USERS (user_label, user_type, tva_number, tva_invoice)
VALUES (
    '" . mysql_real_escape_string($user[2]) . "',
    'client',
    '" . mysql_real_escape_string($user[3]) . "',
    '1')";
$req = mysql_query($sql) or die(mysql_error() . $sql);
$user_id = mysql_insert_id();

$password = super_randomize(10);

$sql = "INSERT INTO CORE_CONTACTS (user_id, login, password, email, language_code, address1, address2, postal_code, city, phone1, mobile, fax, main_contact, active)
VALUES (
    '" . $user_id . "',
    '" . mysql_real_escape_string($user[0]) . "',
    '" . $password . "',
    '" . mysql_real_escape_string($user[4]) . "',
    'FR',
    '" . mysql_real_escape_string($user[5]) . "',
    '" . mysql_real_escape_string($user[6]) . "',
    '" . mysql_real_escape_string($user[7]) . "',
    '" . mysql_real_escape_string($user[8]) . "',
    '" . mysql_real_escape_string($user[10]) . "',
    '" . mysql_real_escape_string($user[11]) . "',
    '" . mysql_real_escape_string($user[12]) . "',
    '1',
    '1')";
$req = mysql_query($sql) or die(mysql_error() . $sql);

update_ldap_user(mysql_real_escape_string($user[0]), $password);


}
header("location:index.php?Page=import_contacts&message=imported_seccessful"); exit; */

}

if($_POST['section'] == "my_profile") {

  if($application->userprefs['login'] == CORE_ADMIN) $application->message_die("{LANG_Root_user_have_not_access_to_this_page}");

  $sql = "SELECT email FROM CORE_CONTACTS WHERE id='" . $application->userprefs['id'] . "'";
  $req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);
  $user = mysql_fetch_array($req);

  if(!trim($_POST['firstname']) || !trim($_POST['lastname'])) {
    new wizz("Certains champs obligatoires n'ont pas été renseignés");
    header("location:index.php?Page=profile");
    exit;
  }

  $update_pwd = "";
  if($_POST['password1']) $update_pwd = "`password`='" . md5($_POST['password1']) . "',";
  $sql = "UPDATE CORE_CONTACTS SET
  $update_pwd
  civility_code='" . $_POST['civility_code'] . "',
      firstname='" . $_POST['firstname'] . "',
          lastname='" . $_POST['lastname'] . "',
              email='" . $_POST['email'] . "',
                  language_code='" . $_POST['language'] . "',
                      address1='" . $_POST['address1'] . "',
                          address2='" . $_POST['address2'] . "',
                              postal_code='" . $_POST['postal_code'] . "',
                                  city='" . $_POST['city'] . "',
                                      country='" . $_POST['country'] . "',
                                          phone1='" . $_POST['phone1'] . "',
                                              phone2='" . $_POST['phone2'] . "',
                                                  mobile='" . $_POST['mobile'] . "',
                                                      fax='" . $_POST['fax'] . "'
                                                          WHERE id='" . $application->userprefs['id'] . "'";
  $req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);

  if($application->userprefs['user_type'] == "employee") {
    $sql = "UPDATE CORE_USERS SET
        user_label='" . mysql_real_escape_string($_POST['firstname'] . " " . $_POST['lastname']) . "'
            WHERE id='" . $application->userprefs['user_id'] . "'";
    mysql_query($sql) or $application->message_die(mysql_error() . $sql, false, MANAGIS_MESSAGE_ERROR);

    // Mise à jour de roundcube
    $sql = "UPDATE ROUNDCUBE_identities SET email='" . $_POST['email'] . "'
        WHERE user_id IN(SELECT user_id FROM ROUNDCUBE_users WHERE username='" . $application->userprefs['login'] . "') AND
            email LIKE '" . $user['email'] . "'";
    $req = mysql_query($sql) or $application->message_die(mysql_error() . $sql);
  }


  update_ldap_user($application->userprefs['login'], $_POST['password1']);

  new wizz("Your changes has correctly been updated", WIZZ::$WIZZ_SUCCESS);
  header("location:index.php?Page=profile");
  exit;
}

if($_GET['section'] == "applets") {
  //Manage the plugins
  if($_GET['action'] == "new") {
    // User has selected an applet to add on his home page
    $sql = "INSERT INTO APPLET_SELECTED (user_id, applet_name) VALUES (
        '" . $application->userprefs['user_id'] . "',
            '" . $_GET['applet_id'] . "')";
    mysql_query($sql) or die(mysql_error() . $sql);
    header("location:" . urldecode($_GET['url']));
    exit;
  }

  if($_GET['action'] == "del") {
    // Include the applets librairie

    include "includes/applet_librairie.php";
    if(!defined("INDEX_LAUNCHED")) define("INDEX_LAUNCHED", true);
    $applets = new applet_collection();
    $applets->delete_position($application->userprefs['id'],  $_GET['applet_id']);
    $sql = "DELETE FROM APPLET_SELECTED WHERE user_id='" . $application->userprefs['user_id'] . "' AND applet_name='" . $_GET['applet_id'] . "'";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    header("location:index.php");
    exit;
  }
}

if($_POST['section'] == "bug_report") {
  $project_name = "igestis-ldap";
  preg_match("/module_name=([A-Za-z0-9\_]+)/", $_POST['page_url'], $matches);
  if(isset($matches[1])) {
    $project_name = "igestis-" . strtolower(str_replace("_", "-", $matches[1]));
  }
  else {
    preg_match("/modules\/([A-Za-z0-9\_]+)/", $_POST['page_url'], $matches);
    if(isset($matches[1])) $project_name = "igestis-" . strtolower(str_replace("_", "-", $matches[1]));
  }

  $mail_content = "Project: " . $project_name. "\n" .
      "Sender: " . $_POST['email'] . "\n" .
      "Page url:" . $_POST['page_url'] . "\n" .
      "Que vouliez vous faire ? " . $_POST['wanted_to_do'] . "\n" .
      "Que s'est-il produit ? " . $_POST['what_appened'] . "\n" .
      "Qu'est ce qui auait dû se produire ? " . $_POST['what_expected'];

  if(is_file(LOG_FILE) && is_readable(LOG_FILE)) {
    $log_file = "/tmp/" . super_randomize(20);
    exec("tar cfz " .  escapeshellarg($log_file) . " " . escapeshellarg(LOG_FILE));
    $newmail = new CMailFile(
        $_POST['bug_title_label'],
        SUPPORT_IGESTIS,
        REDMINE_USER,
        $mail_content,
        $log_file,
        "application/octet-stream",
        "Fichier log.tgz",
        "Cc: olivier.b@iabsis.com, gilles.h@iabsis.com");
    $newmail->sendfile();
  }
  else {
    mail(SUPPORT_IGESTIS, $_POST['bug_title_label'],
    $mail_content,
    "From: " . REDMINE_USER. "\r\n" .
    "Cc: olivier.b@iabsis.com, gilles.h@iabsis.com\r\n" .
    "Content-Type: text/plain; charset=\"utf-8\"\r\n");
  }

  $application->message_die("Merci d'avoir envoyé un rapport de bug, nous en prendrons compte dans les plus bref délais", false, MANAGIS_MESSAGE_INFO);
}

?>
