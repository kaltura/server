<?php
/**
 * @package infra
 * @subpackage Conversion
 */
class kOperatorSets
{
	/**
	 * @var array<array<kOperator>>
	 */
	public $sets = array();
	
	/**
	 * @param array<kOperator> $set
	 */
	public function addSet(array $set)
	{
		$this->sets[] = $set;
	}
	
	/**
	 * @return array<array<kOperator>>
	 */
	public function getSets()
	{
		return $this->sets;
	}
	
	/**
	 * @param int $set
	 * @param int $index
	 * @return kOperator
	 */
	public function getOperator($set = 0, $index = 0)
	{
		if(!is_array($this->sets))
			return null;
		
		$theSet = null;
		if(isset($this->sets[$set]))
			$theSet = $this->sets[$set];
		else
			$theSet = end($this->sets);
			
		$index = max($index, 0);
		
		if(!is_array($theSet))
			return null;
		
		if(isset($theSet[$index]))
			return $theSet[$index];
		
		return end($theSet);
	}
	
	/**
	 * @return string
	 */
	public function getSerialized()
	{
		return json_encode($this->sets);
	}
	
	/**
	 * @return string
	 */
	public function setSerialized($json)
	{
		$sets = json_decode($json);
		if(!is_array($sets))
			return;
		
		$this->sets = array();
		foreach($sets as $decodedSet)
		{
			$set = array();
			foreach($decodedSet as $decodedOperator)
			{
				$operator = new kOperator($decodedOperator);
/*				$operator->id = $decodedOperator->id;
				$operator->extra = isset($decodedOperator->extra) ? $decodedOperator->extra : null;
				$operator->command = isset($decodedOperator->command) ? $decodedOperator->command : null;
				$operator->config = isset($decodedOperator->config) ? $decodedOperator->config : null;
*/				
				$set[] = $operator;
			}
			
			$this->addSet($set); 
		}
	}
}