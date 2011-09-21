<?php
/**
 *  @package infra
 *  @subpackage DB
 */
class KalturaStatement extends PDOStatement
{
	protected static $dryRun = false;
	protected static $comment = null;
	
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
		$this->values[":p{$index}"] = "'$value'";
		
		return parent::bindValue ($parameter, $value, $data_type);
	}

	public static function getComment() 
	{
		if(!self::$comment)
		{
			$uniqueId = new UniqueId();
			self::$comment = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : '');
			self::$comment .= "[$uniqueId]";
		}
		
		return self::$comment;
	}

	public function execute ($input_parameters = null) 
	{
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
				$replace[] = "'$value'";
			}
			$search = array_reverse($search);
			$replace = array_reverse($replace);
		}
			
		$sql = str_replace($search, $replace, $this->queryString);
		
		KalturaLog::debug($sql);
		KalturaLog::logByType($sql, KalturaLog::LOG_TYPE_TESTS);
		
		$sqlStart = microtime(true);
		if(self::$dryRun)
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
			KalturaLog::debug("Sql took - " . (microtime(true) - $sqlStart) . " seconds");
		}
	}
	
}