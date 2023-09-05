<?php

/**
 * @package Core
 * @subpackage model
 **/
class RegexItem
{
	private $regex;
	
	private $description;
	
	public function setRegex($regex)
	{
		$this->regex = $regex;
	}
	
	public function getRegex()
	{
		return $this->regex;
	}
	
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
}
