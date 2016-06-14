<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaHTMLPurifierBehaviourType extends KalturaDynamicEnum implements HTMLPurifierBehaviourType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'HTMLPurifierBehaviourType';
	}
}
