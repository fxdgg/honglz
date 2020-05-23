<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'production';
if(ENVIRONMENT == "development")
{
    $active_group = 'dev';
}
$active_record = TRUE;

$db['dev']['hostname'] = 'localhost';
$db['dev']['username'] = 'root';
$db['dev']['password'] = '';
$db['dev']['database'] = 'exunion_hlz_dev';#bailaohui_dev
$db['dev']['dbdriver'] = 'mysql';
$db['dev']['dbprefix'] = '';
$db['dev']['pconnect'] = TRUE;
$db['dev']['db_debug'] = false;
$db['dev']['cache_on'] = FALSE;
$db['dev']['cachedir'] = '';
$db['dev']['char_set'] = 'utf8';
$db['dev']['dbcollat'] = 'utf8_general_ci';
$db['dev']['dbcollat'] = 'utf8_unicode_ci';
$db['dev']['swap_pre'] = '';
$db['dev']['autoinit'] = TRUE;
$db['dev']['stricton'] = FALSE;


$db['production']['hostname'] = 'localhost';
$db['production']['username'] = 'root';
$db['production']['password'] = '';
$db['production']['database'] = 'exunion_hlz';#bailaohui
$db['production']['dbdriver'] = 'mysql';
$db['production']['dbprefix'] = '';
$db['production']['pconnect'] = TRUE;
$db['production']['db_debug'] = false;
$db['production']['cache_on'] = FALSE;
$db['production']['cachedir'] = '';
$db['production']['char_set'] = 'utf8';
$db['production']['dbcollat'] = 'utf8_general_ci';
$db['production']['dbcollat'] = 'utf8_unicode_ci';
$db['production']['swap_pre'] = '';
$db['production']['autoinit'] = TRUE;
$db['production']['stricton'] = FALSE;


$db['jd']['hostname'] = 'localhost';
$db['jd']['username'] = 'root';
$db['jd']['password'] = '';
$db['jd']['database'] = 'jd';
$db['jd']['dbdriver'] = 'mysql';
$db['jd']['dbprefix'] = '';
$db['jd']['pconnect'] = TRUE;
$db['jd']['db_debug'] = false;
$db['jd']['cache_on'] = FALSE;
$db['jd']['cachedir'] = '';
$db['jd']['char_set'] = 'utf8';
$db['jd']['dbcollat'] = 'utf8_general_ci';
$db['jd']['dbcollat'] = 'utf8_unicode_ci';
$db['jd']['swap_pre'] = '';
$db['jd']['autoinit'] = TRUE;
$db['jd']['stricton'] = FALSE;

$db['jobanalyses']['hostname'] = 'localhost';
$db['jobanalyses']['username'] = 'root';
$db['jobanalyses']['password'] = '';
$db['jobanalyses']['database'] = 'jobanalyses';
$db['jobanalyses']['dbdriver'] = 'mysql';
$db['jobanalyses']['dbprefix'] = '';
$db['jobanalyses']['pconnect'] = TRUE;
$db['jobanalyses']['db_debug'] = false;
$db['jobanalyses']['cache_on'] = FALSE;
$db['jobanalyses']['cachedir'] = '';
$db['jobanalyses']['char_set'] = 'utf8';
$db['jobanalyses']['dbcollat'] = 'utf8_general_ci';
$db['jobanalyses']['dbcollat'] = 'utf8_unicode_ci';
$db['jobanalyses']['swap_pre'] = '';
$db['jobanalyses']['autoinit'] = TRUE;
$db['jobanalyses']['stricton'] = FALSE;

$db['spider_resume']['hostname'] = '120.27.79.89';
$db['spider_resume']['username'] = 'spider';
$db['spider_resume']['password'] = '123321';
$db['spider_resume']['database'] = 'resume';
$db['spider_resume']['dbdriver'] = 'mysql';
$db['spider_resume']['dbprefix'] = '';
$db['spider_resume']['pconnect'] = TRUE;
$db['spider_resume']['db_debug'] = false;
$db['spider_resume']['cache_on'] = FALSE;
$db['spider_resume']['cachedir'] = '';
$db['spider_resume']['char_set'] = 'utf8';
$db['spider_resume']['dbcollat'] = 'utf8_general_ci';
$db['spider_resume']['dbcollat'] = 'utf8_unicode_ci';
$db['spider_resume']['swap_pre'] = '';
$db['spider_resume']['autoinit'] = TRUE;
$db['spider_resume']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */
