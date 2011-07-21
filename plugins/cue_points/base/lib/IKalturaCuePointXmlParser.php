<?php
/**
 * @package plugins.cuePoint
 * @subpackage plugins
 */
interface IKalturaCuePointXmlParser extends IKalturaBase
{
	/**
	 * @param SimpleXMLElement $scene
	 * @param int $partnerId
	 * @param CuePoint $cuePoint
	 * @return CuePoint
	 */
	public static function parseXml(SimpleXMLElement $scene, $partnerId, CuePoint $cuePoint = null);
}
