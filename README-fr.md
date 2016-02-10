Introduction
------------

iGestis vous permet :
 * d'implémenter un PGI (Progiciel de Gestion Intégré) conçu et adapté pour les petites entreprises, collectivités, artisans, ...
 * d'utiliser un annuaire de votre choix (OpenLDAP, Active Directory,...)
 * de créer rapidement des comptes d'employés, de clients et de founisseurs avec des comptes d'accès à l'aide de son interface conviviale
 * de centraliser la gestion de plusieurs sociétés avec une seule instance d'installation.

iGestis a besoin d'une base de données fonctionnelle pour stocker toutes les informations.
Pour le moment, seul Mysql est supporté.

Installation avec le paquet Debian (version 2)
----------------------------------------------

Nous fournissons une installation simplifiée d'iGestis grâce au paquet Debian.

Ajoutez le fichier de dépôt

    wget http://open.iabsis.com/iabsis.list -O /etc/apt/sources.list.d/iabsis.list

Ajoutez le certificat

    wget http://open.iabsis.com/open.iabsis.com.asc -O- | apt-key add -

Rafraichissez la liste des paquets

    apt-get update

Et finalement installez iGestis

    apt-get install igestis

iGestis va vous poser quelques questions :

 * Choose the wanted authentication method: choose your actual directory (OpenLDAP or Active Directory). If none, choose **Internal**
 * Choose the admin account name : changez ce compte uniquement si celui proposé ne vous plait pas. Ce compte doit exister dans annuaire le cas échéant.
 * Specify the uris for the directory : remplacez ici par l'adresse de votre serveur. En cas d'utilisation de Samba4, gardez l'url ldapi proposée.
 * Specify the directory base tree : saisissez le nom de domaine FQDN (exemple: *domain.local*)
 * Specify the directory admin account for the directory : Le bind dn du compte Administrator. Par exemple **dc=admin,dc=domain,dc=local** pour OpenLDAP ou **Administrator@domain.local** pour Samba4 ou Active Directory.
 * Please specify the admin password for the directory : Le mot de passe de votre compte administrateur.
 * Create a user in iGestis when present in the directory ? Préférez **Yes**
 * Configure database for igestis with dbconfig-common? Yes
 * Password of the database's administrative user: Votre mot de passe Root pour Mysql.
 * MySQL application password for igestis: Laissez vide pour générer un mot de passe aléatoire
 * Web server to reconfigure automatically : Gardez coché *apache2* pour une configuration automatique de Apache2.

Ouvrez votre navigateur, saisissez l'adresse de votre serveur et ajoutez **/igestis**, par exemple : http://ip_de_votre_serveur/igestis

Installation avec le paquet Debian (version 3)
----------------------------------------------

Nous fournissons une installation simplifiée d'iGestis grâce au paquet Debian.

Ajoutez le fichier de dépôt

    wget http://open.iabsis.com/iabsis.list -O /etc/apt/sources.list.d/iabsis.list

Modifiez le fichier téléchargé avec

    nano /etc/apt/sources.list.d/iabsis.list

Et décommentez la ligne

    deb http://open.iabsis.com/debian testing main

Ajoutez le certificat

    wget http://open.iabsis.com/open.iabsis.com.asc -O- | apt-key add -

Rafraichissez la liste des paquets

    apt-get update

Et finalement installez iGestis

    apt-get install igestis

