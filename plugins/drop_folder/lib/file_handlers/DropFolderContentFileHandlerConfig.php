<?php

class DropFolderContentFileHandlerConfig extends DropFolderFileHandlerConfig
{
		
	/**
	 * @var DropFolderContentFileHandlerMatchPolicy //TODO: change name
	 */
	private $contentMatchPolicy;
	
	/**
	 * @var string
	 */
	private $slugRegex;
	

	public function getHandlerType() {
		// override type parameter - should always be DropFolderFileHandlerType::CONTENT
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
	

	/**
	 * @return DropFolderContentFileHandlerMatchPolicy the $contentMatchPolicy
	 */
	public function getContentMatchPolicy() {
		return $this->contentMatchPolicy;
	}

	/**
	 * @param DropFolderContentFileHandlerMatchPolicy
	 */
	public function setContentMatchPolicy($contentMatchPolicy) {
		$this->contentMatchPolicy = $contentMatchPolicy;
	}
	
}