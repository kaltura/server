

<?php

require_once( "mySpaceUpdater.class.php");

function cacheTest( $ok , $notok )
{
	/*	echo microtime() . "<br>";
	 usleep( 2000000 );
	 echo microtime() . "<br>";
	 */

	try
	{
		echo "(" . microtime () .") Testing cache <br>";
		$start_microtime = microtime();

		$cache1 = new myCache ( "c1" );
		$cache2 = new myCache ( "c2" );
		echo "Fetching an object from cache1<br>";
		$obj1 = $cache1->get ("obj1");
		echo "This is what we have: " . $obj1 . "<br>";
		$obj1 = ( $obj1 == NULL ? 0 : $obj1 = $obj1 + 3  );
		echo "Now setting with new value: " . $obj1 . " (+3)<br>";
		$cache1->put ( "obj1" , $obj1 , 0);

		echo "Fetching an object from cache2 - using the same object name 'obj1'<br>";
		$obj2 = $cache2->get ("obj1");
		echo "This is what we have: " . $obj2 . "<br>";
		$obj2 = ( $obj2 == NULL ? 0 : $obj2 = $obj2 - 2  );
		echo "Now setting with new value: " . $obj2 . " (-2)<br>";
		$cache2->put ( "obj1" , $obj2 , 0);

		echo "incrementing by 7<br>";
		$inc_res = $cache2->increment( "obj1" , 7);
		echo "inc result: $inc_res<br>";
		echo "incrementing by 6<br>";
		$inc_res = $cache2->increment( "obj1" , 6);
		echo "inc result: $inc_res<br>";
		
		$end_microtime = microtime();

		echo "(" . microtime() .") Test took [" . ( $end_microtime - $start_microtime) . "] seconds";
		
		echo $ok;
	}
	catch ( Exception $e )
	{
		echo "Problem with your memcache installtion";
		echo $notok;
	}

}


function debuggerTest()
{
	@ini_set('zend_monitor.enable', 0);
	if(@function_exists('output_cache_disable')) 
	{
		@output_cache_disable();
	}
	if(isset($_GET['debugger_connect']) && $_GET['debugger_connect'] == 1) 
	{
		echo "debugger_connect<br>";
		if(function_exists('debugger_connect'))  
		{
			debugger_connect();
			echo "after debugger_connect";
			exit();
		} 
		else 
		{
			echo "No connector is installed.";
		}
	}
}

function emailContactImporterDiagnose ( )
{
	include_once ( "email_import_diagnose.php" );
}
$ok = image_tag( '/sf/sf_default/images/icons/ok48.png');
$notok = image_tag( '/sf/sf_default/images/icons/cancel48.png');
?>

<a href="/index.php/system/login?exit=true">logout</a><br>

<h1>System Test Results</h1>
<TABLE align="center">

<?php
echo '<TR><TD>';
echo "logger class: " . get_class( sfLogger::getInstance() );
echo '</TD></TR>';

echo '<TR><TD>';
echo "Host: " . requestUtils::getHost()  ;
echo '</TD></TR>';
echo '<TR><TD>';
echo " webRoot: " . requestUtils::getWebRootUrl();
echo '</TD></TR>';

echo "<TR><TD>Is the server running the 'curl_init' extension? " . ( function_exists("curl_init") ? $ok : $notok ) ."</TD></TR>";

/*
echo '<TR><TD>The name of the user running httpd: '. exec('whoami')."</TD></TR>";

echo '<TR><TD>Path: '. exec('echo $PATH')."</TD></TR>";

echo '<TR><TD>Is FFMPEG in the path: '. (exec('ffmpeg') != null ? $ok : $notok)."</TD></TR>";
*/

echo '<TR><TD>';

//emailContactImporterDiagnose ();

echo '</TD></TR>';

/*
 @ini_set('zend_monitor.enable', 0);
 if(@function_exists('output_cache_disable'))
 {
	echo ("<br>output_cache_disable<br>");
	@output_cache_disable();
	}
	if(isset($_GET['debugger_connect']) && $_GET['debugger_connect'] == 1)
	{
	if(function_exists('debugger_connect'))  {
	debugger_connect();
	exit();
	} else {
	echo "No connector is installed.";
	}
	}
	*/

echo "<TR><TD>" ;
cacheTest( $ok , $notok );
echo "</TD></TR>";

/*
echo "<TR><TD>" ;
echo "<br>Read from config:<br>";
$config = new myConfigWrapper( "app_" );
$l = $config->getList ( "featured_intro_list" );
echo "featured_intro_list count: " . count ( $l ) . " " . print_r ( $l , true ) . "<br>";
$l = $config->getList ( "featured_show_list" );
echo "featured_show_list count: " . count ( $l ) . " " . print_r ( $l , true ) . "<br>";
$l = $config->getList ( "featured_team_list" );
echo "featured_team_list count: " . count ( $l ) . " " . print_r ( $l , true ) . "<br>";
echo "</TD></TR>";

echo "<TR><TD>" ;
echo "<br/>";
echo "Myspace cookie path: " . MySpace::getMySpaceCookiePath();
echo "<br/>";
echo "</TD></TR>";
*/
//debuggerTest();

 echo "<TR><TD>" . phpinfo()."</TD></TR>";
 echo "<TR><TD>Session Timeout:" . sfConfig::get('sf_timeout')."</TD></TR>";
?>
</TABLE>
