Introduction
------------

iGestis est un ERP conçu pour les petites entreprises. iGestis supporte la création de compte d'employés,
des clients et des founisseurs avec des comptes d'accès. iGestis peut aussi gérer plusieurs sociétés avec
une seule instance d'installation. Enfin, il est possible de configurer iGestis sur l'annuaire de votre
choix (OpenLDAP, Active Directory,...).

iGestis a besoin d'une base de donnée fonctionnelle pour stocker toutes les informations. Pour le moment, 
seul Mysql est supporté.

Installation
------------

Vous pouvez télécharger iGestis sur l'adresse https://github.com/olivierb2/igestis/releases. Ce guide vous
permet de configurer iGestis version 3.

Une fois copié sur le serveur, extraire le fichier avec votre gestionnaire d'archive préféré, ou utilisez la commande
`tar xfz igestis-xx.tar.gz` ou `unzip igestis-xx.zip`. Déplacez le dossier extrait dans un emplacement non accessble par
apache, par example **/usr/share/** ou **/opt**. Ensuite configurez apache avec un alias ou créez un lien symbolique afin
 de rendre le dossier **public** accessible par apache. Par exemple `ln -s /usr/share/igestis/public/ /var/www/igestis`.
Un Alias pour apache pourrait être **Alias /igestis /usr/share/igestis/public**.

Vous pouvez désormais utiliser votre navigateur préféré pour accéder à la de votre serveur comme **http://your_server/igestis**.
La première fois que vous ouvrirez la page, vous accéderez à une page de vérification de l'installation pour vérifier les
paramètres de votre serveur.

Configuration
-------------

Pendant la configuration, vous pourrez raffraichir la page pour vérifier ce que vous avez corrigé.

### Correction les problèmes de permissions

Avant toute chose, rendez le dossier **document** et **cache** inscriptible par le compte Apache. En fonction de votre
distribution Linux, faite un `chown www-data documents cache` ou `chown apache documents cache`.

### Créez une base MySQL

Créez une base de donnée vide sous MySQL. Depuis la ligne de commande, vous pouvez accéder au shell de Mysql avec :

	mysql -uroot -p

Et créez une base pour iGestis :

	create database igestis;
	create user 'igestis'@'localhost' identified by 'igestis1234';
	grant all privileges on igestis.* to 'igestis'@'localhost';
	quit

Remplacez igestis1234 par un mot de passe de votre choix (ou aléatoire).

### Create the config.ini file.

Then create a `config.ini` file by copying the `config.ini-template.ini` with the command 
`cp config/igestis/config.ini-template.ini config/igestis/config.ini`. Use your prefered text
editor and edit the file, by example `nano config/igestis/config.ini`. Change the value for the fourth 
first lines to adapt the MYSQL configuration you defined before.

	MYSQL_HOST = "localhost"
	MYSQL_DATABASE = "igestis"
	MYSQL_LOGIN = "igestis"
	MYSQL_PASSWORD = ""

Also change the **ENCRYPT_KEY** value by defining a random key. This key will be used to 
encrypt all sensitive data in the database that must be decipherable.

	ENCRYPT_KEY = "TBXvZGkFMiKoCsMY1AjlEuexFR6XMo"

In the case of the database is stollen without the configuration file, it will be no way to recover
the encrypted data.

### Install MySQL database.

Go back to the check install web page, check at least everything are green or orange, and then click 
on **Launch database update**.

### Ldap configuration (optionnal).

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

### Debian/Ubuntu installation

Installing iGestis on a Debian is pretty simple, first add the repository:

	wget http://open.iabsis.com/igestis.list -O /etc/apt/sources.list.d/igestis.list

Install the repository certificate:

	wget http://open.iabsis.com/open.iabsis.com.asc -O- | apt-key add -

And finally install the igestis core

	apt-get update && apt-get install igestis

iGestis will ask you regarding some question like the database and ldap configuration.

You can also easily search for modules available with

	apt-cache search igestis

### Upgrade from branch 2.0 to 3.0 on Debian/Ubuntu

iGestis can be easily updated from version 2.0 to 3.0. First ensure you have the testing branch enabled:

	nano /etc/apt/sources.list.d/igestis.list

And uncomment/add the line

	deb http://open.iabsis.com/debian testing main

Update the list:

	apt-get update

And install updates for the core and the installed modules:

	apt-get upgrade

> Note: iGestis v3 uses now an *ini* file instead the *php* for the configuration. Most of the configuration will be kept, but you have probably to check your settings inside the file */etc/igestis/config.ini*.
