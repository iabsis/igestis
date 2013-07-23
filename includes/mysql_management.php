<?php

class mysql_table {
	
	protected $entity = array();
	protected $primary_key = "";
	protected $table_name = "";
	protected $fk_list = array();	
	private $req_ressource = null;
	private $auto_fields = null;
	var $fields_list = null;
	
	function __construct($id = "") {
		if($id) {
			$this->get_entity($id);			
		}
	}
	
	/**
	 * Méthode permettant d'enregistrer la ligne dans la table
	 */
	
	public function save_entity($create_if_not_exists = true) {	
		if(!is_array($this->entity)) return false;
		
		
		if($create_if_not_exists) {
			// Si l'objet n'existe pas on en crée un nouveau dans la base de données
			$sql = "REPLACE INTO " . $this->table_name . "(" . $this->get_field_list() . ") VALUES (";
			@reset($this->entity);
			$values = "";
			while(list($key, $value) = each($this->entity)) {
				if(!preg_match("/^[0-9]*$/", $key)) $values .= ",'" . mysql_real_escape_string($value) . "'";
			}
			$sql .= substr($values, 1) . ")";	
			
			mysql_query($sql) or die(mysql_error() . $sql);		
		}
		else {
			// Si l'objet existe on l'enregistre sinon on ne crée pas de nouvel enregistremnt dans la base de données
			$clone = clone $this;
			// Si l'entité existe bien alors on enregistre les données
			if($clone->get_entity($this->entity[$this->primary_key])) {
				$sql = "REPLACE INTO " . $this->table_name . "(" . $this->get_field_list() . ") VALUES (";
				@reset($this->entity);
				$values = "";
				while(list($key, $value) = each($this->entity)) {
					if(!preg_match("/^[0-9]*$/", $key)) $values .= ",'" . mysql_real_escape_string($value) . "'";
				}
				$sql .= substr($values, 1) . ")";	
				
				mysql_query($sql) or die(mysql_error() . $sql);		
			}
			else return false;
		}
		
		// On renvoie la clé primaire de l'entité
		if(!$this->entity[$this->primary_key]) {
			$this->entity[$this->primary_key] = mysql_insert_id();
		}
		
		return $this->entity[$this->primary_key];		
	
	}

	/**
	 * Récupère une ligne dans la base de données correspondant à la clé primaire passée en paramètre
	 */

	public function get_entity($id) {		
		$sql = "SELECT * FROM " . $this->table_name . " WHERE " . $this->primary_key . "='" . mysql_real_escape_string($id) . "'";
		
		$req = mysql_query($sql) or die(mysql_error() . $sql);
		$data = mysql_fetch_array($req);
		if(is_array($data)) {
			while(list($key, $value) = each($data)) {
				$this->entity[$key] = $value;
			}
		}
		
		return $data;
	}
	
	/*
	 * Méthode permettant de récupérer la liste des champs à mettre à jour dans la base de données
	 * format de sortie : `champ1`,`champ2`,`champ3`
	 */
	
	private function get_field_list() {
		$field_list = "";
		
		if(is_array($this->entity)) {
			@reset($this->entity);
			while(list($key, $value) = each($this->entity)) {
				if(!preg_match("/^[0-9]*$/", $key)) $field_list .= ",`" . $key . "`";
			}		
		}
		
		return substr($field_list, 1);
	}
	
	/**
	 * Retourne la valeur d'un champ pour l'objet courant
	 */
	
	public function get_field_value($field) {
		if(isset($this->entity[$field])) return $this->entity[$field];
		else return null;
	}
	
	public function update_entity(Array $row) {
		if(is_array($row)) {
			while(list($key, $value) = each($row)) {
				$this->entity[$key] = $value;
			}			
		}
		
	}
	
	public function delete_entity() {
		if(isset($this->entity[$this->primary_key])) {
			$sql = "DELETE FROM " . $this->table_name . " 
					WHERE `" . $this->primary_key . "`='" . mysql_real_escape_string($this->entity[$this->primary_key]) . "'";
			mysql_query($sql) or die(mysql_error() . $sql);
		}
	}
	
