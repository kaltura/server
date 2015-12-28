
<?php
/**
 * @package plugins.integration
 * @subpackage api.enum
 * @see KalturaVisualRecognitionAdultContentPolicy
 */
class KalturaVisualRecognitionAdultContentPolicy extends KalturaDynamicEnum implements VisualRecognitionAdultContentPolicy
{
	public static function getEnumClass()
	{
		return 'VisualRecognitionAdultContentPolicy';
	}
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
