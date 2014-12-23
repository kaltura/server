<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "bootstrap.php");

ini_set("memory_limit", "512M");
error_reporting(E_ALL);

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "*"));
KAutoloader::register();

$code = array();

$options = getopt('iu:p:h:P:ds', array(
	'ignore',
	'user:',
	'password:',
	'host:',
	'port:',
	'database-only:',
	'scripts-only:',
));

$ignoreErrors = false;
$skipDB = false;
$skipScripts = false;

if(isset($options['i']) || isset($options['ignore']))
	$ignoreErrors = true;

if(isset($options['d']) || isset($options['database-only']))
	$skipScripts = true;
	
if(isset($options['s']) || isset($options['scripts-only']))
	$skipDB = true;
	
$params = array();
if(isset($options['u']))
	$params['user'] = $options['u'];
if(isset($options['user']))
	$params['user'] = $options['user'];
	
if(isset($options['p']))
	$params['password'] = $options['p'];
if(isset($options['password']))
	$params['password'] = $options['password'];
	
if(isset($options['h']))
	$params['host'] = $options['h'];
if(isset($options['host']))
	$params['host'] = $options['host'];
	
if(isset($options['P']))
	$params['port'] = $options['P'];
if(isset($options['port']))
	$params['port'] = $options['port'];
	
$updateRunner = new ScriptsRunner();
$updateRunner->init($ignoreErrors, $params);

// create version_management table
$updateRunner->runSqlScript(dirname(__FILE__) . DIRECTORY_SEPARATOR . "create_version_mng_table.sql");

if(!$skipDB)
{
	$sqlDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql";
	$updateRunner->runSqlScripts($sqlDir);
}

if(!$skipScripts)
{
	$phpDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "scripts";
	$updateRunner->runPhpScripts($phpDir);
}

exit(0);

class ScriptsRunner
{
	
	private $dbParams = array();
	private $version;
	private $alreadyRun;
	private $ignoreErrors;
	
	public function init($ignore, array $params)
	{
		$this->ignoreErrors = $ignore;
		$dbConf = kConf::getDB();
		$dsn = $dbConf['datasources']['propel']['connection']['dsn'];
		$dsn = explode(":", $dsn);
		$dsnArray = explode(";", $dsn[1]);
		
		// init with default port
		$this->dbParams = $dbConf['datasources']['propel']['connection'];
		$this->dbParams['port'] = '3306';
		
		foreach($dsnArray as $param)
		{
			$items = explode("=", $param);
			if(count($items) == 2)
				$this->dbParams[$items[0]] = $items[1];
		}
		
		foreach($params as $key => $value)
			$this->dbParams[$key] = $value;
		
		foreach($this->dbParams as $key => $value)
		{
			echo $key .' => '; 
			if (is_array($value)){
				var_dump($value);
			}else{
				echo "$value\n";
			}
		}
		$this->version = $this->getMaxVersion() + 1;
		$this->alreadyRun = $this->getDeployedScripts();
	}
	
	public function runSqlScript($file)
	{
		if(! is_file($file))
		{
			echo "Could not run script: script not found $file";
			return false;
		}
		
		if(empty($this->dbParams['password']))
		{
			$cmd = sprintf("mysql -h%s -u%s -P%s %s < %s", $this->dbParams['host'], $this->dbParams['user'], $this->dbParams['port'], $this->dbParams['dbname'], $file);
		}
		else
		{
			$cmd = sprintf("mysql -h%s -u%s -p%s -P%s %s < %s", $this->dbParams['host'], $this->dbParams['user'], $this->dbParams['password'], $this->dbParams['port'], $this->dbParams['dbname'], $file);
		}
		echo "Executing [$cmd]" . PHP_EOL;
		passthru($cmd . ' 2>&1', $return_var);
		if($return_var === 0)
		{
			echo "Command [$cmd] Executed Successfully" . PHP_EOL . PHP_EOL;
			return true;
		}
		else
		{
			echo "Failed to run [$cmd]" . PHP_EOL . PHP_EOL;
			return false;
		}
	}
	
	public function runSqlScripts($sqlDir)
	{
		$sqlFiles = $this->getDirContnet($sqlDir);
		foreach($sqlFiles as $sqlFile)
		{
			if(substr($sqlFile, - 4) == ".sql")
			{
				if(! isset($this->alreadyRun[$sqlFile]))
				{
					if(!$this->runSqlScript($sqlDir . DIRECTORY_SEPARATOR . $sqlFile) && !$this->ignoreErrors)
					{
						exit(-1);
					}
					$this->updateVersion($sqlFile);
				}
				else
				{
					echo $sqlFile . ' already run' . PHP_EOL;
				}
			}
		}
	}
	
