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
	 * @return string object propel name
	 */
	public function getObjectIndexName();
	
	/**
	 * @return array while the key is the index attribute name and the value is the getter name
	 * For example array('name' => 'title') if name should be retrieved by getTitle() getter
	 */
	public function getIndexFieldsMap();
	
	/**
	 * @return array of fields that should be indexed with suffix that indicates that the content is not null or text that indicates that the value is null
	 */
	public static function getIndexNullableFields();
	
	/**
	 * @return string field type, string, int or timestamp
	 */
	public function getIndexFieldType($field);
	
	/**
	 * @return string field escape type for strings
	 */
	public function getSearchIndexFieldsEscapeType($field);
	
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