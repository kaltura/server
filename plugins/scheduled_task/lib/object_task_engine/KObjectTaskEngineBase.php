<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
abstract class KObjectTaskEngineBase
{
	/**
	 * @var int
	 */
	private $_originalPartnerId;

	/**
	 * @var KalturaClient
	 */
	private $_client;

	/**
	 * @var KalturaObjectTask
	 */
	private $_objectTask;

	abstract function getSupportedObjectTypes();

	abstract function processObject($object);

	/**
	 * @param object $object
	 * @throws Exception
	 */
	public function execute($object)
	{
		KalturaLog::info('Executing object task '.get_class($this).' for object '.get_class($object));

		if (is_null($this->_client))
			throw new Exception('Client must be set before execution');

		if (!$this->isObjectSupported($object))
			throw new Exception('Object '.get_class($object).' is not support by '.get_class($this).' engine');

		$this->processObject($object);
		
	}
	

	/**
	 * @param KalturaClient $client
	 */
	public function setClient(KalturaClient $client)
	{
		$this->_client = $client;
	}

	/**
	 * @return KalturaClient
	 */
	public function getClient()
	{
		return $this->_client;
	}

	/**
	 * @param KalturaObjectTask $objectTask
	 */
	public function setObjectTask($objectTask)
	{
		$this->_objectTask = $objectTask;
	}

	/**
	 * @return KalturaObjectTask
	 */
	public function getObjectTask()
	{
		return $this->_objectTask;
	}

	/**
	 * Checks if an object is supported by the current object task engine
	 *
	 * @param $object
	 * @return bool
	 */
	public function isObjectSupported($object)
	{
		$supportedObjectTypes = $this->getSupportedObjectTypes();
		foreach($supportedObjectTypes as $supportObjectType)
		{
			if ($object instanceof $supportObjectType)
				return true;
		}
		return false;
	}
	/**
	 * Checks if the a filter class is supported by the current object task engine
	 *
	 * @param $filterClass
	 * @return bool
	 * @throws Exception
	 */
	public function isFilterSupported($filterClass)
	{
		$objectClass = str_replace('Filter', '', $filterClass);
		if (!class_exists($objectClass))
			throw new Exception('Cannot initiate object type '.$objectClass);

		$dummyObjectInstance = new $objectClass;
		return $this->isObjectSupported($dummyObjectInstance);
	}

	protected function impersonate($partnerId)
	{
		if (is_null($this->_originalPartnerId))
			$this->_originalPartnerId = $this->_client->getPartnerId();

		$this->_client->setPartnerId($partnerId);
	}

	protected function unimpersonate()
	{
		$this->_client->setPartnerId($this->_originalPartnerId);
		$this->_originalPartnerId = null;
	}

}