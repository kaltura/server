<?php
/*
 * Created on Nov 25, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// assume the existance of the paths
/*
mkdir /web/content/new_preconvert
mkdir /web/conversions/preconvert_cmd
mkdir /web/conversions/postconvert_res

*/
if ( $argc > 1 )
{
	$server_id = $argv[1];
}
else
{
	$server_id = 1;
}
$start_time = microtime(true);
$script_name = $_SERVER['SCRIPT_NAME'];

$server_cmd_path = myContentStorage::getFSContentRootPath (). "/conversions/preconvert_cmd" ;
$server_res_path = myContentStorage::getFSContentRootPath (). "/conversions/postconvert_res" ;
$commercial = false;
$convert_server = new kConversionServer( $script_name , $server_cmd_path , $server_res_path , $commercial , $server_id ) ;
$convert_server->convert();
$end_time = microtime ( true );
$diff = (int)(( $end_time - $start_time ) * 1000);
echo ( "****************\nEndes after " . $diff . " millisecond \n****************" );
?>
