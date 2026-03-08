<?php
/**
 * @package plugins.markdown
 * @subpackage api.objects
 */
class KalturaMarkdownAsset extends KalturaAttachmentAsset
{
	/**
	 * The percentage accuracy of the markdown - values between 0 and 100
	 * @var int
	 * @minValue 0
	 * @maxValue 100
	 */
	public $accuracy;
	
	/**
	 * The provider of the markdown
	 * @var KalturaMarkdownProviderType
	 */
	public $providerType;
	
	private static $map_between_objects = array
	(
		"accuracy",
		"providerType",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new MarkdownAsset();
	
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		if($this->accuracy)
		{
			$this->validatePropertyMinMaxValue('accuracy', 0, 100, true);
		}
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->accuracy && $this->accuracy != $sourceObject->getAccuracy())
		{
			$this->validatePropertyMinMaxValue('accuracy', 0, 100, true);
		}
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