	protected function add_fk_field($field, $table) {
		try {
			$this->fk_list[$field]['object_type'] = $table;
			$this->fk_list[$field]['object'] = new $table();
		}
		catch(Exception $e) {
			die($e->getMessage());
		}		
	}
	
	public function get_foreign_table_for_field($field) {
		return $this->fk_list[$field]['object'];
	}
	
	public function fk($table_name) {
		@reset($this->fk_list);
		while(list($field_name, $params) = each($this->fk_list)) {
			if($this->fk_list[$field_name]['object']->get_table_name() == $table_name) {
				return $this->fk_list[$field_name]['object'];
			}
		}
	}
	
	
	public function get_primary_key() {
		return $this->primary_key;
	}
	
	public function get_table_name() {
		return $this->table_name;
	}
	
	public function prepare($field_to_show = "", $search = "") {
		$this->browse($field_to_show, $search);
	}
	
	public function browse($field_to_show = "", $search = "", $start = 0, $number = 0) {
		$this->auto_fields = false;
		
		if($field_to_show == "") {
			$this->auto_fields = true; 
			foreach($this->get_fields_list() as $field) {  
				$field_to_show .= "," .$this->get_table_name() . ".`" . $field . "` AS " . "__FIELD_TABLE__MAIN8F_6__FIELD_NAME__$field";				
			}
			
			if(count($this->fk_list) > 0) {
				@reset($this->fk_list);
				while(list($field_name, $linked_table) = each ($this->fk_list)) {
					foreach($linked_table['object']->get_fields_list() as $field) {
						$field_to_show .= "," .$linked_table['object']->get_table_name() . ".`" . $field . "` AS " . "__FIELD_TABLE__" . $linked_table['object']->get_table_name() . "__FIELD_NAME__$field";				
					}
				}
			}
			
			$field_to_show = substr($field_to_show, 1);
		}
			
			
		$sql = "SELECT " . $field_to_show . " FROM " . $this->table_name . " ";
		if(count($this->fk_list) > 0) {
			@reset($this->fk_list);
			while(list($key, $value) = each ($this->fk_list)) {
				$linked_table = $this->get_foreign_table_for_field($key);
				$sql .= " LEFT JOIN " . $linked_table->get_table_name() . " ON ";
				$sql .= $this->table_name . "." . $key . "=" . $linked_table->get_table_name() . "." . $linked_table->get_primary_key();
			}
		}
		
		if(trim($search)) $sql .= " WHERE " . $search;
		if($number) {
			if($start) $sql .= " LIMIT " . (int)$start . ", " . (int)$number;
			else $sql .= " LIMIT " . (int)$number;
		}
		
		$this->req_ressource = mysql_query($sql) or die(mysql_error() . $sql);
	}
	
	public function fetch() {
		$datas = mysql_fetch_array($this->req_ressource);
		if(!$this->auto_fields || !$datas) return $datas;
		
		$returned_datas = array();
		
		@reset($datas);
		while(list($key, $value) = each($datas)) {
			if(preg_match("/^[0-9]+$/", $key)) continue;
			preg_match("/^__FIELD_TABLE__([\W\w]+)__FIELD_NAME__([\W\w]+)$/", $key, $return);
			$returned_datas[$return[2]] = $value;
			if($return[1] == "MAIN8F_6") {
				$this->update_entity(array($return[2] => $value));
			}
			else {
				@reset($this->fk_list);
				while(list($field_name, $params) = each($this->fk_list)) {
					if($this->fk_list[$field_name]['object']->get_table_name() == $return[1]) {
						$this->fk_list[$field_name]['object']->update_entity(array($return[2] => $value));
						break;
					}
				}
			}
		}
		
		return $returned_datas;
		
	}
	
	public function num_rows() {
		if($this->req_ressource) return mysql_num_rows($this->req_ressource);	
		return false;
	}
	
	public function get_fields_list() {
		if(!$this->fields_list) {
			$sql = "SHOW FIELDS FROM " . $this->get_table_name();
			$req = mysql_query($sql) or die(mysql_error() . $sql);
			while($data = mysql_fetch_array($req)) {
				$this->fields_list[] = $data['Field'];
			}			
		}

		return $this->fields_list;
	}

}