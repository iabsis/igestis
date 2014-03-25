
Table of content
================

* General iGestis installation
* LDAP synchronisation

General iGestis installation
============================

iGestis is an ERP designed for small companies. iGestis can support the account creation
throught employees creation, customers creation with account access. iGestis can also 
manage multiple companies with one instance.

iGestis needs to have a working database installation to store all information. For
the moment, only Mysql is supported but could be easily be migrated to another as
iGestis use doctrine for the database requests.

Debian/Ubuntu installation
--------------------------

iGestis was originaly designed for Debian, a repository is available to make the
installation.

Please read information on http://www.igestis.org/installer-igestis/

Manual installation
-------------------

Please read information on http://www.igestis.org/installer-igestis/

LDAP synchronisation
====================

iGestis is able to synchronise the users informations with a LDAP service.
This can be usefull to have a centralised password system and connect others
third parties software.

TODO : Complete how iGestis works with LDAP.