iGestis va vous poser quelques questions.

 * Choose the wanted authentication method: choose your actual directory (OpenLDAP or Active Directory). If none, choose **Internal**
 * Choose the admin account name : changez ce compte uniquement si celui proposé ne vous plait pas. Ce compte doit exister dans annuaire le cas échéant.
 * Specify the uris for the directory : remplacez ici par l'adresse de votre serveur. En cas d'utilisation de Samba4, gardez l'url ldapi proposée.
 * Specify the directory base tree : saisissez le nom de domaine FQDN (exemple: *domain.local*)
 * Specify the directory admin account for the directory : Le bind dn du compte Administrator. Par exemple **dc=admin,dc=domain,dc=local** pour OpenLDAP ou **Administrator@domain.local** pour Samba4 ou Active Directory.
 * Please specify the admin password for the directory : Le mot de passe de votre compte administrateur.
 * Create a user in iGestis when present in the directory ? Préférez **Yes**
 * Configure database for igestis with dbconfig-common? Yes
 * Password of the database's administrative user: Votre mot de passe Root pour Mysql.
 * MySQL application password for igestis: Laissez vide pour générer un mot de passe aléatoire
 * Web server to reconfigure automatically : Gardez coché *apache2* pour une configuration automatique de Apache2.

Ouvrez votre navigateur, saisissez l'adresse de votre serveur et ajoutez **/igestis**, par exemple : http://ip_de_votre_serveur/igestis

Installation Manuelle
---------------------

Vous pouvez télécharger iGestis sur l'adresse https://github.com/olivierb2/igestis/releases
Ce guide vous permet de configurer iGestis version 3.

Une fois copié sur le serveur, extraire le fichier avec votre gestionnaire d'archive préféré ou utilisez la commande
`tar xfz igestis-xx.tar.gz` ou `unzip igestis-xx.zip`
Déplacez le dossier extrait dans un emplacement non accessible par apache, par example **/usr/share/** ou **/opt**.
Configurez apache avec un alias ou créez un lien symbolique afin de rendre le dossier **public** accessible par apache.
Par exemple `ln -s /usr/share/igestis/public/ /var/www/igestis`
Un Alias pour apache pourrait être **Alias /igestis /usr/share/igestis/public**

Vous pouvez désormais utiliser votre navigateur préféré afin d'accéder à l'interface web de votre serveur : **http://ip_de_votre_serveur/igestis**
Lors de cette première connexion, vous accéderez à une page de vérification de l'installation permettant de consulter les paramètres de votre serveur.

### Configuration

Pendant la configuration, vous pourrez rafraichir la page afin de vérifier ce que vous avez modifié.

#### Correction des problèmes de permissions

Dans un premier temps, autorisez les droits en écriture par le compte Apache sur les répertoires **documents** et **cache**.
En fonction de votre distribution Linux, faites un `chown www-data documents cache` ou `chown apache documents cache`.

#### Créez une base MySQL

Créez une base de données vide sous MySQL. Depuis la ligne de commande, vous pouvez accéder au shell de Mysql avec :

	mysql -uroot -p

Et créez une base pour iGestis :

	create database igestis;
	create user 'igestis'@'localhost' identified by 'igestis1234';
	grant all privileges on igestis.* to 'igestis'@'localhost';
	quit

Remplacez igestis1234 par un mot de passe de votre choix (ou aléatoire).

#### Créez le fichier config.ini.

Créez ensuite un fichier `config.ini` en copiant le fichier `config.ini-template.ini` grâce à la commande
`cp config/igestis/config.ini-template.ini config/igestis/config.ini`. Utilisez votre éditeur de texte préféré
et modifiez le fichier, comme par exemple `nano config/igestis/config.ini`. Changez les valeurs des quatre premières lignes
afin de l'adapter à votre configuration définie précédemment.

	MYSQL_HOST = "localhost"
	MYSQL_DATABASE = "igestis"
	MYSQL_LOGIN = "igestis"
	MYSQL_PASSWORD = ""

Pensez également à changer la variable **ENCRYPT_KEY** en générant une clef aléatoire. Cette clef sera utilisée pour stocker
les données sensibles dans votre base de données tout en restant déchiffrable.

	ENCRYPT_KEY = "TBXvZGkFMiKoCsMY1AjlEuexFR6XMo"

Dans ce cas, même si la base de données est volée, ces données resteront cryptées et indéchiffrables sans la clef de décryptage.

