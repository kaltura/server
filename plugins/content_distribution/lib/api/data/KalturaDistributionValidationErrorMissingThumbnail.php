<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionValidationErrorMissingThumbnail extends KalturaDistributionValidationError
{
	/**
	 * @var KalturaDistributionThumbDimensions
	 */
	public $dimensions;

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return null;
			
		parent::toObject($dbObject, $skip);
		
		if($this->dimensions)
		{
			$key = $this->dimensions->width . 'x' . $this->dimensions->height;
			$dbObject->setData($key);
		}

		return $dbObject;
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!$sourceObject)
			return;
			
		parent::doFromObject($sourceObject, $responseProfile);
		
		if($this->shouldGet('dimensions', $responseProfile))
		{
			$data = $sourceObject->getData();
			$matches = null;
			if(preg_match('/(\d+)x(\d+)/', $data, $matches))
			{
				$this->dimensions = new KalturaDistributionThumbDimensions();
				$this->dimensions->width = $matches[1];
				$this->dimensions->height = $matches[2];
			}
		}
	}
}