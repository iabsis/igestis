<?php
/**
 * LDAP object.
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

class LDAPObject {
	/**
	 * LDAP Object constructor
	 *
	 * @param ressource $lfd LDAP 
	 */
	public function __construct($lfd){
		$this->lfd = $lfd;
	}
	
	/**
	 * Return a LDAPException containing the last ldap error.
	 * 
	 * @return LDAPException
	 */
	protected final function newExeption(){
		return new LDAPException(@ldap_error($this->lfd), @ldap_errno($this->lfd));
	}
	
	public function getConnectionRessource(){
		return $this->lfd;
	}
	
	/**
	 * LDAP connection ressource
	 * @var ressource
	 */
	protected $lfd;
}

?>