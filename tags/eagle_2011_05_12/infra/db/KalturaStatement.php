<?php
/**
 *  @package infra
 *  @subpackage DB
 */
class KalturaStatement extends PDOStatement
{
	protected $values = array();
	
	public function bindValue ($parameter, $value, $data_type = null)
	{
		$index = count($this->values) + 1;
		$this->values[":p{$index}"] = "'$value'";
		
		return parent::bindValue ($parameter, $value, $data_type);
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
		
		$sqlStart = microtime(true);
		parent::execute($input_parameters);
		KalturaLog::debug("Sql took - " . (microtime(true) - $sqlStart) . " seconds");
	}
	
}