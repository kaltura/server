<?php
/*
 * CREATE ELASTIC CLUSTER (ESEARCH / BEACON) ON AN EMPTY ELASTIC_SEARCH NODE
 */
class elasticClusterProvisioning
{
	const DEFAULT_DESIRED_VERSION = 7;
	const DEFAULT_SERVER_VERSION = 'master';
	const DEFAULT_TABLE_SPLIT = 0;
	const ESEARCH_CLUSTER_TYPE = 'esearch';
	const BEACON_CLUSTER_TYPE = 'beacon';
	const DEFAULT_REPLICATION_FACTOR = 1;
	const DEFAULT_REFRESH_INTERVAL = '1s';
	
	// variables
	private $verbose;
	private $testRun;
	
	private $tmpDirectory;
	private $elasticHost;
	private $elasticCurlHost;
	private $elasticDesiredVersion;
	private $elasticClusterName;
	private $elasticVersion;
	private $serverVersion;
	private $clusterType;
	private $elasticServerDirectoryName;
	private $indexSuffix;
	private $createSubIndexes;
	private $replicationFactor;
	private $refreshInterval;
	
	// arrays
	private $tables;
	private $splitFactorTable;
	private $createdIndices;
	
	
	function __construct($elasticHost, $options = array())
	{
		$this->setProperties($elasticHost, $options);
		$elasticResponse = $this->verifyElasticRunning();
		$this->setClusterNameAndVersion($elasticResponse);
		$this->verifyElasticVersion();
		$this->fetchElasticTableMaps();
		logLine("All verifications passed successfully :)");
	}
	
	function __destruct()
	{
		if (!isset($this->tmpDirectory) || !strpos($this->tmpDirectory, 'elastic_cluster_provisioning'))
		{
			logLine("\033[31mNOTICE:\033[39m unexpected temp directory path or name [$this->tmpDirectory]");
			logLine("Not going to delete anything");
			logLine("Script finished");
			exit();
		}
		
		do
		{
			$time = '[' . strftime("%F %T", time()) . ']';
			$answer = readline($time . " Delete directory at [$this->tmpDirectory] and all files inside? [y/n]");
		}
		while (!in_array($answer, array('y', 'n')));
		
		if ($answer == 'y')
		{
			foreach (glob($this->tmpDirectory . '*') as $file)
			{
				if (is_file($file) && strpos($file, '.json'))
				{
					$str = "remove file at [$file] - ";
					if (unlink($file))
					{
						$str .= 'success';
					}
					else
					{
						$str .= 'failed';
					}
					logLine("\033[31mNOTICE:\033[39m $str");
				}
			}
			
			$str = "remove directory at [$this->tmpDirectory] - ";
			if (rmdir($this->tmpDirectory))
			{
				$str .= 'success';
			}
			else
			{
				$str .= 'failed';
			}
			logLine("\033[31mNOTICE:\033[39m $str");
		}
		logLine("Script finished");
	}
	
