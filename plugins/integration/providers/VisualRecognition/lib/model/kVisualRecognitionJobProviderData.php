<?php
/**
 * @package plugins.visualRecognition
 * @subpackage model.data
 */
class VisualRecognitionJobProviderData extends kIntegrationJobProviderData
{
	/**
	 * @var string
	 */
	private  $recognizeElementURL;
	
	/**
	 * @return string
	 */
	public function getExampleUrl()
	{
		return $this->recognizeElementURL;
	}

	/**
	 * @param string $recognizeElementURL
	 */
	public function setRecognizeElementUrl($recognizeElementURL)
	{
		$this->recognizeElementURL = $recognizeElementURL;
	}
}