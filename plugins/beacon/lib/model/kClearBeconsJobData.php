<?php

/**
 * @package plugins.beacon
 * @subpackage model
 */

class kClearBeconsJobData extends kJobData
{
	/**
	 * The object Id to clear beacons for
	 *
	 * @var string
	 */
	protected $objectId;
	
	/**
	 * The beacon object type
	 * @var int
	 */
	protected $relatedObjectType;
	
	/**
	 * @param string $objectId
	 */
	public function setObjectId($objectId) { $this->objectId = $objectId; }
	
	/**
	 * @param int $relatedObjectType
	 */
	public function setRelatedObjectType($relatedObjectType) { $this->relatedObjectType = $relatedObjectType; }
	
	/**
	 * @return the $objectId
	 */
	public function getObjectId() { return $this->objectId; }
	
	/**
	 * @return the $relatedObjectType
	 */
	public function getRelatedObjectType() { return $this->relatedObjectType; }
}