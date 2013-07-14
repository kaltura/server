<?php

abstract class WidevineVodBaseResponse
{
	private $id;
	private $name;
	private $status; 

	protected function setAttribute($attrName, $attrValue)
	{
		$method = "set{$attrName}"; 
		if(method_exists($this, $method))
			$this->$method($attrValue);
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $status
	 */
	public function getStatus() {
		return $this->status;
	}
	
		/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}
	
	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	
}