	private function setProperties($elasticHost, $options)
	{
		$this->verbose = isset($options['verbose']);
		logLine("\033[31mNOTICE:\033[39m setting verbose mode to [$this->verbose]");
		
		$this->testRun = isset($options['testRun']);
		logLine("\033[31mNOTICE:\033[39m Setting test run to [$this->testRun]");
		
		$this->elasticHost = $elasticHost;
		logLine("\033[31mNOTICE:\033[39m setting elastic host to [$this->elasticHost]");
		
		$this->elasticCurlHost = "curl -s -H 'Content-Type: application/json' $this->elasticHost:9200";
		logLine("\033[31mNOTICE:\033[39m setting elastic curl host to [$this->elasticCurlHost]");
		
		$this->elasticDesiredVersion = isset($options['elasticVersion']) ? $options['elasticVersion'] : self::DEFAULT_DESIRED_VERSION;
		logLine("\033[31mNOTICE:\033[39m setting desired elastic version to [$this->elasticDesiredVersion]");
		
		if ($this->elasticDesiredVersion == self::DEFAULT_DESIRED_VERSION)
		{
			$this->elasticServerDirectoryName = 'elasticsearch-7';
		}
		elseif (isset($options['elasticServerDirectoryName']))
		{
			$this->elasticServerDirectoryName = $options['elasticServerDirectoryName'];
		}
		else
		{
			logLine("\033[31mERROR:\033[39m elastic desired version is [$this->elasticDesiredVersion] but no server directory passed");
			logLine("Please pass option '--elasticServerDirectoryName=[directory_name]' to sub dir from 'configurations/elastic/mapping/[directory_name]' when executing script");
			logLine("Terminating script");
		}
		logLine("\033[31mNOTICE:\033[39m setting elastic server directory name to [$this->elasticServerDirectoryName]");
		$this->elasticServerDirectoryName .= '/';
		
		$this->serverVersion = isset($options['serverVersion']) ? $options['serverVersion'] : self::DEFAULT_SERVER_VERSION;
		logLine("\033[31mNOTICE:\033[39m setting server version to [$this->serverVersion]");
		
		if ($options['clusterType'] == self::BEACON_CLUSTER_TYPE)
		{
			$this->clusterType = self::BEACON_CLUSTER_TYPE;
			$this->indexSuffix = date('Ym');
			
			$this->tables = array(
				// $objectName, $elasticIndexName, $subDir, $mapName
				'entry' => array('entry', 'beacon_entry_index', 'mapping/', 'beacon_entry_index.json'),
				'entryServerNode' => array('entryServerNode', 'beacon_entry_server_node_index', 'mapping/', 'beacon_entry_server_node_index.json'),
				'scheduledResource' => array('scheduledResource', 'beacon_scheduled_resource_index', 'mapping/', 'beacon_scheduled_resource_index.json'),
				'serverNode' => array('serverNode', 'beacon_server_node_index', 'mapping/', 'beacon_server_node_index.json')
			);
		}
		elseif ($options['clusterType'] == self::ESEARCH_CLUSTER_TYPE)
		{
			$this->clusterType = self::ESEARCH_CLUSTER_TYPE;
			$this->indexSuffix = date('Y-m-d');
			
			$this->tables = array(
				// $objectName, $elasticIndexName, $subDir, $mapName, $splitFactor
				'entry' => array('entry', 'kaltura_entry', 'mapping/', 'entry_mapping.json'),
				'entryDedicated' => array('entry', 'kaltura_entry_dedicated', 'mapping/', 'entry_mapping_sub.json'),
				'category' => array('category', 'kaltura_category', 'mapping/', 'category_mapping.json'),
				'kuser' => array('kuser', 'kaltura_kuser', 'mapping/', 'kuser_mapping.json'),
				'searchHistory' => array('searchHistory', 'search_history', 'mapping/', 'search_history_mapping.json')
				//'kalturaSynonyms' => array('kalturaSynonyms', 'kaltura_synonyms', 'synonyms/', 'kaltura_synonyms_contraction.txt')
			);
			logLine("\033[31mNOTICE:\033[39m setting indices suffix to [$this->indexSuffix]");
			
			$this->splitFactorTable = array(
				'kaltura_entry' => self::DEFAULT_TABLE_SPLIT,
				'kaltura_category' => self::DEFAULT_TABLE_SPLIT,
				'kaltura_kuser' => self::DEFAULT_TABLE_SPLIT
			);
			
			isset($options['entrySplit']) ? $this->splitFactorTable['kaltura_entry'] = $options['entrySplit'] : null;
			logLine("\033[31mNOTICE:\033[39m setting kaltura_entry table split factor to [{$this->splitFactorTable['kaltura_entry']}]");
			
			isset($options['categorySplit']) ? $this->splitFactorTable['kaltura_category'] = $options['categorySplit'] : null;
			logLine("\033[31mNOTICE:\033[39m setting kaltura_category table split factor to [{$this->splitFactorTable['kaltura_category']}]");
			
			isset($options['kuserSplit']) ? $this->splitFactorTable['kaltura_kuser'] = $options['kuserSplit'] : null;
			logLine("\033[31mNOTICE:\033[39m setting kaltura_kuser table split factor to [{$this->splitFactorTable['kaltura_kuser']}]");
		}
		else
		{
			logLine("\033[31mNOTICE:\033[39m unsupported cluster type [" . $options['clusterType'] . "] please pass 'esearch' or 'beacon'");
			logLine("Terminating script");
			exit(-1);
		}
		logLine("\033[31mNOTICE:\033[39m setting cluster type to [$this->clusterType]");
		
		$this->replicationFactor = isset($options['replicationFactor']) ? $options['replicationFactor'] : self::DEFAULT_REPLICATION_FACTOR;
		logLine("\033[31mNOTICE:\033[39m Setting replication factor to [$this->replicationFactor]");
		
		if (isset($options['refreshInterval']))
		{
			if (is_numeric($options['refreshInterval']))
			{
				$this->refreshInterval = $options['refreshInterval'] . 's';
			}
			elseif ($options['refreshInterval'] === 'null')
			{
				$this->refreshInterval = $options['refreshInterval'];
			}
		}
		else
		{
			$this->refreshInterval = self::DEFAULT_REFRESH_INTERVAL;
		}
		logLine("\033[31mNOTICE:\033[39m Setting refresh interval to [$this->refreshInterval]");
		
		$this->createSubIndexes = isset($options['createSubIndexes']);
		logLine("\033[31mNOTICE:\033[39m Setting create of sub indexes to [$this->createSubIndexes]");
		
		$this->tmpDirectory = sys_get_temp_dir() . '/elastic_cluster_provisioning/';
		if (!file_exists($this->tmpDirectory))
		{
			if (!mkdir($this->tmpDirectory))
			{
				logLine("\033[31mNOTICE:\033[39m fail to create temp directory for script files at [$this->tmpDirectory]");
				logLine("Terminating script");
				exit();
			}
			logLine("\033[31mNOTICE:\033[39m temp directory for script file was created at [$this->tmpDirectory]");
		}
		else
		{
			logLine("\033[31mNOTICE:\033[39m temp directory for script file already exists at [$this->tmpDirectory]");
		}
		
		return true;
	}
	
