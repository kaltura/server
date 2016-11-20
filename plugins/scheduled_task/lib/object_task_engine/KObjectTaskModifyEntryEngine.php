<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskModifyEntryEngine extends KObjectTaskEntryEngineBase
{
	/**
	 * @param KalturaBaseEntry $object
	 */
	function processObject($object)
	{
		/** @var KalturaModifyEntryObjectTask $objectTask */
		$objectTask = $this->getObjectTask();
		if (is_null($objectTask))
			return;

		KBatchBase::impersonate($object->partnerId);
		$client = $this->getClient();
		$metadataPlugin = KalturaMetadataClientPlugin::get($client);
		$entryId = $object->id;
		$outputMetadataProfileId = $objectTask->outputMetadataProfileId;
		$outputMetadataArr = $objectTask->outputMetadata;
		$inputMetadataProfileId = $objectTask->inputMetadataProfileId;
		$inputMetadataArr = $objectTask->inputMetadata;
		
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
		$metadataFilter->objectIdEqual = $entryId;
		
		if($outputMetadataProfileId != 0 && !empty($outputMetadataArr))
		{
			$entryResultForMetadataUpdate = $client->baseEntry->get($entryId);
			
			$metadataFilter->metadataProfileIdEqual = $outputMetadataProfileId;
			$this->updateMetadataObj($entryResultForMetadataUpdate, $metadataPlugin, $outputMetadataArr, $outputMetadataProfileId, $metadataFilter);
		}
		
		KalturaLog::info("updating entry $entryId");
		$entryObj = new KalturaBaseEntry();
		
		if($inputMetadataProfileId != 0 && !empty($inputMetadataArr))
		{
			$metadataFilter->metadataProfileIdEqual = $inputMetadataProfileId;
			
			$entryObj = $this->updateEntryFromMetadata($metadataPlugin, $inputMetadataArr, $entryObj, $metadataFilter);
		}

		if(is_null($entryObj->userId))
			$entryObj->userId = $objectTask->inputUserId;
		if(is_null($entryObj->entitledUsersEdit))
			$entryObj->entitledUsersEdit = $objectTask->inputEntitledUsersEdit;
		if(is_null($entryObj->entitledUsersPublish))
			$entryObj->entitledUsersPublish = $objectTask->inputEntitledUsersPublish;

		try
		{
			$client->baseEntry->update($entryId, $entryObj);
		}
		catch(Exception $e)
		{
			KalturaLog::notice("problem with entry update entry id $entryId - " . $e->getMessage());
		}
		KBatchBase::unimpersonate();
	}
	
	private function updateMetadataObj(KalturaBaseEntry $entryResultForMetadataUpdate, &$metadataPlugin, $outputMetadataArr, $outputMetadataProfileId, $metadataFilter)
	{
		$entryId = $entryResultForMetadataUpdate->id;
		
		try
		{
			$metadataInputResult = $metadataPlugin->metadata->listAction($metadataFilter, null);
		}
		catch(Exception $e)
		{
			KalturaLog::notice("problem with metadata list with entry id $entryId - " . $e->getMessage());
		}
		
		$tepmlateXmlObj = $this->getMetadataXmlTemplate($metadataPlugin, $outputMetadataProfileId);
	
		if($metadataInputResult && $metadataInputResult->totalCount != 0)
		{
			$metadataId = $metadataInputResult->objects[0]->id;
			$metadataInputXmlStr = $metadataInputResult->objects[0]->xml;
			$currentXmlObj = new SimpleXMLElement($metadataInputXmlStr);
			
			if(!is_null($tepmlateXmlObj))
			{
				$xmlData = $this->getUpdatedMetadataXmlStrFromEntry($entryResultForMetadataUpdate, $tepmlateXmlObj, $currentXmlObj, $outputMetadataArr);
			
				try
				{
					$metadataPlugin->metadata->update($metadataId, $xmlData, null);
				}
				catch(Exception $e)
				{
					KalturaLog::notice("problem with metadata update entry id $entryId - " . $e->getMessage());
				}
			}
		}
		else
		{
			
			$xmlObj = new SimpleXMLElement("<metadata></metadata>");
			$xmlData = $this->getUpdatedMetadataXmlStrFromEntry($entryResultForMetadataUpdate, $tepmlateXmlObj, $xmlObj, $outputMetadataArr);
	
			try
			{
				$metadataPlugin->metadata->add($outputMetadataProfileId, KalturaMetadataObjectType::ENTRY, $entryId, $xmlData);
			}
			catch(Exception $e)
			{
				KalturaLog::notice("problem with metadata add entry id $entryId - " . $e->getMessage());
			}
		}
	}
	
	private function getUpdatedMetadataXmlStrFromEntry(KalturaBaseEntry $entryResultForMetadataUpdate, SimpleXMLElement $emptyXmlObj, SimpleXMLElement $currentXmlObj, array $outputMetadataArr)
	{
		$tempXmlObj = $emptyXmlObj;		
		
		KalturaLog::debug("current xml object - " . print_r($currentXmlObj, true));
		KalturaLog::debug("output metadata array - " . print_r($outputMetadataArr, true));
		
		foreach($tempXmlObj as $metadataFieldName => $emptyXmlObjItem)
		{
			foreach($currentXmlObj as $currMetadataFieldName => $currentXmlObjItem)
			{
				if($currMetadataFieldName == $metadataFieldName && (string)$currentXmlObjItem != '')
				{
					$tempXmlObj->$metadataFieldName = $currentXmlObjItem;	
					continue 2;
				}				
			}
			
			foreach($outputMetadataArr as $outputMetadataArrItem)
			{
				if($outputMetadataArrItem->key == $metadataFieldName)
				{
					$entryFieldName = $outputMetadataArrItem->value;
					$emptyXmlObjItem = (string)$entryResultForMetadataUpdate->$entryFieldName;
					$tempXmlObj->$metadataFieldName = (string)$entryResultForMetadataUpdate->$entryFieldName;
					break;
				}
			}
		}
		return $tempXmlObj->asXml();
	}
	
	private function updateEntryFromMetadata($metadataPlugin, array $inputMetadataArr, KalturaBaseEntry $entryObj, $metadataFilter)
	{
		$entryId = $entryObj->id;
		
		try
		{
			$metadataInputResult = $metadataPlugin->metadata->listAction($metadataFilter, null);
		}
		catch(Exception $e)
		{
			KalturaLog::notice("problem with metadata list - " . $e->getMessage());
		}
	
		if($metadataInputResult->totalCount != 0)
		{
			$metadataInputXmlStr = $metadataInputResult->objects[0]->xml;
			$xmlObj = new SimpleXMLElement($metadataInputXmlStr);
			
			foreach($inputMetadataArr as $inputMetadataItem)
			{
				$xpathName = $inputMetadataItem->key;
				$fieldName = $inputMetadataItem->value;
	
				$metadataValue = $xmlObj->$xpathName;
				$entryObj->$fieldName = (string)$metadataValue;
			}	
		}
		else
			KalturaLog::info("found no input metadata objects for entry $entryId");
		
		return $entryObj;
	}
	
	private function getMetadataXmlTemplate($metadataPlugin, $outputMetadataProfileId)
	{
		try
		{
			$outputMetadataProfile = $metadataPlugin->metadataProfile->get($outputMetadataProfileId);
		}
		catch(Exception $e)
		{
			KalturaLog::notice("problem with metadataProfile get entry id $entryId - " . $e->getMessage());
			return null;
		}
		
		$metadataXsd = $outputMetadataProfile->xsd;
		
		$xsdSchema = new DOMDocument();
		$xsdSchema->loadXml($metadataXsd);
		
		$elements = $xsdSchema->getElementsByTagName('element');
		$emptyXmlObj = new SimpleXMLElement("<metadata></metadata>");
		
		foreach($elements as $element)
		{
			if ($element->hasAttribute('type') == false)
				continue;
			$key = $element->getAttribute('name');
			$emptyXmlObj->addChild($key);
		}

		KalturaLog::debug("metadata profile schema - " . $emptyXmlObj->asXml());

		return $emptyXmlObj;
	}
}
