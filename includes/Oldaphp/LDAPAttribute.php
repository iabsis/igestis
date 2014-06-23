<?php
/**
 * LDAP Attribute
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
 * LDAP Attribute
 *
 */
class LDAPAttribute extends LDAPEntity implements IteratorAggregate, Countable {
	/**
	 * LDAP Attribute Object constructor
	 *
	 * @param ressource $lfd
	 * @param ressource $efd
	 * @param string $dn
	 * @param string $eattr
	 * @param boolean $isBin
	 */
	public function __construct($lfd, $efd, $dn, $attr, $isBin=false){
		parent::__construct($lfd,$efd,$dn);
		$this->attr = $attr;
		$this->isBin = $isBin;
		$this->values = null;
	}
	
	/**
	 * Populate the internal value array in the specified format
	 * 
	 * @param boolean $isBin true for binary format or false for string format
	 */
	protected function populate($isBin=false){
		if(func_num_args() != 1){
			$this->isBin = $isBin;
		}
		if($this->isBin){
			$this->values = @ldap_get_values_len($this->lfd, $this->efd, $this->attr);
		}else{
			$this->values = @ldap_get_values($this->lfd, $this->efd, $this->attr);
		}
		if(!$this->values){
			throw $this->newExeption();
		}
		unset($this->values['count']);
	}
	
	/**
	 * Set the good format of the values
	 * 
	 * @param boolean $isBin true for binary format or false for string format
	 */
	public function setType($isBin){
		$this->isBin = $isBin;
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Get an values iterator
	 * 
	 * @return ArrayIterator
	 */
	public function getIterator(){
		$this->checkState();
		if(!$this->values){
			$this->populate();
		}
		return new ArrayIterator($this->values);
	}
	
	/**
	 * Get values
	 * 
	 * @param boolean $isBin true for binary format or false for string format
	 * @return array of strings
	 */
	public function getValues($isBin=false){
		$this->checkState();
		if(func_num_args() != 1){
			$this->isBin = $isBin;
		}
		if(!$this->values){
			$this->populate();
		}
		return $this->values;
	}
	
	/**
	 * Reload this attribute (ie: after an update)
	 */
	public function reload(){
		$ret = @ldap_read($this->lfd,$this->dn,"(objectclass=*)",array($this->attr));
		if(!$ret){
			throw $this->newExeption();
		}
		$this->efd = @ldap_first_entry($this->lfd,$ret);
		if(!$this->lfd){
			throw $this->newExeption();
		}
		$this->dn = @ldap_get_dn($this->lfd, $this->efd);
		$this->values = null;
		$this->state = self::UPTODATE;
	}
	
	/**
	 * 
	 */
	public function count(){
		$this->checkState();
		if(!$this->values){
			$this->populate();
		}
		return count($this->values);
	}
	
	/**
	 * Set the values
	 * 
	 */
	public function setValues($values){
		$this->checkState(false);
		if(!@ldap_mod_replace($this->lfd, $this->dn, array($this->attr => $values))){
			throw $this->newExeption();
		}
		$this->state = self::OBSOLETE;
	}
	
	/**
	 * Remove this attribute
	 * 
	 */
	public function remove(){
		if(!@ldap_mod_del($this->lfd, $this->dn, $this->attr)){
			throw $this->newExeption();
		}
		$this->state = self::DELETED;
	}
	
	/**
	 * Internal use only (string object convertion)
	 */
	public function __toString(){
		try{
			$this->checkState();
			if(!$this->values){
				$this->populate();
			}
			return join(", ",$this->values);
		}catch(Exception $e){
			return "###ERROR isBin=".($this->isBin?"true":"false")."### ".$e->getMessage();
		}
	}
	
	private $attr;
	private $values;
	private $isBin;
}
;

?>