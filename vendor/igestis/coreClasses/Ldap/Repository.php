<?php

namespace Igestis\Ldap;

class Repository {
    /**
     * Ldap uri 
     * @example ldapi://serveur
     * @var string
     */
    protected $uri;
    /**
     * Distinguished name of the directory
     * @var string
     */
    protected $baseDn;
    /**
     * Ldap administrator login
     * @var string
     */
    protected $adminUser;
    /**
     * Ldap administrator password
     * @var string
     */
    protected $adminPassword;
    /**
     * Ldap filter to retrive the users with the admin account
     * @var string
     */
    protected $filter;
    /**
     * Restult of the ldap connexion
     * @var object
     */
    protected $ldapConnexion;

    /**
     * Factory to create the Repository configuration
     * @return self New Repository object
     */
    public static function newInstance() {
        return new self(
            \ConfigIgestisGlobalVars::ldapUri(),
            \ConfigIgestisGlobalVars::ldapBaseDn(),
            \ConfigIgestisGlobalVars::ldapAdmin(),
            \ConfigIgestisGlobalVars::ldapPassword(),
            \ConfigIgestisGlobalVars::ldapUserFilter()
        );
    }

    /**
     * Initialize the Repository object
     * @param string $uri           Ldap uri 
     * @param string $baseDn        Distinguished name of the directory
     * @param string $adminUser     Ldap administrator login
     * @param stirng $adminPassword Ldap administrator password
     * @param string $filter        Ldap filter to retrive the users with the admin account
     */
    public function __construct($uri, $baseDn, $adminUser, $adminPassword, $filter) {
        $this->uri = $uri;
        $this->baseDn = $baseDn;
        $this->adminUser = $adminUser;
        $this->adminPassword = $adminPassword;
        $this->filter = $filter;
    }

    /**
     * Start a ldap connexion
     * @return Ldap object
     */
    public function connect() {
        $this->ldapConnexion = ldap_connect($this->uri);
        $this->bind();

        return $this->ldapConnexion;
    }

    /**
     * Start a ldap bind
     * @param  string $login    Login to use for the bind
     * @param  string $password Password to use for the bind
     * @throws BindError if bind failed
     */
    public function bind($login=null, $password=null) {
        if (!$this->ldapConnexion) {
            $this->connect();
        }

        if ($login === null) {
            $login = $this->adminUser;
        }
        if ($password === null) {
            $password = $this->adminPassword;
        }
        
        if (!ldap_bind($this->ldapConnexion, $login, $password)) {
            throw new BindException();
        }
    }
}