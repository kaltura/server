
<?php

/**
 * Basic entry vendor task job data 
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kTranslationEntryVendorTaskJobData extends kCaptionsEntryVendorTaskJobData
{
	/**
	 * @var string
	 */
	public $targetLanguage;
	
	/**
	 * @return the $targetLanguage
	 */
	public function getEnableSpeakerId() { return $this->targetLanguage; }
	
	/**
	 * @param bool $targetLanguage
	 */
	public function setEnableSpeakerId($targetLanguage) { $this->targetLanguage = $targetLanguage; }
}