<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IIndexable extends IBaseObject
{
	const FIELD_TYPE_STRING = 'string';
	const FIELD_TYPE_INTEGER = 'int';
	const FIELD_TYPE_DATETIME = 'datetime';
	
	/**
	 * Is the id as used and know by Kaltura
	 * @return string
	 */
	public function getId();
	
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
	 * @return string object propel name
	 */
	public function getObjectIndexName();
	
	/**
	 * @return array while the key is the index attribute name and the value is the getter name
	 * For example array('name' => 'title') if name should be retrieved by getTitle() getter
	 */
	public function getIndexFieldsMap();
	
	/**
	 * @return string field type, string, int or timestamp
	 */
	public function getIndexFieldType($field);
}