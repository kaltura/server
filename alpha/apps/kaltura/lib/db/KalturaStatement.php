<?php
/**
 *  @package server-infra
 *  @subpackage DB
 */
class KalturaStatement extends PDOStatement
{
	protected static $dryRun = false;
	
	protected $values = array();
	
	/**
	 * @param bool $dryRun
	 */
	public static function setDryRun($dryRun)
	{
		self::$dryRun = $dryRun;
	}
	
	public function bindValue ($parameter, $value, $data_type = null)
	{
		$index = count($this->values) + 1;
		if(is_null($value))
			$this->values[":p{$index}"] = "NULL";
		else
			$this->values[":p{$index}"] = "'" . str_replace("'", "''", $value) . "'";
		
		return parent::bindValue ($parameter, $value, $data_type);
	}

	public function execute ($input_parameters = null) 
	{
		if (!kQueryCache::isCurrentQueryHandled())
			kApiCache::disableConditionalCache();
	
		$search = array();
		$replace = array();
		
		if(is_null($input_parameters))
		{
			$search = array_reverse(array_keys($this->values));
			$replace = array_reverse($this->values);
		}
		else
		{
			$i = 1;
			foreach ($input_parameters as $value) 
			{
				$search[] = ':p' . $i++;
				if(is_null($value))
					$replace[] = "NULL";
				else
					$replace[] = "'$value'";
			}
			$search = array_reverse($search);
			$replace = array_reverse($replace);
		}
			
		$sql = str_replace($search, $replace, $this->queryString);
		
		KalturaLog::debug($sql);
		
		$sqlStart = microtime(true);
		if(self::$dryRun && !preg_match('/^(\/\*.+\*\/ )?SELECT/i', $sql))
		{
			KalturaLog::debug("Sql dry run - " . (microtime(true) - $sqlStart) . " seconds");
		}
		else
		{
			try
			{
				parent::execute($input_parameters);
			}
			catch(PropelException $pex)
			{
				KalturaLog::alert($pex->getMessage());
				throw new PropelException("Database error");
			}
			$sqlTook = (microtime(true) - $sqlStart);
			KalturaLog::debug("Sql took - " . $sqlTook . " seconds");
			KalturaMonitorClient::monitorDatabaseAccess($sql, $sqlTook);
		}
	}
	
}