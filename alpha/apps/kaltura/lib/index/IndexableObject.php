<?php

/**
 * This class is a container class for all indexing properties about
 * a single indexable object  
 */
class IndexableObject {
	
	/** The name of the indexable object */
	public $name;
	
	/** The name of the peer */
	public $peerName;
	
	/** The id of the indexable object */
	public $indexId = "id";
	
	/** The id of the propel object */
	public $objectId = "ID";
	
	/** The index table name */
	public $indexName;
	
	/** The ID field in case of 'string' id.
	 * relevant only for objects in which ID is string.*/
	public $id = null;

	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $indexId
	 */
	public function getIndexId() {
		return $this->indexId;
	}

	/**
	 * @return the $objectId
	 */
	public function getObjectId() {
		return $this->objectId;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $indexId
	 */
	public function setIndexId($indexId) {
		$this->indexId = $indexId;
	}

	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId) {
		$this->objectId = $objectId;
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @return the $peerName
	 */
	public function getPeerName() {
		return $this->peerName;
	}

	/**
	 * @param field_type $peerName
	 */
	public function setPeerName($peerName) {
		$this->peerName = $peerName;
	}
	
	/**
	 * @return the $indexName
	 */
	public function getIndexName() {
		return $this->indexName;
	}

	/**
	 * @param field_type $indexName
	 */
	public function setIndexName($indexName) {
		$this->indexName = $indexName;
	}

	
	
}

