iGestis installation
====================

Introduction
------------

iGestis is an ERP designed for small companies. iGestis can support the account creation for employees, 
customers and providers with account access. iGestis can also manage multiple companies with one instance. 
You can also connect iGestis to a directory of your choice (OpenLDAP, Active Directory,...).

iGestis needs to have a working database installation to store all information. For the moment, 
only Mysql is supported.

Installation
------------

You can get iGestis on https://github.com/olivierb2/igestis/releases. This guide assume you have at least 
iGestis version 3.0.

Once copied to your server, extract the file with your prefered archive manager, or use the command 
`tar xfz igestis-xx.tar.gz` or `unzip igestis-xx.zip`. Move the extracted folder somewhere not accessible
by apache, by example **/usr/share/** or **/opt**. Then configure apache with an alias or make a symbolic link
to let the **public** folder accessible by apache. By example `ln -s /usr/share/igestis/public/ /var/www/igestis`.
Apache alias can be **Alias /igestis /usr/share/igestis/public**.

You can already use your prefered web browser and access to the folder something like **http://your_server/igestis**.
The first time you open this page, you will get an install check page, to check your server settings.

Configuration
-------------

During the configuration, you can anytime refresh the webpage to check what you done.

### Fix permission right

First off all, make the folder **document** and **cache** writeable for Apache username. Depending of your 
distribution, let do a `chown www-data documents cache` or `chown apache documents cache`.

### Create the database in MySQL

Create an empty database under MySQL. From command line, you can access to the prompt with the command.

	mysql -uroot -p

Then create an empty database and user dedicated for iGestis.

	create database igestis;
	create user 'igestis'@'localhost' identified by 'igestis1234';
	grant all privileges on igestis.* to 'igestis'@'localhost';
	quit

Replace igestis1234 by the password you want.

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