	public function runPhpScripts($phpDir)
	{
		$phpFiles = $this->getDirContnet($phpDir);
		foreach($phpFiles as $phpFile)
		{
			if(is_dir($phpDir . DIRECTORY_SEPARATOR . $phpFile))
			{
				$this->handleScriptDir($phpDir . DIRECTORY_SEPARATOR . $phpFile);
			}
			else
			{
				$this->handleScriptFile($phpDir . DIRECTORY_SEPARATOR . $phpFile);
			}
		}
	}
	
	private function getMaxVersion()
	{
		$link = mysqli_connect($this->dbParams['host'], $this->dbParams['user'], $this->dbParams['password'], null, $this->dbParams['port']);
		$db_selected = mysqli_select_db($link,$this->dbParams['dbname']);
		$result = mysqli_query($link,'select max(version) from version_management');
		if($result)
		{
			$row = mysqli_fetch_row($result);
			$version = $row ? $row[0] : null;
		}
		
		mysqli_free_result($result);
		mysqli_close($link);
		return $version;
	}
	
	private function getDeployedScripts()
	{
		$link = mysqli_connect($this->dbParams['host'], $this->dbParams['user'], $this->dbParams['password'], null, $this->dbParams['port']);
		
		$db_selected = mysqli_select_db($link,$this->dbParams['dbname']);
		$result = mysqli_query($link,'select filename from version_management');
		if($result)
		{
			$res = array();
			
			while($row = mysqli_fetch_assoc($result))
			{
				$res[$row['filename']] = true;
			}
		}
		
		mysqli_free_result($result);
		mysqli_close($link);
		return $res;
	}
	
	private function getDirContnet($dir)
	{
		$content = scandir($dir);
		$weeds = array('.', '..', '.svn');
		return array_diff($content, $weeds);
	}
	
	private function updateVersion($fileName)
	{
		$link = mysqli_connect($this->dbParams['host'] . ':' . $this->dbParams['port'], $this->dbParams['user'], $this->dbParams['password'], null);
		
		$db_selected = mysqli_select_db($this->dbParams['dbname'], $link);
		$filePathToInsert = mysqli_real_escape_string($fileName);
		$result = mysqli_query("insert into version_management(filename, version) values ('" . $filePathToInsert . "'," . $this->version . ")");
		
		return $result;
	}
	
	function handleScriptFile($scriptFile)
	{
		if(substr($scriptFile, - 4) == ".php")
		{
			if(! isset($this->alreadyRun[$scriptFile]))
			{
				if(!$this->runPHPScript($scriptFile) && !$this->ignoreErrors)
				{
					exit(-2);
				}
				$this->updateVersion($scriptFile);
			}
			else
			{
				echo $scriptFile . ' already run' . PHP_EOL;
			}
		}
	}
	
	function handleScriptDir($scriptsDir)
	{
		$directories = $this->getDirContnet($scriptsDir);
		
		foreach($directories as $scriptFile)
		{
			if(! is_dir($scriptsDir . DIRECTORY_SEPARATOR . $scriptFile))
			{
				$this->handleScriptFile($scriptsDir . DIRECTORY_SEPARATOR . $scriptFile);
			}
			else
			{
				$this->handleScriptDir($scriptsDir . DIRECTORY_SEPARATOR . $scriptFile);
			}
		}
	}
	
	function runPHPScript($file)
	{
		if(! is_file($file))
		{
			echo "Could not run script: script not found $file";
			return false;
		}
		
		echo "Running [$file]" . PHP_EOL;
		
		passthru("php " . $file . " realrun", $return_var);
		
		if($return_var === 0)
		{
			echo "Finish [$file]" . PHP_EOL . PHP_EOL;
			return true;
		}
		else
		{
			echo "Failed to run [$file]" . PHP_EOL . PHP_EOL;
			return false;
		}
	}

}

class OsUtils
{
	const WINDOWS_OS = 'Windows';
	const LINUX_OS = 'linux';
	public static function getOsName()
	{
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			return self::WINDOWS_OS;
		}
		else if(strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX')
		{
			return self::LINUX_OS;
		}
		else
		{
			echo "OS not recognized: " . PHP_OS . PHP_EOL;
			return "";
		}
	}
	
	public static function getCurrentDir()
	{
		if(OsUtils::getOsName() === self::LINUX_OS)
		{
			return exec('pwd');
		}
		return dirname(__FILE__);
	}

}