	private function execCmd($cmdLine, $returnValues = false)
	{
		if ($this->verbose)
		{
			logLine("Executing command: '$cmdLine'");
		}
		
		unset($output);
		exec($cmdLine, $output, $resultCode);
		if ($resultCode)
		{
			logLine("Executing command result status = '$resultCode'");
			logLine("Executing command result output: " . print_r($output));
			logLine("Executed command '$cmdLine' bad result - terminating script");
			exit();
		}
		
		if ($returnValues)
		{
			return array($output, $resultCode);
		}
		
		return true;
	}
	
	private function verifyElasticRunning()
	{
		logLine("Verifying '$this->elasticHost' is running...");
		list($output, $resultCode) = $this->execCmd($this->elasticCurlHost, true);
		$outputJson = implode('', $output);
		$output = json_decode($outputJson, true);
		
		if ($this->verbose)
		{
			logLine("Executing command result status = '$resultCode'");
			logLine("Executing command result output: " . print_r($output, 1));
		}
		
		if (!isset($output['name']))
		{
			logLine("Bad response from Elastic");
			logLine("Response is: " . print_r($output,1));
			logLine("Terminating script");
			exit();
		}
		
		logLine("Elastic '$this->elasticHost' is running");
		return $output;
	}
	
	private function setClusterNameAndVersion($elasticResponse)
	{
		$this->elasticClusterName = isset($elasticResponse['cluster_name']) ? $elasticResponse['cluster_name'] : false;
		$this->elasticVersion = isset($elasticResponse['version']['number']) ? $elasticResponse['version']['number'] : false;
	}
	
	private function verifyElasticVersion()
	{
		logLine("Verifying elastic major version is: [$this->elasticDesiredVersion]");
		$regexPattern = "/^$this->elasticDesiredVersion\./";
		if (!preg_match($regexPattern, $this->elasticVersion))
		{
			logLine("Elastic version [$this->elasticVersion] does not match the desired major version [$this->elasticDesiredVersion]");
			logLine("Terminating script");
			exit();
		}
		
		logLine("Elastic version [$this->elasticVersion] is OK");
		return true;
	}
	
