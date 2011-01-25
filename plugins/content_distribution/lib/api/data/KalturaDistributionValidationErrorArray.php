<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaDistributionValidationErrorArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr)
	{
		$newArr = new KalturaDistributionValidationErrorArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = null;
			switch($obj->getErrorType())
			{
				case DistributionErrorType::MISSING_FLAVOR:
    				$nObj = new KalturaDistributionValidationErrorMissingFlavor();
    				break;
    			
				case DistributionErrorType::MISSING_THUMBNAIL:
    				$nObj = new KalturaDistributionValidationErrorMissingThumbnail();
    				break;
    			
				case DistributionErrorType::MISSING_METADATA:
    				$nObj = new KalturaDistributionValidationErrorMissingMetadata();
    				break;
    			
				case DistributionErrorType::INVALID_DATA:
    				$nObj = new KalturaDistributionValidationErrorInvalidMetadata();
    				break;
    			
				default:
					break;
			}
			
			if(!$nObj)
				continue;
				
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDistributionValidationError");	
	}
}