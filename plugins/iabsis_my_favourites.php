<?php
$user_rights = $application->module_access("CORE");
if($application->userprefs['user_type'] == "employee") {

    // If a little malicious guy attempt to launche this file directly, application stop with the message below ...
    if(!defined("INDEX_LAUNCHED")&& !defined("GENERAL_INDEX_REQUEST_LAUNCHED")) die("Hacking attempt");  


    // Ajout d'une occurrence de l'applet dans le getionnaire d'applet
    $applet_id = $this->add_applet("IABSIS_MY_FAVOURITES", "Afficher mes favoris");


    // Création du contenu de l'applet
    $data = "";
    $file = $application->get_html_content("plugins/iabsis_my_favourites.htm");
    $data = $file;


    // Ajout du contenu de l'applet
    $this->set_applet_data($applet_id, $data);


}

// Fonction utilisables pour cet applet
function get_favourites(&$applet_collection, &$application) {
        $sql = "SELECT * FROM APPLET_MY_FAVOURITES WHERE contact_id='" . $application->userprefs['id'] . "' OR visibility='PUBLIC' ORDER BY visibility ASC, title ASC";
        $req = mysql_query($sql) or die(mysql_error() . $sql);
        $cpt = 0;
        while($data = mysql_fetch_array($req)) {
                $application->add_block("IABSIS_MY_FAVOURITES", array(
                        "CLASS" => ($cpt++%2) ? "ligne1" : "ligne2",
                        "is_public" => ($data['visibility'] == "PUBLIC" ? true : false),
                        "IS_OWNER" => ($data['contact_id'] == $application->userprefs['id'] ? true : false),
                        "title" => $data['title'],
                        "link" => htmlentities($data['link'],ENT_COMPAT,"UTF-8"),
                        "id" => $data['id'],
                        "favicon" => SERVER_FOLDER ."/" . APPLI_FOLDER . "/cache/favicon_" . $data['id'] . ".ico",
                        "date" => convert_date_en_to_fr($data['date'])));
        }

        $application->add_var("IABSIS_RSS_LINK", IABSIS_NEWS_URL);

}
?>