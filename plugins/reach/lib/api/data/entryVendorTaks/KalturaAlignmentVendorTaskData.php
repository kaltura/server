<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaAlignmentVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * The id of the text transcript object the vendor should use while runing the alignment task
	 * @var string
	 */
	public $textTranscriptAssetId;
	
	/**
	 * Optional - The id of the json transcript object the vendor should update once alignment task processing is done
	 * @insertonly
	 * @var string
	 */
	public $jsonTranscriptAssetId;
	
	/**
	 * Optional - The id of the caption asset object the vendor should update once alignment task processing is done
	 * @insertonly
	 * @var string
	 */
	public $captionAssetId;
	
	private static $map_between_objects = array
	(
		'textTranscriptAssetId',
		'jsonTranscriptAssetId',
		'captionAssetId',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kAlignmentVendorTaskData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new kAlignmentVendorTaskData();
		}
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("textTranscriptAssetId");
		$this->validateTranscriptAsset($this->textTranscriptAssetId, KalturaAttachmentType::TEXT);
		
		if($this->jsonTranscriptAssetId)
		{
			$this->validateTranscriptAsset($this->jsonTranscriptAssetId, KalturaAttachmentType::JSON);
		}
		
		if($this->captionAssetId)
		{
			$this->validateCaptionAsset($this->captionAssetId);
		}

		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject kAlignmentVendorTaskData */
		if(isset($this->textTranscriptAssetId) && $sourceObject->getTextTranscriptAssetId() != $this->textTranscriptAssetId)
		{
			$this->validateTranscriptAsset($this->textTranscriptAssetId, KalturaAttachmentType::TEXT);
		}
		
		/* @var $sourceObject kAlignmentVendorTaskData */
		if(isset($this->jsonTranscriptAssetId) && $sourceObject->getJsonTranscriptAssetId() != $this->jsonTranscriptAssetId)
		{
			$this->validateTranscriptAsset($this->jsonTranscriptAssetId, KalturaAttachmentType::JSON);
		}
		
		/* @var $sourceObject kAlignmentVendorTaskData */
		if(isset($this->captionAssetId) && $sourceObject->getCaptionAssetId() != $this->captionAssetId)
		{
			$this->validateCaptionAsset($this->captionAssetId);
		}

		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	protected function validateTranscriptAsset($transcriptAssetId, $expectedType)
	{
		$transcriptAssetDb = assetPeer::retrieveById($transcriptAssetId);
		if (!$transcriptAssetDb || !($transcriptAssetDb instanceof TranscriptAsset))
		{
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_ID_NOT_FOUND, $transcriptAssetId);
		}
		
		/* @var $transcriptAssetDb TranscriptAsset */
		if($transcriptAssetDb->getContainerFormat() != $expectedType)
		{
			throw new KalturaAPIException(KalturaAttachmentErrors::ATTACHMENT_ASSET_FORMAT_MISMATCH, $transcriptAssetId, $expectedType);
		}
	}
}
