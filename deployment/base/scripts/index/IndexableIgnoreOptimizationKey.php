<?php

/**
 * This class is a container class for all indexing properties about
 * a single Sphinx-IgnoreOptimizationKey.php object
 */
class IndexableIgnoreOptimizationKey {

	private $name;
	private $getter;

	public function __construct($name, $getter) {
		$this->name = $name;
		$this->getter = $getter;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $getter
	 */
	public function getGetter() {
		return $this->getter;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $getter
	 */
	public function setGetter($getter) {
		$this->getter = $getter;
	}
}

