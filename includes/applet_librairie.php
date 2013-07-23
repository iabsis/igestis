<?php

if(!defined("INDEX_LAUNCHED") && !defined("GENERAL_INDEX_REQUEST_LAUNCHED") && !defined("UPDATEDB_FILE")) die("Hacking attempt");
define("APPLET_CLASS_LAUNCHED", true);

class applet_collection {
    var $applet_list;
    var $applet_options_list;
    var $user_selected_addons;
    var $applet_title;

    function applet_collection() {// Constructor will search every applet found on each addon
        global $application;

        $this->user_selected_addons = NULL;
        $this->applet_options_list = NULL;
        $this->user_selected_addons = NULL;

        $numargs = func_num_args();

        // Init variables
        $applet_list = NULL;

        // Listing all plugins in the plugin folder
        $directory_to_search = SERVER_FOLDER . "/" . APPLI_FOLDER . "/plugins/";
        $dir = opendir($directory_to_search);

        if($numargs > 0) {
            $arg_list = func_get_args();
            for($i = 0; $i < $numargs; $i++) {
                if(is_file($directory_to_search.$arg_list[$i])) {
                    include $directory_to_search.$arg_list[$i];
                }
            }

        }
        else {
            while ($file = readdir($dir)) {
                if(is_file($directory_to_search.$file)) {
                    // If thats not a good folder, we restart the loop
                    if($file == "." || $file == "..") continue;
                    // If there are not a config.php, we restart the loop
                    if(!is_file($directory_to_search.$file)) continue;
                    // Including the config file of the menu
                    include $directory_to_search.$file;
                }
            }
        }

    }

    function get_applet_list() {
        return $this->applet_list;
    }

    function add_applet($applet_id, $applet_name, $for_all=FALSE, $show_content=TRUE) {
        $this->applet_list[] = array("id" => $applet_id, "title" => $applet_name, "for_all" => $for_all, "show_content" => $show_content);
        return count($this->applet_list) - 1;
    }