	private function fetchElasticTableMaps()
	{
		$basePath = "https://raw.githubusercontent.com/kaltura/server/$this->serverVersion/";
		if ($this->clusterType == self::ESEARCH_CLUSTER_TYPE)
		{
			$basePath .= "configurations/elastic/";
		}
		elseif ($this->clusterType == self::BEACON_CLUSTER_TYPE)
		{
			$basePath .= "plugins/beacon/config/";
		}
		
		$cmd = "wget -q -P $this->tmpDirectory ";
		foreach ($this->tables as $tableName => $table)
		{
			list($objectName, $elasticIndexName, $subDir, $mapName) = $this->tables[$tableName];
			$url = $basePath . $subDir . $this->elasticServerDirectoryName . $mapName;
			$str = "Downloading [$url] to [$this->tmpDirectory] - ";
			if ($this->execCmd("$cmd $url"))
			{
				$str .= 'success';
			}
			else
			{
				$str .= 'failed';
			}
			logLine($str);
		}
	}
	
	private function createEsearchIndices()
	{
		logLine("Starting esearch indices creation process");
		foreach ($this->tables as $tableName => $table)
		{
			$this->prepareAndRun($tableName);
		}
		$this->createEsearchAliases();
		$this->setReplicationFactor($this->replicationFactor);
		$this->setRefreshInterval($this->refreshInterval);
		// wait for green cluster? probably not because there is no data so it will stay yellow
		return true;
	}
	
	private function createBeaconIndices()
	{
		logLine("Starting beacon indices creation process");
		foreach ($this->tables as $tableName => $table)
		{
			$this->prepareAndRun($tableName);
		}
		$this->createBeaconAliases();
		$this->setReplicationFactor($this->replicationFactor);
		$this->setRefreshInterval($this->refreshInterval);
		return true;
	}
	
	private function createElasticIndex($objectName, $elasticIndexName, $mapName, $isMainIndex = true)
	{
		if ($isMainIndex && isset($this->splitFactorTable[$elasticIndexName]) && $this->splitFactorTable[$elasticIndexName])
		{
			$splitFactor = $this->splitFactorTable[$elasticIndexName];
			for ($i = 0; $i < $splitFactor; $i++)
			{
				$elasticIndexNameSplit = $elasticIndexName . '_' . $i;
				$res = $this->executeToElastic($objectName, $elasticIndexNameSplit, $mapName);
				if (!$res)
				{
					logLine("Failed to create index for $elasticIndexNameSplit");
					logLine("Terminating script");
					exit(-1);
				}
			}
		}
		else
		{
			$res = $this->executeToElastic($objectName, $elasticIndexName, $mapName);
			if (!$res)
			{
				logLine("Failed to create index for $elasticIndexName");
				logLine("Terminating script");
				exit(-1);
			}
		}
	}
	
	private function executeToElastic($objectName, $elasticIndexName, $mapName)
	{
		$elasticIndexFullName = "{$elasticIndexName}-{$this->indexSuffix}";
		$mapPath = "{$this->tmpDirectory}{$mapName}";
		logLine("Creating [$elasticIndexFullName] using map [$mapPath] to [$this->elasticClusterName] elastic cluster");
		
		$cmd = "curl -s -H 'Content-Type: application/json' -XPUT $this->elasticHost:9200/$elasticIndexFullName --data-binary \"@$mapPath\"";
		list($output, $resultCode) = $this->execCmd($cmd, true);
		$res = isset($output[0]) ? json_decode($output[0], true) : null;
		if (isset($res['errors']) && $res['errors'] || isset($res['error']) && $res['error'])
		{
			logLine("Elastic error:\n" . print_r($output, true) . "\n");
			return false;
		}
		logLine("Creating index [$elasticIndexFullName] finished successfully");
		$this->createdIndices[$elasticIndexName] = $elasticIndexFullName;
		return true;
	}
	
