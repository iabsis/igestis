<?php
/**
 * LDAP Result
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

class LDAPResult extends LDAPObject implements IteratorAggregate, Countable {
	/**
	 * LDAP Entries Object constructor
	 *
	 * @param ressource $lfd
	 * @param ressource $rfd
	 */
	public function __construct($lfd, $rfd){
		parent::__construct($lfd);
		$this->rfd = $rfd;
	}
	
	/**
	 * Internal use only
	 * Free the result
	 */
	public function __destruct(){
		@ldap_free_result($this->rfd);
	}
	
	/**
	 * Internal use only (foreach control structure)
	 * Get an result iterator
	 * 
	 * @return LDAPNodeIterator
	 */
	public function getIterator(){
		return new LDAPNodeIterator($this->lfd, $this->rfd);
	}
	
	/**
	 * Internal use only (count function)
	 * Get the number of elements
	 * 
	 * @return int
	 */
	public function count(){
		$ret = @ldap_count_entries($this->lfd, $this->rfd);
		if($ret === false){
			throw $this->newExeption();
		}
		return $ret;
	}
	
	/**
	 * Get all result entries
	 * 
	 * @return array a complete result information in a multi-dimensional array
	 */
	public function getEntries(){
		$ret = @ldap_get_entries($this->lfd, $this->rfd);
		if(!$ret){
			throw $this->newExeption();
		}
		return $ret;
	}
	
	/**
	 * Sort LDAP result entries
	 *
	 * @param string $sortfilter
	 */
	public function sort($sortfilter){
		if(!@ldap_sort($this->lfd, $this->rfd, $sortfilter)){
			throw $this->newExeption();
		}
	}
	
	/**
	 * Get the first node of a result
	 * 
	 * @return LDAPNode
	 */
	public function getFirstNode(){
		if(!$ret = @ldap_first_entry($this->lfd, $this->rfd)){
			throw new LDAPException("No result");
		}
		return new LDAPNode($this->lfd, $ret, @ldap_get_dn($this->lfd, $ret));
	}
}

?>