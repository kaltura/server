<?php
define('KALTURA_ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.".."));
define('KALTURA_INFRA_PATH', KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."infra");

require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."kConf.php");
		
ini_set("memory_limit", "512M");
error_reporting(E_ALL);


require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "*"));
KAutoloader::register();


$code = array();

$ignoreErrors = false;
if($argc > 1 && $argv[1] == 'ignore')
	$ignoreErrors = true;

$updateRunner = new ScriptsRunner();
$updateRunner->init($ignoreErrors);
	
// create version_management table
$updateRunner->runSqlScript(dirname(__FILE__).DIRECTORY_SEPARATOR."create_version_mng_table.sql");
$sqlDir = dirname(__FILE__).DIRECTORY_SEPARATOR."sql";
$updateRunner->runSqlScripts($sqlDir);

$phpDir = dirname(__FILE__).DIRECTORY_SEPARATOR."scripts";
$updateRunner->runPhpScripts($phpDir);


class ScriptsRunner {
	
	private $dbParams = array();
	private $version;
	private $alreadyRun;
	private $ignoreErrors;
	
	public function init($ignore) {
		$this->ignoreErrors = $ignore;
		$dbConf = kConf::getDB();
		$dsn = $dbConf['datasources']['propel']['connection']['dsn'];
		$dsn = explode(":", $dsn);
		$dsnArray = explode(";", $dsn[1], -1);
		
		// init with default port
		$this->dbParams['port'] = '3306';
		
		foreach ($dsnArray as $param) {
			$items = explode("=",$param);
			$this->dbParams[$items[0]] = $items[1];
		} 
		foreach ($this->dbParams as $key=>$value) {			
			echo $key."=>".$value."\n";
		}
		$this->version = $this->getMaxVersion() + 1;
		$this->alreadyRun = $this->getDeployedScripts();
	}
	
	public function runSqlScript($file) {		
		if (!is_file($file)) {
			echo "Could not run script: script not found $file";	
			return false;
		}
		
		if (empty($this->dbParams['password'])) {		
			$cmd = sprintf("mysql -h%s -u%s -P%s %s < %s", $this->dbParams['host'], $this->dbParams['user'], $this->dbParams['port'], $this->dbParams['dbname'], $file);
		} else {
			$cmd = sprintf("mysql -h%s -u%s -p%s -P%s %s < %s", $this->dbParams['host'], $this->dbParams['user'], $this->dbParams['password'], $this->dbParams['port'], $this->dbParams['dbname'], $file);
		}
//		logMessage(L_INFO, "Executing $cmd");
		@exec($cmd . ' 2>&1', $output, $return_var);
		if ($return_var === 0) {
//			logMessage(L_INFO, "Command $cmd Executed Successfully");
			echo "Command ".$cmd." Executed Successfully".PHP_EOL;	
			return true;
		} else {
			echo "Failed to run: ".$cmd." ".PHP_EOL.implode("\n",$output).PHP_EOL;	
//			logMessage(L_INFO, "Failed to run: Failed to run:  $cmd ");
//			logMessage(L_INFO, implode("\n",$output));
			return false;
		}
	}
	
	public function runSqlScripts($sqlDir) {
		$sqlFiles = $this->getDirContnet($sqlDir);
		foreach ($sqlFiles as $sqlFile) {
			if (substr ( $sqlFile, - 4 ) == ".sql") {
				if (!isset($this->alreadyRun[$sqlFile])) {
					if ($this->runSqlScript($sqlDir.DIRECTORY_SEPARATOR.$sqlFile) || $this->ignoreErrors)
						$this->updateVersion($sqlFile);
				} else {
					echo $sqlFile.' already run'.PHP_EOL;
				}
			}
		}
	}
	
	public function runPhpScripts($phpDir) {
		$phpFiles = $this->getDirContnet($phpDir);
		foreach ($phpFiles as $phpFile) {
			if (is_dir($phpDir.DIRECTORY_SEPARATOR.$phpFile)) {
				$this->handleScriptDir($phpDir.DIRECTORY_SEPARATOR.$phpFile);
			} else {
				$this->handleScriptFile($phpDir.DIRECTORY_SEPARATOR.$phpFile);
			}
		}
	}

	private function getMaxVersion() {
		$link  = mysql_connect ($this->dbParams['host'].':'.$this->dbParams['port'], $this->dbParams['user'], $this->dbParams['password'], null);
			
		$db_selected =  mysql_select_db($this->dbParams['dbname'], $link);
		$result = mysql_query('select max(version) from version_management');
		if ($result) {
			$row = mysql_fetch_row($result);
	        $version = $row ? $row[0] : null;
	    }
	    
	    mysql_free_result($result);
		mysql_close($link);
		return $version;
	}
	
	private function getDeployedScripts() {
		$link  = mysql_connect($this->dbParams['host'].':'.$this->dbParams['port'], $this->dbParams['user'], $this->dbParams['password'], null);
			
		$db_selected =  mysql_select_db($this->dbParams['dbname'], $link);
		$result = mysql_query('select filename from version_management');
		if ($result) {
			$res = array();
		
			while ($row = mysql_fetch_assoc($result)) 
			{			
				$res[$row['filename']] = true;
			}
	    }
	    
	    mysql_free_result($result);
		mysql_close($link);
		return $res;
	}
	
	private function getDirContnet($dir) {
		$content = scandir($dir);
		$weeds = array('.', '..', '.svn'); 
		return array_diff($content, $weeds); 
	}
	
	private function updateVersion($fileName) {
		$link  = mysql_connect ($this->dbParams['host'].':'.$this->dbParams['port'], $this->dbParams['user'], $this->dbParams['password'], null);
			
		$db_selected =  mysql_select_db($this->dbParams['dbname'], $link);
		$filePathToInsert = mysql_real_escape_string($fileName); 
		$result = mysql_query("insert into version_management(filename, version) values ('".$filePathToInsert."',".$this->version.")");
		
		return $result;
	}
	
	function handleScriptFile($scriptFile) {
		if (substr ( $scriptFile, - 4 ) == ".php") {
			if (!isset($this->alreadyRun[$scriptFile])) {
				if ($this->runPHPScript($scriptFile) || $this->ignoreErrors)
					$this->updateVersion($scriptFile);
			} else {
				echo $scriptFile.' already run'.PHP_EOL;
			}
		}
	}
	
	function handleScriptDir($scriptsDir) {
		$directories = $this->getDirContnet($scriptsDir); 
		
		foreach ($directories as $scriptFile) {
			if (!is_dir($scriptsDir.DIRECTORY_SEPARATOR.$scriptFile)) {
				$this->handleScriptFile($scriptsDir.DIRECTORY_SEPARATOR.$scriptFile);
			}
			else { 
				$this->handleScriptDir($scriptsDir.DIRECTORY_SEPARATOR.$scriptFile);
			}
		}
	}
	
	function runPHPScript($file) {		
		if (!is_file($file)) {
			echo "Could not run script: script not found $file";	
			return false;
		}
		exec("php " .  $file . " realrun", $logFile, $return_var);
		
		if ($return_var === 0) {
			echo "running ".$file.PHP_EOL;
			return true;
		} else {
			echo "Failed to run: ".$file.PHP_EOL.implode("\n",$logFile).PHP_EOL;	
			return false;
		}
	}
	
}

