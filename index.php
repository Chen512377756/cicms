<?php if(!defined('BASEPATH')){

//ini_set('display_errors','On');//for debug

/*
|---------------------------------------------------------------
| PHP ERROR REPORTING LEVEL
|---------------------------------------------------------------
|
| By default CI runs with error reporting set to ALL.  For security
| reasons you are encouraged to change this when your site goes live.
| For more info visit:  http://www.php.net/error_reporting
|
*/
	error_reporting(E_ALL);//for debug
	//error_reporting(E_ALL ^ E_NOTICE);//for public

/*
|---------------------------------------------------------------
| SYSTEM FOLDER NAME
|---------------------------------------------------------------
|
| This variable must contain the name of your "system" folder.
| Include the path if the folder is not in the same  directory
| as this file.
|
| NO TRAILING SLASH!
|
*/
	$system_folder = dirname(__FILE__)."/system";

/*
|---------------------------------------------------------------
| APPLICATION FOLDER NAME
|---------------------------------------------------------------
|
| If you want this front controller to use a different "application"
| folder then the default one you can set its name here. The folder 
| can also be renamed or relocated anywhere on your server.
| For more info please see the user guide:
| http://codeigniter.com/user_guide/general/managing_apps.html
|
|
| NO TRAILING SLASH!
|
*/
	$application_folder = dirname(__FILE__)."/application";

/*
|===============================================================
| END OF USER CONFIGURABLE SETTINGS
|===============================================================
*/


/*
|---------------------------------------------------------------
| DEFINE APPLICATION CONSTANTS
|---------------------------------------------------------------
|
| EXT		- The file extension.  Typically ".php"
| SELF		- The name of THIS file (typically "index.php")
| FCPATH	- The full server path to THIS file
| BASEPATH	- The full server path to the "system" folder
| APPPATH	- The full server path to the "application" folder
|
*/
define('EXT', '.php');
defined('SELF')||define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
defined('FCPATH')?define('SUBPATH',strtr(substr(FCPATH,strlen(dirname(__FILE__))+1),'\\','/'))
	:define('FCPATH',str_replace(SELF,'',__FILE__));
define('BASEPATH', str_replace("\\", "/", $system_folder).'/');
define('APPPATH', $application_folder.'/');

/*
 * --------------------------------------------------------------------
 * LET 404 ENTRY TO MAKE IT FURTHER
 * --------------------------------------------------------------------
 */
	if(defined('IN404'))return;

/*
|---------------------------------------------------------------
| LOAD THE FRONT CONTROLLER
|---------------------------------------------------------------
|
| And away we go...
|
*/
require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;

/*
 * --------------------------------------------------------------------
 * STOP THIS REQUEST HERE DUE TO THE COOPERATE MODE
 * --------------------------------------------------------------------
 */
exit;

}
/* End of file index.php */
/* Location: ./index.php */