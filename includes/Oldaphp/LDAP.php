<?php
/**
 * Module LDAP.
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

class LDAP extends LDAPNode {
	/**
	 * LDAP Object constructor
	 * @param string $url server url
	 * @param string $basedn base DN
	 * @param integer $pv rotocol version (optional)
	 */
	public function __construct($url, $basedn=null, $pv = 3){
            parent::__construct(null,null,$basedn);
            $this->lfd = @ldap_connect($url);
            $this->state = self::OBSOLETE;

            if(!$this->lfd){
                    throw new LDAPException(sprintf(_("Connection to %s failed"), $url));
            }

            if($pv != null) {
                @ldap_set_option($this->lfd, LDAP_OPT_PROTOCOL_VERSION, $pv);
            }

            @ldap_set_option($this->lfd, LDAP_OPT_REFERRALS, 0);
	}
	
	/**
	 * Set the default base DN
	 * 
	 * @param unknown_type $basedn
	 */
	public function setBaseDn($basedn){
		$this->dn = $basedn;
	}
	
	/**
	 * Convert a DN in UFN format (User Friendly Naming)
	 * 
	 * @param string $dn
	 */
	public static function dn2fdn($dn){
		return ldap_dn2ufn($dn);
	}
	
	/**
	 * Split the DN string in array(name => type) format
	 * 
	 * @param string $dn the dn
	 * @return array splited format
	 */
	public static function splitDn($dn){
		$dns = array();
		$ret = array();
		$nb = preg_match_all("/\\s*([^,=]*[^,=\\s]+)\\s*=\\s*([^,=]*[^,=\\s]+)\\s*/",$dn,$ret);
		for($i=0; $i<$nb; $i++){
			$dns[$ret[1][$i]] = $ret[2][$i];
		}
		return $dns;
	}
	
	/**
	 * Get a part of the DN
	 * 
	 * @param string $dn the DN
	 * @param int $offset
	 * @param int $length
	 */
	public static function sliceDn($dn, $offset, $length=null){
		$dns = array();
		$ret = array();
		$nb = preg_match_all("/\\s*([^,=]*[^,=\\s]+)\\s*=\\s*([^,=]*[^,=\\s]+)\\s*/",$dn,$ret);
		for($i=0; $i<$nb; $i++){
			$dns[] = $ret[1][$i] ."=". $ret[2][$i];
		}
		if($length === null){
			$dns = array_slice($dns, $offset);
		}else{
			$dns = array_slice($dns, $offset, $length);
		}
		return join(', ',$dns);
	}
	
	/**
	 * Hash a plaintext password ie before push it into the LDAP schema
	 * 
	 * @param string $passwd plain text password to hash
	 * @param int $method hash method (LDAP::SSHA/LDAP::NONE)
	 */
	public static function hashPasswd($passwd, $method=self::SSHA){
		switch($method){
			case self::NONE:
				return $passwd;
			case self::MD5:
				throw new LDAPException("This hash method is not implemented yet");
			case self::SSHA:
				$salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
				return "{SSHA}" . base64_encode(pack("H*", sha1($passwd . $salt)) . $salt);
			default:
				throw new LDAPException("This hash method is not supported");
		}
	}
	
	const NONE = 0;
	const MD5 = 1;
	const SSHA = 2;
}
