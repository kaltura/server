<?php

class DropFolderContentFileHandlerConfig extends DropFolderFileHandlerConfig
{
		
	//TODO: to slug or not to slug ?!?
	
	private $slugRegex;
	
	/**
	 * @var DropFolderUnmatchedContentFilePolicy
	 */
	private $unmatchedFilePolicy;
	

	// override type parameter - should always be DropFolderFileHandlerType::CONTENT
	public function getHandlerType() {
		return DropFolderFileHandlerType::CONTENT;
	}
	

	/**
	 * @return string the $slugRegex
	 */
	public function getSlugRegex() {
		return $this->slugRegex;
	}

	/**
	 * @param string $slugRegex
	 */
	public function setSlugRegex($slugRegex) {
		$this->slugRegex = $slugRegex;
	}	
	
}