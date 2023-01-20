# dokuwiki-remotehostgroup
Make a dokuwiki ACL(access control list)-group based on a list of remote host names.
Based on the IPGroup-Plugin: https://www.dokuwiki.org/plugin:ipgroup

This plugin allows access control based on the remote host name. This in mainly intended for the situation in an intranet of an organization.

Let's assume you have the workstations `computer1.myorganization.org`, `computer2.myorganization.org` and `computer3.myorganization.org`. With this plugin you can add `computer1` and `computer2` to a list called `read-access`. In the Access Control Settings of Dokuwiki you can now for example grant read-access to this group, while other users without login (called @all) do not have access.

## Install
* Download the repo as zip file 
* Install manually via the Plugin Manager in Dokuwiki

## Config
* In the Dokuwiki configuration settings you have to specify the domain your remote hosts reside in, e.g. `myorganization.org`.