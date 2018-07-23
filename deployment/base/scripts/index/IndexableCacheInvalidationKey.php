<?php

/**
 * This class is a container class for all indexing properties about
 * a single Sphinx-CacheInvalidationKey object
 */
class IndexableCacheInvalidationKey {

	private $name;
	private $getter;
	private $peerName;
	private $apiName;

	public function __construct($name, $getter, $peerName, $apiName) {
		$this->name = $name;
		$this->getter = $getter;
		$this->peerName = $peerName;
		$this->apiName = $apiName;
	}

	/**
	 * @return $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return $getter
	 */
	public function getGetter() {
		return $this->getter;
	}

	/**
	 * @param $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param $getter
	 */
	public function setGetter($getter) {
		$this->getter = $getter;
	}

	/**
	 * @return $peerName
	 */
	public function getPeerName() {
		return $this->peerName;
	}

	/**
	 * @param $peerName
	 */
	public function setPeerName($peerName) {
		$this->peerName = $peerName;
	}

	/**
	 * @return string
	 */
	public function getApiName()
	{
		return $this->apiName;
	}

	/**
	 * @param string $apiName
	 */
	public function setApiName($apiName)
	{
		$this->apiName = $apiName;
	}
}

