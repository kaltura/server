<?php

/**
 * Enter description here ...
 * @author Dor
 *
 */
class DropFolderFileHandlerConfig
{
	
	/**
	 * Type of the relevant file handler
	 * @var DropFolderFileHandlerType
	 */
	protected $handlerType;
			
	
	/**
	 * @return the $handlerType
	 */
	public function getHandlerType() {
		return $this->handlerType;
	}

	/**
	 * @param DropFolderFileHandlerType $handlerType
	 */
	public function setHandlerType($handlerType) {
		$this->handlerType = $handlerType;
	}

	
	
}