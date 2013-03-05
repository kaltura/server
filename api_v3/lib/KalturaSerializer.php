<?php
/**
 * Base class for plugin serializers
 * @package api
 * @subpackage v3
 */
abstract class KalturaSerializer
{
	protected $_serializedString = "";
	
	public abstract function serialize($object);
	
	public function setHeaders(){}
	
	public function getSerializedData()
	{
		return $this->_serializedString;
	}
}
