<?php
require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
$action = $argv[1]; // start/stop 
$batch_name = @$argv[2];

$kaltura_root = dirname(__FILE__).'/../../../';
$kaltura_rules_path = dirname(__FILE__).'/ce_rules.cfg';
if (isset($_SERVER['SERVER_SOFTWARE']))
{
	$os = (substr_count($_SERVER['SERVER_SOFTWARE'], 'Win32'))? 'win': 'nix';
}
else
{
	$os = (substr_count(strtolower($_SERVER['OS']), 'windows'))? 'win': 'nix';
}
$kaltura_env_path = dirname(__FILE__).'/kaltura_env.sh';
echo $kaltura_env_path."\n";
$kaltura_env = file_get_contents($kaltura_env_path);
$kaltura_env_lines = explode("\n", $kaltura_env);
foreach($kaltura_env_lines as $line)
{
	// find PHP_PATH and return the path
	if(substr_count($line, 'PHP_PATH'))
	{
		$parts = explode('=', $line);
		$php_path = str_replace("\n", '',$parts[1]);
	}
}

// assume the rules.cfg is in this current directory
$rules_file_name = $kaltura_rules_path;
$content = file_get_contents( $rules_file_name );
$lines = explode ( "\n" , $content );

if ($batch_name)
{
	$lines = array ( $batch_name." true" );
}
foreach ( $lines as $line ) 
{
	$args = explode (" " , $line );
	switch($action)
	{
		case 'start':
			echo "starting batch {$args[0]}".PHP_EOL;
			run_single_batch($os, $args[0],$php_path);
			sleep(1);
			break;
		case 'stop':
			echo "killing batch {$args[0]}".PHP_EOL;
			kill_single_batch($os, $args[0]);
			break;
		case 'restart':
			echo "restarting batch {$args[0]}".PHP_EOL;
			kill_single_batch($os, $args[0]);
			run_single_batch($os, $args[0],$php_path);
			break;
		default:
			echo "no action !\n";
			break;
	}
}

exit(0);

function run_single_batch($os, $batch_name, $php_path)
{
	global $kaltura_root;
	$batch_log_file = $kaltura_root.'logs/'.php_uname('n').'-'.$batch_name.'.log';
	$batch_php_file = SF_ROOT_DIR.'/batch/'.$batch_name;
	if (!substr_count($batch_php_file, '.php'))
	{
		$batch_php_file .= '.php';
	}
	if ($os == 'win')
	{
		$args = '"'.$batch_php_file.'" >> "'.$batch_log_file.'"';
		echo "start \"kaltura_".$batch_name."\" \"" . $php_path . "\" " . $args.PHP_EOL;
		pclose(popen("start /B \"kaltura_".$batch_name."\" \"" . $php_path . "\" " . $args, "r"));
	}
	if ($os == 'nix')
	{
		$args = $batch_php_file.' >> '.$batch_log_file.' &';
		pclose(popen($php_path . " " . $args, "r"));
	}
}

function kill_single_batch($os, $batch_name)
{
	global $kaltura_root;
	$indicators = scandir($kaltura_root.'/batchwatch/');
	$batch_indicators = array();
	foreach($indicators as $key => $ind)
	{
	    if (substr_count($ind, $batch_name.'.running'))
	    {
		$batch_indicators[] = $ind;
	    }
	}
	foreach($batch_indicators as $ind)
	{
		$parts = explode('.', $ind);
		$pid = $parts[2];
		echo $pid.PHP_EOL;
		if ($os == 'win')
		{
			$killCmd = 'taskkill /F /T /PID '.$pid;
		}
		if ($os == 'nix')
		{
			$killCmd = 'kill -9 '.$pid;
		}
		echo $killCmd;
		exec($killCmd, $output, $error);
		if (!$error)
		{
			unlink($kaltura_root.'batchwatch/'.$ind);
		}
		else
		{
			var_dump($error, $output);
		}
	}
}
?>