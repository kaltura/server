<?php
/**
 * @package plugins.group
 */
class GroupPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending
{
	const PLUGIN_NAME = 'group';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}

	public static function dependsOn()
	{
		$eSearchDependency = new KalturaDependency(ElasticSearchPlugin::getPluginName());
		return array($eSearchDependency);
	}

	public static function getServicesMap ()
	{
		$map = array(
			'group' => 'GroupService',
		);
		return $map;
	}
}