<?php
/**
 * @package infra
 * @subpackage Plugins
 */
class KalturaDependency
{
	/**
	 * @var string
	 */
	protected $pluginName;
	
	/**
	 * @var KalturaVersion
	 */
	protected $minVersion;
	
	/**
	 * @param string $pluginName
	 * @param KalturaVersion $minVersion
	 */
	public function __construct($pluginName, KalturaVersion $minVersion = null)
	{
		$this->pluginName = $pluginName;
		$this->minVersion = $minVersion;
	}
	
	/**
	 * @return string plugin name
	 */
	public function getPluginName()
	{
		return $this->pluginName;
	}

	/**
	 * @return KalturaVersion minimum version
	 */
	public function getMinimumVersion()
	{
		return $this->minVersion;
	}
}