<?php

class ContentDropFolderFileHandlerConfig extends DropFolderFileHandlerConfig
{
	
	private $slugField;
	
	private $slugRegex;
	

	// override type parameter - should always be DropFolderFileHandlerType::CONTENT
	public function getHandlerType() {
		return DropFolderFileHandlerType::CONTENT;
	}
	
	
	/**
	 * @return string the $slugField
	 */
	public function getSlugField() {
		return $this->slugField;
	}

	/**
	 * @param string $slugField
	 */
	public function setSlugField($slugField) {
		$this->slugField = $slugField;
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