#### Installer la base de données Mysql.

De retour dans la page de vérification, vérifiez une dernière fois que tout est vert ou orange et cliquez sur **Launch database update**.

#### Configuration Ldap (optionnelle).

In the of you would like to let user use their **Active directory** or **OpenLDAP** account, you can 
configure iGestis to use and manage the Users LDAP information.

Uncomment the values you want to use. The minimum that you need to define are :

	USE_LDAP = true
	LDAP_URIS = "ldap://localhost:389"
	LDAP_BASE = "dc=example,dc=local"
	LDAP_ADMIN = "cn=admin,dc=example,dc=local"
	LDAP_PASSWORD = ""
	LDAP_USERS_OU = "ou=Users,dc=example,dc=local"
	LDAP_CUSTOMERS_OU = "ou=Customers,dc=example,dc=local"
	LDAP_SUPPLIERS_OU = "ou=Suppliers,dc=example,dc=local"

* **LDAP_ADMIN** must be a LDAP account with write privilage on the directory. Don't forgot to define the 
**LDAP_PASSWORD** as well.

* **LDAP\_USERS\_OU** is the location in the directory where the new employee will be written **LDAP\_CUSTOMERS\_OU** 
is for new customers and **LDAP\_SUPPLIERS\_OU** is for new suppliers.

Others optionnal values can be configured :

* **LDAP\_AUTO\_IMPORT\_USER** allow a user existing in the directory but not in iGestis to be created on fly in 
iGestis when the user tries to login in iGestis. Note the employee will be automatically assigned to the first company available.

* **LDAP_READONLY** (not implemented yet) restricts iGestis for writting in the directory. The only way to 
create new employees will be using a third party tool.

* **LDAP_SCHEMA** (not implemented yet) lets you choose the schema you want to enable and use with your 
directory.

* **LDAP\_AD\_MODE** (deprecated) define if the directory is directory is an **Active Directory** or not.

* **LDAP\_USER\_RDN** by default, iGestis create an employee with "uid=%username%". 
But Active Directory use the convention "cn=%username%".


Installation de module (version 3)
----------------------------------

iGestis est fourni avec plusieurs modules optionnels

|                                                                               | iGestis v2            | iGestis v3 | Package name       |
|-------------------------------------------------------------------------------|-----------------------|------------|--------------------|
| Commercial: let you manage your quotation, invoicing, orders and accounting   | Yes (but not updated) | Yes        | igestis-commercial |
| Ajaxplorer: Access remotely to your files (not available for iGestis 3 yet    | Yes                   | Not yet    | igestis-ajaxplorer |
| Roundcube: Display and manage your mail within iGestis                        | Yes                   | Yes        | igestis-roundcube  |
| OpenChange: Extend Active Directory attributes to manage OpenChange.          | Yes                   | Not yet    | igestis-roundcube  |
| Samba: Extend OpenLDAP attributes to manage Samba 3/4.                        | Yes                   | Yes        | igestis-roundcube  |
| ServerMgmt: Easily setup your folder access right within iGestis.             | No                    | Yes        | igestis-roundcube  |

Dépannage
---------

Dans le cas ou vous rencontreriez un problème avec iGestis, suivez la procédure suivante.

iGestis v2
----------

Activez le mode debuggage avec la commande suivante :

    nano /usr/share/igestis/index.php

Et modifiez la ligne

    define("DEBUG_MODE", false);

En 

    define("DEBUG_MODE", true);

Ouvrez la page web et tentez à nouveau l'étape ayant échouée, vous devriez avoir maintenant un message plus détaillé du problème.

### iGestis v3

Activez le mode debuggage avec la commande suivante :

    nano /etc/igestis/config.ini

Et ensuite ajoutez la ligne suivante :

    DEBUG_MODE=true

Ouvrez la page web et tentez à nouveau l'étape ayant échouée, vous devriez avoir maintenant un message plus détaillé du problème.

