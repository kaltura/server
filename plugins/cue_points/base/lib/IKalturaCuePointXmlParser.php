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
	
	/**
	 * @param CuePoint $cuePoint
	 * @param SimpleXMLElement $scenes the parent node
	 * @param SimpleXMLElement $scene the node
	 * @return SimpleXMLElement the created node
	 */
	public static function generateXml(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null);
	
	/**
	 * @param CuePoint $cuePoint
	 * @param SimpleXMLElement $scenes the parent node
	 * @param SimpleXMLElement $scene the node
	 * @return SimpleXMLElement the created node
	 */
	public static function syndicate(CuePoint $cuePoint, SimpleXMLElement $scenes, SimpleXMLElement $scene = null);
}
