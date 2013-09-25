fake-webauth
============

Fakes a UCI WebAuth service, useful for testing along with AWTWebAuthBundle

Install
------
1. Clone the repository
2. Run composer install: `composer.phar install`
3. Make the `web` directory accessible

Configure with AWTWebAuthBundle
------
Set the parameters:


| Name | Value |
|------|-------|
| webauth.url_for_login | `http://<server_url>/index.php/login` |
| webauth.url_for_logout | `http://<server_url>/index.php/logout` |
| webauth.url_for_check | `https://<server_url>/index.php/check` |
| webauth.bypass_ip_check | `true` |