    function get_option_value($applet_name, $option_name, $option_default_value="", $id_user=0) {
        // On vérifie les infos stockées dans la base de données

        if(!$applet_name) die("Illegal applet name");
        if(!$option_name) die("Illegal option name");


        if(isset($this->applet_options_list[$applet_name][$option_name])) return $this->applet_options_list[$applet_name][$option_name];

        $sql = "SELECT * FROM APPLET_INFOS WHERE APPLET_NAME='" . $applet_name . "' AND APPLET_USER_ID=0 OR APPLET_USER_ID='" . $application->userprefs['user_id'] . "' ORDER BY APPLET_USER_ID";
        $req = mysql_query($sql) or die(mysql_error() . $sql);
        while($data = mysql_fetch_array($req)) {
            $this->applet_options_list[$applet_name][$data['APPLET_OPTION']] = $data['APPLET_VALUE'];
        }


        if(isset($this->applet_options_list[$applet_name][$option_name])) return $this->applet_options_list[$applet_name][$option_name];
        else {
            $sql = "INSERT INTO APPLET_INFOS (APPLET_NAME, APPLET_OPTION, APPLET_VALUE, APPLET_USER_ID) VALUES (
						'" . mysql_real_escape_string($applet_name) . "',
						'" . mysql_real_escape_string($option_name) . "',
						'" . mysql_real_escape_string($option_default_value) . "',
						'" . (int)$id_user . "')";
            $req = mysql_query($sql) or die(mysql_error() . $sql);
            $this->applet_options_list[$applet_name][$option_name] = $option_default_value;
            return $this->applet_options_list[$applet_name][$option_name];
        }
    }

    function set_option($applet_name, $option_name, $option_value, $id_user=NULL) {
        // On enregistre un paramètre dans la base de données
        $sql = "UPDATE APPLET_INFOS SET APPLET_VALUE='" . mysql_real_escape_string($option_value) . "'
				WHERE 
					APPLET_NAME='" . mysql_real_escape_string($applet_name) . "' AND
					APPLET_OPTION='" . mysql_real_escape_string($option_name) . "'";
        if($id_user) $sql .= " AND APPLET_USER_ID='" . (int)$id_user . "'";

        $req = mysql_query($sql) or die(mysql_error() . $sql) ;

        $this->applet_options_list[$applet_name][$option_name] = $option_value;
    }

    function set_applet_data($applet_id, $applet_data) {
        // On passe les données de l'addon pour stockage avant affichage
        $applet_data = str_replace('{WIDGET_NAME}', $this->applet_list[$applet_id]['id'], $applet_data);
        $applet_data = str_replace('{WIDGET_TITLE}', $this->applet_list[$applet_id]['title'], $applet_data);
        $applet_data = str_replace('WIDGET_DELETABLE', "WIDGET_DELETABLE_" . $this->applet_list[$applet_id]['id'], $applet_data);
        $this->applet_list[$applet_id]['data'] = $applet_data;
        
    }

    function set_content(&$page, $column1 = "column1", $column2 = "column2", $column3 = "column3", $hidden_applets_column = "hidden_applets") {
        // On applique les applets dans la page passée en premier parametre

        global $application;

        $column1_value = $column2_value = $column3_value = $hidden_applets = "";
        $applet_column1 = $applet_column2 = $applet_column3 = array();

        if(is_array($this->applet_list)) {
            foreach($this->applet_list as $applet) {                
                if($this->user_has_selected_this_applet($applet['id'])) {
                    if($applet['show_content'])
                    {
                        $position = $this->set_position($application->userprefs['id'], $applet['id']);
                        ${"applet_column" . $position['column']}[$position['row']] = $applet['data'];
                    }
                    else {
                        $hidden_applets .= $applet['data'] . "\n";
                    }

                }
            }
        }

        $page = str_replace("{" . $column1 .  "}", implode("\n", $applet_column1), $page);
        $page = str_replace("{" . $column2 .  "}", implode("\n", $applet_column2), $page);
        $page = str_replace("{" . $column3 .  "}", implode("\n", $applet_column3), $page);
        $page = str_replace("{" . $hidden_applets_column .  "}", $hidden_applets, $page);
    }

    function delete_position($contact_id, $applet_id) {
        $sql = "DELETE FROM APPLET_POSITION WHERE contact_id='" . $contact_id . "' AND applet_id='" . $applet_id . "'";
        mysql_query($sql) or die(mysql_error() . $sql);
        $this->clean_positions();
    }

    function clean_positions() {
        global $application;

        $sql = "SELECT `column` FROM APPLET_POSITION
                WHERE contact_id='" . $application->userprefs['contact_id'] . "'
                GROUP BY `column` ORDER BY `column`";
        $req_column = mysql_query($sql) or die(mysql_error() . $sql);
        $cpt = 1;
        while($column = mysql_fetch_array($req_column)) {
            $sql = "SELECT `row` FROM APPLET_POSITION
                    WHERE contact_id='" . $application->userprefs['contact_id'] . "' AND `column`='" . $column['column'] . "
                    ORDER BY `row`";
            $req_row = mysql_query($sql) or die(mysql_error() . $sql);
            while($row = mysql_fetch_array($req_row)) {
                $sql = "UPDATE APPLET_POSITION SET
                            `row`='" . $cpt . "'
                        WHERE
                            contact_id='" . $application->userprefs['contact_id'] . "' AND
                            `column`='" . $column['column'] . " AND
                            `row`='" . $row['row'] . "'";
                mysql_query($sql) or die(mysql_error() . $sql);

                $cpt ++;
            }

        }
    }

    function set_position($contact_id, $applet_id, $row="auto", $column="auto")
    {
        if($column == "auto" && $row == "auto") {
            // Les 2 colonnes sont automatiques
            $sql = "SELECT * FROM  APPLET_POSITION WHERE contact_id='" . $contact_id . "' AND applet_id='" . $applet_id . "'";
            $req = mysql_query($sql) or die(mysql_error() . $sql);
            if($data = mysql_fetch_array($req)) {
                return array("row" => $data['row'], "column" => $data['column']);
            }

            // On enregistre la colonne tant qe toutes les colonnes n'ont pas au moins un plugin
            $sql = "SELECT * FROM APPLET_POSITION WHERE contact_id='" . $contact_id . "' AND `column`='1'";
            $req = mysql_query($sql) or die(mysql_error() . $sql);
            if(!mysql_num_rows($req)) $column = 1;
            else {
                $sql = "SELECT * FROM APPLET_POSITION WHERE contact_id='" . $contact_id . "' AND `column`='2'";
                $req = mysql_query($sql) or die(mysql_error() . $sql);
                if(!mysql_num_rows($req)) $column = 2;
                else {
                    $sql = "SELECT * FROM APPLET_POSITION WHERE contact_id='" . $contact_id . "' AND `column`='3'";
                    $req = mysql_query($sql) or die(mysql_error() . $sql);
                    if(!mysql_num_rows($req)) $column = 3;
                }
            }

        }

        if($column == "auto") {
            // On cherche quelle colonne est la moins haute
            $sql = "SELECT
                        `column`, MAX(`row`) AS max_row
                    FROM APPLET_POSITION
                    WHERE contact_id='" . $contact_id . "'
                    GROUP BY `column`
                    ORDER BY max_row, `column`
                    LIMIT 1";
            $req = mysql_query($sql) or die(mysql_error() . $sql);
            $data = mysql_fetch_array($req);
            $column = $data['column'];            

        }
        if($row == "auto") {
            // On cherche quelle ligne correspond à la colonne passée
            $sql = "SELECT `row`
                    FROM APPLET_POSITION
                    WHERE contact_id='" . $contact_id . "' AND `column`='" . $column . "'
                    ORDER BY `row` DESC";
            $req = mysql_query($sql) or die(mysql_error() . $sql);
            $data = mysql_fetch_array($req);
            $row = $data['row'] + 1;
        }

        // On décalle tous les applet qui doivent être déplacer pour insérer notre applet
        $sql = "UPDATE APPLET_POSITION SET `row`=`row`+1
                WHERE contact_id='" . $contact_id . "' AND
                      `column`='" . $column . "' AND
                      `row`>='" . $row . "'";
        mysql_query($sql) or die(mysql_error() . $sql);

        // On enregistre la position de l'applet à sa nouvelle position
        $sql = "REPLACE INTO APPLET_POSITION (`row`, `column`, contact_id, applet_id) VALUES (
                    '" . $row . "',
                    '" . $column . "',
                    '" . $contact_id . "',
                    '" . $applet_id . "')";
        mysql_query($sql) or die(mysql_error() . $sql);

        $this->clean_positions();
        return array("row" => $row, "column" => $column);

    }

    function user_has_selected_this_applet($applet_id) {
        global $application;

        if(!isset($this->user_selected_addons)) {
            $sql = "SELECT * FROM APPLET_SELECTED WHERE user_id='" . $application->userprefs['user_id'] . "'";
            $req = mysql_query($sql) or die(mysql_error() . $sql);
            while($data = mysql_fetch_array($req)) {
                $this->user_selected_addons[$data['applet_name']] = true;
                $application->add_var("WIDGET_DELETABLE_" . $data['applet_name'], true);
            }
        }

        if(is_array($this->applet_list)) {
            foreach($this->applet_list as $applet) {
                if($applet['for_all']) {
                    $this->user_selected_addons[$applet['id']] = true;                    
                }
            }
        }

        if(isset($this->user_selected_addons[$applet_id])) return true;
        return false;
    }
}

?>