	private function createEsearchAliases()
	{
		logLine("Creating aliases");
		foreach ($this->createdIndices as $alias => $index)
		{
			if ($alias == 'search_history')
			{
				$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "search_history_index" }}';
				$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "search_history_search" }}';
				continue;
			}
			elseif ($alias == 'test_search_history')
			{
				$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "test_search_history_index" }}';
				$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "test_search_history_search" }}';
				continue;
			}
			$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "' . $alias . '" }}';
		}
		
		if (!count($actions))
		{
			logLine('Error ! no aliases to add');
			logLine('Terminating script');
			exit(-1);
		}
		
		if ($this->verbose)
		{
			logLine('Going to add aliases:');
			logLine(print_r($actions));
		}
		
		$aliasJson = implode(',', $actions);
		$cmd = "curl -s -H 'Content-Type: application/json' -XPOST $this->elasticHost:9200/_aliases -d '{\"actions\": [$aliasJson]}'";
		list($output, $resultCode) = $this->execCmd($cmd, true );
		$res = isset($output[0]) ? json_decode($output[0], true) : null;
		if (isset($res["acknowledged"]) && $res["acknowledged"])
		{
			logLine('Aliases created successfully');
		}
		else
		{
			logLine('Alias creation failed');
			logLine('Terminating script');
			exit(-1);
		}
	}
	
	private function createBeaconAliases()
	{
		logLine("Creating aliases");
		foreach ($this->createdIndices as $alias => $index)
		{
			$altAlias = $alias . '_search';
			$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "' . $alias . '" }}';
			$actions[] = '{"add" : { "index" : "' . $index . '", "alias" : "' . $altAlias . '" }}';
		}
		
		if (!count($actions))
		{
			logLine('Error ! no aliases to add');
			logLine('Terminating script');
			exit(-1);
		}
		
		if ($this->verbose)
		{
			logLine('Going to add aliases:');
			logLine(print_r($actions));
		}
		
		$aliasJson = implode(',', $actions);
		$cmd = "curl -s -H 'Content-Type: application/json' -XPOST $this->elasticHost:9200/_aliases -d '{\"actions\": [$aliasJson]}'";
		list($output, $resultCode) = $this->execCmd($cmd, true );
		$res = isset($output[0]) ? json_decode($output[0], true) : null;
		if (isset($res["acknowledged"]) && $res["acknowledged"])
		{
			logLine('Aliases created successfully');
		}
		else
		{
			logLine('Alias creation failed');
			logLine('Terminating script');
			exit(-1);
		}
	}
	
	private function setReplicationFactor($replicationFactor = 0)
	{
		foreach ($this->createdIndices as $indexName)
		{
			logLine("Setting index [$indexName] replication factor to [$replicationFactor]");
			$cmd = "curl -s -H 'Content-Type: application/json' -XPUT $this->elasticHost:9200/$indexName/_settings -d '{\"number_of_replicas\": $replicationFactor}'";
			list($output, $resultCode) = $this->execCmd($cmd, true);
			$res = isset($output[0]) ? json_decode($output[0], true) : null;
			if (isset ($res["acknowledged"]) && $res["acknowledged"])
			{
				logLine("Replication factor for index [$indexName] set to [$replicationFactor] successfully");
			}
			else
			{
				logLine("Replication factor for index [$indexName] set to [$replicationFactor] failed");
				logLine("Terminating script");
				exit(-1);
			}
		}
		logLine("All indices replication factor was set to [$replicationFactor] successfully");
		return true;
	}
	
	private function setRefreshInterval($refreshInterval = "1s")
	{
		foreach ($this->createdIndices as $indexName)
		{
			logLine("Setting index [$indexName] refresh interval to [$refreshInterval]");
			$cmd = "curl -s -H 'Content-Type: application/json' -XPUT $this->elasticHost:9200/$indexName/_settings -d '{\"refresh_interval\": \"$refreshInterval\"}'";
			list($output, $resultCode) = $this->execCmd($cmd, true);
			$res = isset($output[0]) ? json_decode($output[0], true) : null;
			if (isset($res["acknowledged"]) && $res["acknowledged"])
			{
				logLine("Refresh interval for index [$indexName] set to [$refreshInterval] successfully");
			}
			else
			{
				logLine("Refresh interval for index [$indexName] set to [$refreshInterval] failed");
				logLine("Terminating script");
				exit(-1);
			}
		}
		logLine("All indices refresh interval was set to [$refreshInterval] successfully");
		return true;
	}
	
	private function prepareAndRun($tableName)
	{
		list($objectName, $elasticIndexName, $subDir, $mapName) = $this->tables[$tableName];
		
		if (!$this->createSubIndexes && strpos($tableName, 'Dedicated'))
		{
			logLine("\033[31mNOTICE:\033[39m skipping index name [$tableName] since --createSubIndexes option was set to false");
			return true;
		}
		
		if ($this->testRun)
		{
			$testElasticIndexName = "test_{$elasticIndexName}";
			if (isset($this->splitFactorTable[$elasticIndexName]))
			{
				$this->splitFactorTable[$testElasticIndexName] = $this->splitFactorTable[$elasticIndexName];
				unset($this->splitFactorTable[$elasticIndexName]);
			}
			$elasticIndexName = $testElasticIndexName;
		}
		
		if (strpos($tableName, 'Dedicated'))
		{
			$this->createElasticIndex($objectName, $elasticIndexName, $mapName, false);
		}
		
		$this->createElasticIndex($objectName, $elasticIndexName, $mapName);
	}
	
	public function run()
	{
		if ($this->clusterType == self::ESEARCH_CLUSTER_TYPE)
		{
			$this->createEsearchIndices();
		}
		elseif ($this->clusterType == self::BEACON_CLUSTER_TYPE)
		{
			$this->createBeaconIndices();
		}
		logLine("All indices were created and set successfully");
		return true;
	}
}

