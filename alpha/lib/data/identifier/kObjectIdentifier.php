<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class kObjectIdentifier
{
	/**
	 * @var string
	 */
	protected $identifier;
	
	/**
	 * @var array
	 */
	protected $extendedFeatures;
	
	/**
	 * @return the $identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}
	
	/**
	 * @return the $extendedFeatures
	 */
	public function getExtendedFeatures() {
		return $this->extendedFeatures;
	}

	/**
	 * @param string $extendedFeatures
	 */
	public function setExtendedFeatures($extendedFeatures) {
		$this->extendedFeatures = $extendedFeatures;
	}
	
	/**
	 * Function returns the object according to the identifier
	 * @param mixed <string|int> $value
	 * @throws kCoreException
	 * @return BaseObject
	 */
	abstract public function retrieveByIdentifier ($value);
	
}