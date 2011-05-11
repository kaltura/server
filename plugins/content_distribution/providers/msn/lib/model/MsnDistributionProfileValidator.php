<?php
/**
 * @package plugins.msnDistribution
 * @subpackage model
 */
class MsnDistributionProfileValidator
{
	protected static function getMetadataValidationFields()
	{
		return array(
			MsnDistributionProfile::METADATA_FIELD_VIDEO_CAT,
			MsnDistributionProfile::METADATA_FIELD_VIDEO_TOP,
			MsnDistributionProfile::METADATA_FIELD_VIDEO_TOP_CAT,
			MsnDistributionProfile::METADATA_FIELD_PUBLIC,
		);
	}
	
	public static function validateForSubmission(MsnDistributionProfile $distributionProfile, EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$requiredFields = self::getMetadataValidationFields();
		
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