function main($elasticHost, $options)
{
	$elastic = new elasticClusterProvisioning($elasticHost, $options);
	$elastic->run();
}

function logLine($s)
{
	echo '[' . strftime("%F %T", time()) . "] $s\n";
}

/*
 * START OF SCRIPT
 */

$shortOptions = '';
$longOptions = array(
	'verbose',
	'testRun',
	'host::',
	'elasticVersion::',
	'serverVersion::',
	'entrySplit::',
	'categorySplit::',
	'kuserSplit::',
	'clusterType::',
	'elasticServerDirectoryName::',
	'replicationFactor::',
	'refreshInterval::',
	'createSubIndexes'
);
$options = getopt($shortOptions, $longOptions);

if (!isset($options['host']) || !isset($options['clusterType']))
{
	echo <<<EOL
Missing required parameters - host and cluster type are required
To execute: $argv[0] --host=[elasticClusterHost] --clusterType=[(esearch)||(beacon)]

Available options:
	--verbose - more output logs
	--testRun - set all indices with a prefix of 'test_'
	--createSubIndexes - created index with mapping type '*_sub.json' like 'kaltura_entry_dedicated'
	--elasticVersion=[num] - the major elastic desired version (default is '7')
	--elasticServerDirectoryName=[dir name] - if 'elasticVersion' option was changed, you can change directory name for the mapping.json files
	--serverVersion=[version] - the server version from which to pull mapping.json files (default is 'master')
	--entrySplit=[num] - split kaltura_entry index to multiple tables (default is '0')
	--categorySplit=[num] - split kaltura_category index to multiple tables (default is '0')
	--kuserSplit=[num] - split kaltura_kuser index to multiple tables (default is '0')
	--clusterType=[(esearch) || (beacon)] - which cluster to generate (default is 'esearch')
	--replicationFactor=[num] - number of shards to replicate (default is '1')
	--refreshInterval=[num_of_seconds] - when to clear buffer to see cluster changes in seconds (default is '1' second, set 'null' for elastic default value)

EOL;
	exit();
}
$elasticHost = $options['host'];
main($elasticHost, $options);
