<?php
/**
 * @package plugins.msnDistribution
 * @subpackage model
 */
class MsnDistributionProfileValidator
{
	public static function validateForSubmission(MsnDistributionProfile $distributionProfile, EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$requiredFields = array(
			self::METADATA_FIELD_VIDEO_CAT,
			self::METADATA_FIELD_VIDEO_TOP,
			self::METADATA_FIELD_VIDEO_TOP_CAT,
			self::METADATA_FIELD_PUBLIC,
		);
		
		$metadataProfileId = $distributionProfile->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			foreach($requiredFields as $field)
				$validationErrors[] = $distributionProfile->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
			return $validationErrors;
		}
	
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!count($metadatas))
		{
			foreach($requiredFields as $field)
				$validationErrors[] = $distributionProfile->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
			return $validationErrors;
		}
		
		foreach($requiredFields as $field)
		{
			$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, $field);
			if(!$metadataProfileCategoryField)
			{
				$validationErrors[] = $distributionProfile->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
				continue;
			}
		
			$values = $distributionProfile->findMetadataValue($metadatas, $field);
			if(!count($values))
			{
				$validationErrors[] = $distributionProfile->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
				continue;
			}
				
			foreach($values as $value)
			{
				if(!strlen($value))
				{
					$validationError = $distributionProfile->createValidationError($action, DistributionErrorType::INVALID_DATA, $field);
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
					$validationError->setMetadataProfileId($metadataProfileId);
					$validationErrors[] = $validationError;
					return $validationErrors;
				}
			}
		}
		
		return $validationErrors;
	}
}