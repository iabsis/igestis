<?php
use Igestis\Utils\Dump;

/**
 * LDAP Node
 * 
 * This file is a part of the Object LDAP PHP API (oldaphp) project.
 * 
 * LICENSE:
 * 
 * Copyright (c) 1998, Regents of the University of California
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the University of California, Berkeley nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE REGENTS AND CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Emeric Verschuur <contact@openihs.org>
 * @copyright Copyright (C) 2009, Emeric Verschuur
 * @link http://openxodm.openihs.org
 * @license http://www.freebsd.org/copyright/freebsd-license.html BSD License
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License version 3
 */

/**
 * LDAP Node
 *
 */
class LDAPNode extends LDAPEntity implements ArrayAccess, Iterator {
	/**
	 * LDAP Node Object constructor
	 *
	 * @param ressource $lfd
	 * @param ressource $efd
	 * @param string $dn
	 */
	public function __construct($lfd, $efd, $dn){
		parent::__construct($lfd,$efd,$dn);
		$this->it = false;
	}
	
	/**
	 * Internal use only ([] operator)
	 * Set the values @a $value att the attribute @a $offset
	 * 
	 * @param string $attribute Attribute name
	 * @param array $values Values
	 */
	public function offsetSet($attribute, $values){
		$this->checkState(false);
		if($this->offsetExists($attribute)){
			if(!@ldap_mod_replace($this->lfd, $this->dn, array($attribute => $values))){
				throw $this->newExeption();
			}
		}else{
			if(!@ldap_mod_add($this->lfd, $this->dn, array($attribute => $values))){
				throw $this->newExeption();
			}
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Set attributes
	 * 
	 * @param array $node
	 */
	public function setAttributes($node){
		$this->checkState(false);
		if(!@ldap_modify($this->lfd, $this->dn, $node)){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Internal use only ([] operator)
	 * Test if the attribute @a $offset exist
	 * 
	 * @param string $attribute Attribute name
	 * @param array $values Values
	 */
	public function offsetExists($attribute){
		$this->checkState();
		return (@ldap_get_values($this->lfd, $this->efd, $attribute) ? true : false);
	}
	
	/**
	 * Internal use only ([] operator)
	 * 
	 * @param string $attribute Attribute name
	 * @warning This method update the LDAP shema only. This result node still unchanged.
	 */
	public function offsetUnset($attribute){
		$this->checkState(false);
		if(!@ldap_mod_del($this->lfd, $this->dn, $attribute)){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Internal use only ([] operator)
	 * 
	 * @param string $attribute Attribute name
	 * @return LDAPAttribute
	 */
	public function offsetGet($attribute){
		$this->checkState();
		return new LDAPAttribute($this->lfd,$this->efd,$this->dn,$attribute);
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Relocate the cursor on the first attribute
	 */
	public function rewind(){
		$this->checkState();
		$this->it = @ldap_first_attribute($this->lfd, $this->efd);
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Get the current attribute
	 * 
	 * @return LDAPAttribute
	 */
	public function current(){
		return new LDAPAttribute($this->lfd,$this->efd,$this->dn,$this->it);
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Get the the current attribute name
	 * 
	 * @return string
	 */
	public function key(){
		return $this->it;
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Select the next attribute
	 */
	public function next(){
		$this->it = @ldap_next_attribute($this->lfd, $this->efd);
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Test if the current attribute is valid
	 * 
	 * @return boolean
	 */
	public function valid(){
		return $this->it != false;
	}
	
	/**
	 * Reload this node (ie: after an update)
	 */
	public function reload(){
		$ret = @ldap_read($this->lfd,$this->dn,"(objectclass=*)");
		if(!$ret){
			throw $this->newExeption();
		}
		$this->efd = @ldap_first_entry($this->lfd,$ret);
		if(!$this->lfd){
			throw $this->newExeption();
		}
		$this->dn = @ldap_get_dn($this->lfd, $this->efd);
		$this->state = self::UPTODATE;
	}
	
	/**
	 * Get attributes
	 * 
	 * @return array a complete node information in a multi-dimensional array
	 * @see ldap_get_attributes function in the PHP documentation
	 */
	public function getAttributes(){
		$this->checkState();
		$ret = @ldap_get_attributes($this->lfd, $this->rfd);
		if(!$ret){
			return null;throw $this->newExeption();
		}
		return $ret;
	}
	
	/**
	 * Add attribute values to current attributes
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param array $attributes
	 */
	public function addAttributes($attributes){
		$this->checkState(false);
		if(!@ldap_mod_add($this->lfd, $this->dn, $attributes)){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Delete attribute values from current attributes
	 * 
	 * @param array $attributes
	 */
	public function deleteAttributes($dn, $attributes){
		$this->checkState(false);
		if(!@ldap_mod_del($this->lfd, $dn, $attributes)){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Replace attribute values of this node with new ones
	 * 
	 * @param array $attributes
	 */
	public function replaceAttributes($attributes){
		$this->checkState(false);
		if(!@ldap_mod_replace($this->lfd, $this->dn, $attributes)){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Modify this node
	 * 
	 * @param array $node
	 */
	public function modify($node){
                                
		$this->checkState(false);
		if(!@ldap_modify($this->lfd, $this->dn, $node)){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
        
        public function mergeArrays($oldValues, $newValues) {
            $oldNode = array();
            if(!count($oldValues) > 0) return;
            reset($oldValues);
            
            foreach($oldValues as $dn => $entry){ // Pour chaque entrée
                    foreach($entry as $attr => $values){ // pour chaque attribut
                            foreach($values as $value){// pour chaque valeur
                                   $oldNode[$attr][] = $value;
                            }
                    }
                    break;
            }
            reset($oldNode);
            foreach ($oldNode as $key => &$row) {
                if(is_array($row) && count($row) == 1) {
                    $row = $row[0];
                }
            }

            foreach ($newValues as $attr => $value) {
                if($attr == "objectClass") {
                    foreach ($value as $objectClass) {
                        if(!in_array($objectClass, $oldNode[$attr])) {
                            $oldNode[$attr][] = $objectClass;
                        }
                    }
                    continue;
                }
                
                if($oldNode[$attr] != $value) {
                    $oldNode[$attr] =$value;
                }
            }
            return $oldNode;
        }


        /**
	 * Modify the name of an node
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param string $newrdn The new RDN
	 * @param string $newparent The new parent/superior node
	 * @param bool $deleteoldrdn If true the old RDN value(s) is removed, else the old RDN value(s) is retained as non-distinguished values of the node
	 */
	public function moveTo($newrdn, $newparent=null, $deleteoldrdn=true){
		$this->checkState(false);
		if(!$newparent){
			$newparent = LDAP::sliceDn($newrdn,1);
		}
		if(!@ldap_rename($this->lfd, $this->dn, $newrdn, $newparent, $deleteoldrdn)){
			throw $this->newExeption();
		}
		$this->dn = $newrdn;
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Remove this node
	 */
	public function remove(){
		if(!@ldap_delete($this->lfd, $this->dn)){
			throw $this->newExeption();
		}
		$this->state = self::DELETED;
	}
	
	/**
	 * Get the node DN
	 */
	public function getDn(){
		return $this->dn;
	}
	
	/**
	 * Single level search with the current node dn as base DN
	 * 
	 * @param string $filter The search filter can be simple or advanced, using boolean operators in the format described in the LDAP documentation (see the Netscape Directory SDK for full information on filters)
	 * @param array $attributes (optionnal) An array of the required attributes, e.g. array("mail", "sn", "cn"). Note that the "dn" is always returned irrespective of which attributes types are requested
	 * @param int $attrsonly (optionnal) Should be set to 1 if only attribute types are wanted. If set to 0 both attributes types and attribute values are fetched which is the default behaviour
	 * @param int $sizelimit (optionnal) Enables you to limit the count of entries fetched. Setting this to 0 means no limit
	 * @param int $timelimit (optionnal) Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit
	 * @param int $deref (optionnal) Specifies how aliases should be handled during the search. It can be one of the following: LDAP_DEREF_NEVER - (default) aliases are never dereferenced
	 * @return LDAPNode
	 * @see ldap_list function in the PHP documentation
	 */
	public function children($filter="(objectclass=*)", $attributes=array(), $attrsonly=0, $sizelimit=0, $timelimit=0, $deref=LDAP_DEREF_NEVER){
		$rfd = @ldap_list($this->lfd, $this->dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
		if(!$rfd){
			throw $this->newExeption();
		}
		return new LDAPResult($this->lfd, $rfd);
	}
	
	/**
	 * Search LDAP tree with the current node dn as base DN
	 * 
	 * @param string $filter The search filter can be simple or advanced, using boolean operators in the format described in the LDAP documentation (see the Netscape Directory SDK for full information on filters)
	 * @param array $attributes (optionnal) An array of the required attributes, e.g. array("mail", "sn", "cn"). Note that the "dn" is always returned irrespective of which attributes types are requested
	 * @param int $attrsonly (optionnal) Should be set to 1 if only attribute types are wanted. If set to 0 both attributes types and attribute values are fetched which is the default behaviour
	 * @param int $sizelimit (optionnal) Enables you to limit the count of entries fetched. Setting this to 0 means no limit
	 * @param int $timelimit (optionnal) Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit
	 * @param int $deref (optionnal) Specifies how aliases should be handled during the search. It can be one of the following: LDAP_DEREF_NEVER - (default) aliases are never dereferenced
	 * @return LDAPResult
	 * @see ldap_search function in the PHP documentation
	 */
	public function find($filter="(objectclass=*)", $attributes=array(), $attrsonly=0, $sizelimit=0, $timelimit=0, $deref=LDAP_DEREF_NEVER){
		$rfd = @ldap_search($this->lfd, $this->dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
		if(!$rfd){
			throw $this->newExeption();
		}
		return new LDAPResult($this->lfd, $rfd);
	}
	
	/* ### GLOBALS METHODS ### */
	
	/**
	 * Binds to the LDAP directory with specified RDN and password
	 * 
	 * @param string $userdn (optional) User RDN
	 * @param string $passwd (optional) Password RDN
	 */
	public function bind($userdn = null, $passwd = null){
		if(!@ldap_bind($this->lfd, $userdn, $passwd)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Compare value of attribute found in node specified with DN
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param string $attribute The attribute name
	 * @param string $value The compared value
	 * @return boolean true if value matches otherwise returns false
	 */
	public function compare($dn, $attribute, $value){
		$res = @ldap_compare($this->lfd, $dn, $attribute, $value);
		if($res == -1){
			throw $this->newExeption();
		}
		return $res;
	}
	
	/**
	 * Delete an node from a directory
	 * 
	 * @param string $dn
	 * @return unknown_type
	 */
	public function deleteNode($dn){
		if(!@ldap_delete($this->lfd, $dn)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Get the current value for given option
	 * 
	 * @param int $option
	 * @param mixed $retval
	 * @return unknown_type
	 */
	public function getOption($option, &$retval){
		$ret = @ldap_get_option($this->lfd, $option, $retval);
		if(!$ret){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Unbinds from the LDAP directory
	 */
	public function unbind(){
		if(!@ldap_unbind($this->lfd)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Add entries in the LDAP directory. 
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param array $node An array that specifies the information about the node
	 * @see ldap_add function in the PHP documentation
	 */
	public function addNode($dn, $node){
		if(!@ldap_add($this->lfd, $dn, $node)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Single level search
	 * 
	 * @param string $base_dn The base DN for the directory
	 * @param string $filter The search filter can be simple or advanced, using boolean operators in the format described in the LDAP documentation (see the Netscape Directory SDK for full information on filters)
	 * @param array $attributes (optionnal) An array of the required attributes, e.g. array("mail", "sn", "cn"). Note that the "dn" is always returned irrespective of which attributes types are requested
	 * @param int $attrsonly (optionnal) Should be set to 1 if only attribute types are wanted. If set to 0 both attributes types and attribute values are fetched which is the default behaviour
	 * @param int $sizelimit (optionnal) Enables you to limit the count of entries fetched. Setting this to 0 means no limit
	 * @param int $timelimit (optionnal) Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit
	 * @param int $deref (optionnal) Specifies how aliases should be handled during the search. It can be one of the following: LDAP_DEREF_NEVER - (default) aliases are never dereferenced
	 * @return LDAPNode
	 * @see ldap_list function in the PHP documentation
	 */
	public function listNodes($base_dn, $filter="(objectclass=*)", $attributes=array(), $attrsonly=0, $sizelimit=0, $timelimit=0, $deref=LDAP_DEREF_NEVER){
		$rfd = @ldap_list($this->lfd, $base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
		if(!$rfd){
			throw $this->newExeption();
		}
		return new LDAPResult($this->lfd, $rfd);
	}
	
	/**
	 * Read an node
	 * 
	 * @param string $base_dn The base DN for the directory
	 * @param string $filter The search filter can be simple or advanced, using boolean operators in the format described in the LDAP documentation (see the Netscape Directory SDK for full information on filters)
	 * @param array $attributes (optionnal) An array of the required attributes, e.g. array("mail", "sn", "cn"). Note that the "dn" is always returned irrespective of which attributes types are requested
	 * @param int $attrsonly (optionnal) Should be set to 1 if only attribute types are wanted. If set to 0 both attributes types and attribute values are fetched which is the default behaviour
	 * @param int $sizelimit (optionnal) Enables you to limit the count of entries fetched. Setting this to 0 means no limit
	 * @param int $timelimit (optionnal) Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit
	 * @param int $deref (optionnal) Specifies how aliases should be handled during the search. It can be one of the following: LDAP_DEREF_NEVER - (default) aliases are never dereferenced
	 * @return LDAPNode
	 * @see ldap_read function in the PHP documentation
	 */
	public function readNode($base_dn, $filter="(objectclass=*)", $attributes=array(), $attrsonly=0, $sizelimit=0, $timelimit=0, $deref=LDAP_DEREF_NEVER){
		$rfd = @ldap_read($this->lfd, $base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
		if(!$rfd){
			throw $this->newExeption();
		}
		return new LDAPResult($this->lfd, $rfd);
	}
	
	/**
	 * Search LDAP tree
	 * 
	 * @param string $base_dn The base DN for the directory
	 * @param string $filter The search filter can be simple or advanced, using boolean operators in the format described in the LDAP documentation (see the Netscape Directory SDK for full information on filters)
	 * @param array $attributes (optionnal) An array of the required attributes, e.g. array("mail", "sn", "cn"). Note that the "dn" is always returned irrespective of which attributes types are requested
	 * @param int $attrsonly (optionnal) Should be set to 1 if only attribute types are wanted. If set to 0 both attributes types and attribute values are fetched which is the default behaviour
	 * @param int $sizelimit (optionnal) Enables you to limit the count of entries fetched. Setting this to 0 means no limit
	 * @param int $timelimit (optionnal) Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit
	 * @param int $deref (optionnal) Specifies how aliases should be handled during the search. It can be one of the following: LDAP_DEREF_NEVER - (default) aliases are never dereferenced
	 * @return LDAPNode
	 * @see ldap_search function in the PHP documentation
	 */
	public function searchNodes($base_dn, $filter="(objectclass=*)", $attributes=array(), $attrsonly=0, $sizelimit=0, $timelimit=0, $deref=LDAP_DEREF_NEVER){
		$rfd = @ldap_search($this->lfd, $base_dn, $filter, $attributes, $attrsonly, $sizelimit, $timelimit, $deref);
		if(!$rfd){
			throw $this->newExeption();
		}
		return new LDAPResult($this->lfd, $rfd);
	}
	
	/**
	 * Add attribute values to current attributes
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param array $node
	 */
	public function modAdd($dn, $node){
		if(!@ldap_mod_add($this->lfd, $dn, $node)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Delete attribute values from current attributes
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param array $node
	 */
	public function modDel($dn, $node){
		if(!@ldap_mod_del($this->lfd, $dn, $node)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Replace attribute values with new ones
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param array $node
	 */
	public function modReplace($dn, $node){
		if(!@ldap_mod_replace($this->lfd, $dn, $node)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Modyfy the name of an node
	 * 
	 * @param string $dn The distinguished name of an LDAP entity
	 * @param string $newrdn The new RDN
	 * @param string $newparent The new parent/superior node
	 * @param bool $deleteoldrdn If true the old RDN value(s) is removed, else the old RDN value(s) is retained as non-distinguished values of the node
	 */
	public function rename($dn, $newrdn, $newparent, $deleteoldrdn){
		if(!@ldap_rename($this->lfd, $dn, $newrdn, $newparent, $deleteoldrdn)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Set the value of the given option
	 * 
	 * @param int $option
	 * @param mixed $newval
	 */
	public function setOption($option, $newval){
		if(!@ldap_set_option($this->lfd, $option, $newval)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Start TLS
	 */
	public function startTls(){
		if(!@ldap_start_tls($this->lfd)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Get ldap connection ressource
	 * 
	 * @return ressource
	 */
	public final function getRessource(){
		return $this->lfd;
	}
	
	private $it;
}

?>