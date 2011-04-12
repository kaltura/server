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
	 * File pattern 
	 * @var string
	 */
	protected $filePattern;
		
	
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

	/**
	 * @return the $filePattern
	 */
	public function getFilePattern() {
		return $this->filePattern;
	}

	/**
	 * @param string $filePattern
	 */
	public function setFilePattern($filePattern) {
		$this->filePattern = $filePattern;
	}
	
	
}