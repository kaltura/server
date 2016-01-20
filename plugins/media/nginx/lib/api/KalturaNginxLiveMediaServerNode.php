<?php
/**
 * @package plugins.nginxLive
 * @subpackage api.objects
 */
class KalturaNginxLiveMediaServerNode extends KalturaMediaServerNode
{	
	/**
	 * Nginx-Live Media server app prefix
	 *
	 * @var string
	 */
	public $appPrefix;
	
	private static $mapBetweenObjects = array
	(
		'appPrefix',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		return parent::validateForInsertByType($propertiesToSkip, NginxLivePlugin::getNginxLiveMediaServerTypeCoreValue(NginxLiveMediaServerNodeType::NGINX_LIVE_MEDIA_SERVER));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		return parent::validateForUpdateByType($sourceObject, $propertiesToSkip, NginxLivePlugin::getNginxLiveMediaServerTypeCoreValue(NginxLiveMediaServerNodeType::NGINX_LIVE_MEDIA_SERVER));
	}
	
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new NginxLiveMediaServerNode();
	
		return parent::toObject($dbObject, $skip);
	}
}
