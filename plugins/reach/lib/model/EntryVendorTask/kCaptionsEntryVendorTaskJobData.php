
<?php

/**
 * Basic entry vendor task job data 
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kCaptionsEntryVendorTaskJobData extends kEntryVendorTaskJobData
{
	/**
	 * @var string
	 */
	public $sourceLanguage;
	
	/**
	 * @var int
	 */
	public $outputFormat;
	
	/**
	 * @var bool
	 */
	public $enableSpeakerId;
	
	/**
	 * @return the $sourceLanguage
	 */
	public function getSourceLanguage() { return $this->sourceLanguage; }
	
	/**
	 * @return the $outputFormat
	 */
	public function getOutputFormat() { return $this->outputFormat; }
	
	/**
	 * @return the $enableSpeakerId
	 */
	public function getEnableSpeakerId() { return $this->enableSpeakerId; }
	
	/**
	 * @param string $sourceLanguage
	 */
	public function setSourceLanguage($sourceLanguage) { $this->sourceLanguage = $sourceLanguage; }
	
	/**
	 * @param int $outputFormat
	 */
	public function setOutputFormat($outputFormat) { $this->outputFormat = $outputFormat; }
	
	/**
	 * @param bool $enableSpeakerId
	 */
	public function setEnableSpeakerId($enableSpeakerId) { $this->enableSpeakerId = $enableSpeakerId; }
	
}