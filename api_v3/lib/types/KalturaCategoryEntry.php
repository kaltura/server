<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryEntry extends KalturaObject 
{
	/**
	 * 
	 * @var int
	 * @filter eq,in
	 */
	public $categoryId;
	
	/**
	 * User id
	 * 
	 * @var string
	 * @filter eq,in
	 */
	public $entryId;
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('categoryId');
		$this->validatePropertyNotNull('entryId');
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject = null, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('categoryId');
		$this->validatePropertyNotNull('entryId');
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}