<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database driver. e.g.: mysqli.
|			Currently supported:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Query Builder class
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
|	['encrypt']  Whether or not to use an encrypted connection.
|
|			'mysql' (deprecated), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE
|			'mysqli' and 'pdo/mysql' drivers accept an array with the following options:
|
|				'ssl_key'    - Path to the private key file
|				'ssl_cert'   - Path to the public key certificate file
|				'ssl_ca'     - Path to the certificate authority file
|				'ssl_capath' - Path to a directory containing trusted CA certificates in PEM format
|				'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':')
|				'ssl_verify' - TRUE/FALSE; Whether verify the server certificate or not ('mysqli' only)
|
|	['compress'] Whether or not to use client compression (MySQL only)
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|	['ssl_options']	Used to set various SSL options that can be used when making SSL connections.
|	['failover'] array - A array with 0 or more data for connections if the main should fail.
|	['save_queries'] TRUE/FALSE - Whether to "save" all executed queries.
| 				NOTE: Disabling this will also effectively disable both
| 				$this->db->last_query() and profiling of DB queries.
| 				When you run a query, with this setting set to TRUE (default),
| 				CodeIgniter will store the SQL statement for debugging purposes.
| 				However, this may cause high memory usage, especially if you run
| 				a lot of SQL queries ... disable this to avoid that problem.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $query_builder variables lets you determine whether or not to load
| the query builder class.
*/
$active_group = 'master';
$query_builder = TRUE;
$master = K_SITE_KEY;
//Settings database master production
$db['master']['hostname'] = 'rds-numerica.c2fv9xvemqo1.us-east-1.rds.amazonaws.com';
$db['master']['username'] = '';
$db['master']['password'] = '';
$db['master']['database'] = '';
switch ($master) {
	case 'alkhorayef':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_alkhorayef';
		break;
	case 'ngec':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_ngec';
		break;
	case 'sims':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_sims';
		break;
	case 'air':
	case 'trienergy':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_trienergy';
		break;
	case 'wm':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_alkhorayef';
		break;
	case 'vetra':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_vetra';
		break;
	case 'solinpet':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_solinpet';
		break;
	case 'novomet':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_novomet';
		break;
	case 'gte':
		$db['master']['username'] = 'wellmo5';
		$db['master']['password'] = 'M4ch0rr1t0';
		$db['master']['database'] = 'wellmo5_gte';
		break;
	default:
		exit('The application environment is not set correctly (db).');
}
$db['master']['dbdriver'] = 'mysqli';
$db['master']['dbprefix'] = '';
$db['master']['pconnect'] = FALSE;
$db['master']['db_debug'] = TRUE;
$db['master']['cache_on'] = FALSE;
$db['master']['cachedir'] = '';
$db['master']['char_set'] = 'utf8';
$db['master']['dbcollat'] = 'utf8_general_ci';
$db['master']['swap_pre'] = '';
$db['master']['autoinit'] = FALSE;
$db['master']['stricton'] = FALSE;
//Settings database replica production
$db['slave']['hostname'] = 'rds-numerica-replica.cdelr72vk4xc.us-west-2.rds.amazonaws.com';
$db['slave']['username'] = '';
$db['slave']['password'] = '';
$db['slave']['database'] = '';
switch ($master) {
	case 'alkhorayef':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_alkhorayef';
		break;
	case 'ngec':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_ngec';
		break;
	case 'sims':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_sims';
		break;
	case 'air':
	case 'trienergy':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_trienergy';
		break;
	case 'wm':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_alkhorayef';
		break;
	case 'vetra':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_vetra';
		break;
	case 'solinpet':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_solinpet';
		break;
	case 'novomet':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_novomet';
		break;
	case 'gte':
		$db['slave']['username'] = 'wellmo5';
		$db['slave']['password'] = 'M4ch0rr1t0';
		$db['slave']['database'] = 'wellmo5_gte';
		break;
	default:
		exit('The application environment is not set correctly (db).');
}
$db['slave']['dbdriver'] = 'mysqli';
$db['slave']['dbprefix'] = '';
$db['slave']['pconnect'] = FALSE;
$db['slave']['db_debug'] = TRUE;
$db['slave']['cache_on'] = FALSE;
$db['slave']['cachedir'] = '';
$db['slave']['char_set'] = 'utf8';
$db['slave']['dbcollat'] = 'utf8_general_ci';
$db['slave']['swap_pre'] = '';
$db['slave']['autoinit'] = FALSE;
$db['slave']['stricton'] = FALSE;
/**settings database Jupiter*/
$db['local']['hostname'] = 'jupiter.numerica.com.co';
$db['local']['username'] = 'root';
$db['local']['password'] = 'd10n1s10';
$db['local']['database'] = 'wellmo5_alkhorayef';
$db['local']['dbdriver'] = 'mysqli';
$db['local']['dbprefix'] = '';
$db['local']['pconnect'] = FALSE;
$db['local']['db_debug'] = TRUE;
$db['local']['cache_on'] = FALSE;
$db['local']['cachedir'] = '';
$db['local']['char_set'] = 'utf8';
$db['local']['dbcollat'] = 'utf8_general_ci';
$db['local']['swap_pre'] = '';
$db['local']['autoinit'] = FALSE;
$db['local']['stricton'] = FALSE;
