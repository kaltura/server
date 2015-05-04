<?php
/**
 * @package plugins.exampleIntegration
 * @subpackage model.data
 */
class kExampleIntegrationJobProviderData extends kIntegrationJobProviderData
{
	/**
	 * @var string
	 */
	private $exampleUrl;
	
	/**
	 * @return string
	 */
	public function getExampleUrl()
	{
		return $this->exampleUrl;
	}

	/**
	 * @param string $exampleUrl
	 */
	public function setExampleUrl($exampleUrl)
	{
		$this->exampleUrl = $exampleUrl;
	}
}