<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IIndexable extends IBaseObject
{
	const FIELD_TYPE_STRING = 'string';
	const FIELD_TYPE_UINT = 'uint';
	const FIELD_TYPE_INTEGER = 'int';
	const FIELD_TYPE_FLOAT = 'float';
	const FIELD_TYPE_DATETIME = 'datetime';
	const FIELD_TYPE_JSON = 'json';
	
	/**
	 * Is the id as used and know by the indexing server
	 * @return int
	 */
	public function getIntId();
	
	/**
	 * @return string
	 */
	public function getEntryId();
	
	/**
	 * This function returns the index object name (the one responsible for the sphinx mapping)
	 */
	public function getIndexObjectName();
	
	/**
	 * @param int $time
	 * @return IIndexable
	 */
	public function setUpdatedAt($time);
	
	/**
	 * Index the object in the search engine
	 */
	public function indexToSearchIndex();
	
}