#lights0123's CMS
Welcome to the home page for lights0123's CMS! Follow the following steps to set it up.
#Instructions
##Installation
Simply place the contents of this in your web root. Apache is expected; you will need to translate the `.htaccess` to the webserver that you are using. It has not been tested if it works
somewhere else than the web root. SSL is optional. If you have it, the server will automatically detect that and use
[Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API). Don't forget to change the settings in `globals.php` and run `CMS.sql` on your database.
##Requirements
Your web host has to have several things for this CMS to work. You will have to have access to the `mysqli` PHP libraries. It is highly recommended that you use a VPS.
Personally, I use a VPS from vps.net running CentOS 7 and I'm compiling PHP with
`--with-apxs2=/usr/bin/apxs --with-mysql --with-mysqli --enable-libxml --enable-xmlwriter --enable-sockets --with-openssl --enable-mbstring`, using the default version of Apache from Yum.
Also, you will need a database running MySQL, MariaDB, etc. Apache should be able to read & parse `.htaccess` files.

If you don't have the required modules, they will display on the home page.
##Adding Content
###Pre-formatted
This CMS can automatically format documents to conform to your website's style guide. Under the `content` folder, you can create documents for your website. They follow the following guidelines:
```html
Title (1st line)
<div>
    Insert Content Here
</div>
```
You are allowed to use PHP to automatically create content. For example, this will produce what you expect:
```html
<?php
echo "This will be a title!";
$content="<div><p>More Content</p>";
echo $content;
?>
Mix it with HTML too!
</div>
```
Just make sure that you wrap the content with `<div>` and `</div>`, otherwise it will come out oddly.
###Unformatted
You may place any unformatted content under the `customcontent` folder. They include PHP scripts, CSS, fonts, images, javascript, etc.
##Settings
###Globals
This CMS contains various settings. Many variables are available in `scripts/globals.php`, which contain common configurations. Here is a guide to them.
```php
<?php

define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']); #This is the part where your content will be served from. There should be no need to change this.

$split = explode('/', DOCUMENT_ROOT);
unset($split[count($split) - 1]);
define("SETTINGS_ROOT", implode('/', $split));      #This is the settings root. You will place all of your files that contain sensitive information here.
                                                    #If you are unable to use this, see below for alternatives.

define("USES_HTTPS", (
		!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| $_SERVER['SERVER_PORT'] == 443
);                                                  #This configures whether the server uses HTTPS. This is currently only used to determine if
                                                    #Service Workers will be used.

define("DOMAIN_NAME", $_SERVER['SERVER_NAME']);     #This is the domain name. It is not used now, but in the future it will be used for mail.

define("BOX_CREATE_ERROR", 0);                      #This is just a global becuase there are no easy PHP enums. No need to change it.

define("COPYRIGHT_YEAR", 2016);                     #The year of copyright. Used to display the copyright in the footer.
define("COPYRIGHTER","Ben Schattinger");            #The person who owns the copyright.

define("DB_NAME", "CurlingCSC");                    #The name of the database.
define("DB_USERNAME", null);                        #The username for the database. If it is null, it will use a file from the SETTINGS_ROOT.
define("DB_PASSWORD", null);                        #The password for the database. If it is null, it will use a file from the SETTINGS_ROOT.
define("DB_HOST", null);                            #The host for the database. If it is null, it will use a file from the SETTINGS_ROOT.
```
Regarding the file from SETTINGS_ROOT:
A file in the directory below the web root can contain various files containing passwords. For now, there is just one for the data base. It has the format is below:
```javascript
{"host":"localhost","password":"Insert Password","username":"Insert Username"}
```
It should be saved as `db.json`.
###Menu
The menu is customizable. Under `scripts/main.php`, there is this on line 46:
```php
$menu = array(
	'/' => array('Home', null),
	'/data' => array('View Data', null),
	'/contact' => array('Contact', null),
	'/about' => array('About', null)
);
global $auth;
if ($auth>-1) {
	$menu['/signout'] = array('Sign Out', 'right');
	$menu['/settings'] = array('Settings', 'right');
} else {
	$menu['/login'] = array('Login', 'right');
	$menu['/signup'] = array('Sign Up', 'right');
}
```
The first few lines are configuring the menu items. The key is the path, the first element of the second array is the name, and the second element of the second array are for
custom classes. Below that are things for configuring the custom menu for the logging-in system.
###Footer
The footer is customizable, too. In `main.php`, there is a section that looks like this. Instructions on the settings are in the comments.
```php
function footer()
{
	$year = date("Y");                          #grab the current year
	if($year > COPYRIGHT_YEAR){                 #if the current year is later than the copyright date (from globals.php) then...
		$string=COPYRIGHT_YEAR." - " . $year;   #show it
	}else{                                      #else...
		$string=COPYRIGHT_YEAR;                 #just show the copyright year
	}
	$copyright = COPYRIGHTER;                   #grab the copyrighter
	                                            #Down below, you can change the footer.
	echo <<<EOF
<p>Copyright © $string $copyright. Some rights reserved by the <a href="/license">license</a>.</p>
<p><a href="/tos">Terms of Service</a> | <a href="/privacy">Privacy Policy</a></p>
EOF;

}
`