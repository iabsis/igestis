<?php
/**
 * LDAP entity.
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

abstract class LDAPEntity extends LDAPObject {
	/**
	 * LDAP Entity constructor
	 *
	 * @param ressource $lfd
	 * @param ressource $efd
	 * @param string $dn
	 */
	public function __construct($lfd,$efd,$dn){
		parent::__construct($lfd);
		$this->efd = $efd;
		$this->dn = $dn;
		$this->state = self::UPTODATE;
	}
	
	/**
	 * Remove this entity
	 */
	public abstract function remove();
	
	/**
	 * Reload this entity (ie: after an update)
	 */
	public abstract function reload();
	
	/**
	 * Enable/disable the auto reload mechanism
	 * @param boolean $autoReload true to enable, otherwise false
	 */
	function setAutoReload($autoReload) {
		self::$autoReload = $autoReload;
	}

	/**
	 * Is the auto reload mechanism is enabled
	 * @return boolean true if is enabled, otherwise false
	 */
	function getAutoReload() {
		return self::$autoReload;
	}
	
	/**
	 * Get the entity state
	 * 
	 * @return int
	 */
	protected function getState(){
		return $this->state;
	}
	
	/**
	 * Check the entity state
	 */
	protected function checkState($forRead=true){
		switch($this->state){
			case self::UPTODATE:
				break;
			case self::OBSOLETE:
				if($forRead){
					if(!self::$autoReload){
						throw new LDAPException("This Entity is obsolete : see LDAPEntity::reload()");
					}
					$this->reload();
				}
				break;
			case self::DELETED:
				throw new LDAPException("This Entity is deleted");
				break;
		}
	}
	
	/**
	 * Entity state (UPTODATE/OBSOLETE/DELETED)
	 * @var int
	 */
	protected $state;
	
	protected $efd;
	protected $dn;
	
	const UPTODATE = 1;
	const OBSOLETE = 2;
	const DELETED = 0;
	
	/**
	 * Is the auto reload mechanism is enabled
	 * @var boolean
	 */
	protected static $autoReload = true;
}

?>