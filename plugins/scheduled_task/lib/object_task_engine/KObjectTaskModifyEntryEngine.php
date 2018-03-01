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
		
		KalturaLog::debug("updating entry $entryId");
		$entryObj = new KalturaBaseEntry();
		
		if($inputMetadataProfileId != 0 && !empty($inputMetadataArr))
		{
			$metadataFilter->metadataProfileIdEqual = $inputMetadataProfileId;
			$entryObj = $this->updateEntryFromMetadata($metadataPlugin, $inputMetadataArr, $entryObj, $metadataFilter);
		}

		$entryObj->userId = is_null($entryObj->userId) ? $objectTask->inputUserId : null;
		$entryObj->entitledUsersEdit = is_null($entryObj->entitledUsersEdit) ? $objectTask->inputEntitledUsersEdit : null;
		$entryObj->entitledUsersPublish = is_null($entryObj->entitledUsersPublish) ? $objectTask->inputEntitledUsersPublish : null;

		$client->baseEntry->update($entryId, $entryObj);
	}
	
	private function updateMetadataObj(KalturaBaseEntry $entryResultForMetadataUpdate, &$metadataPlugin, $outputMetadataArr, $outputMetadataProfileId, $metadataFilter)
	{
		$entryId = $entryResultForMetadataUpdate->id;
		
		$metadataInputResult = $metadataPlugin->metadata->listAction($metadataFilter, null);

		$tepmlateXmlObj = $this->getMetadataXmlTemplate($metadataPlugin, $outputMetadataProfileId);
	
		if($metadataInputResult && $metadataInputResult->totalCount != 0)
		{
			$metadataId = $metadataInputResult->objects[0]->id;
			$metadataInputXmlStr = $metadataInputResult->objects[0]->xml;
			$currentXmlObj = new SimpleXMLElement($metadataInputXmlStr);
			
			if(!is_null($tepmlateXmlObj))
			{
				$xmlData = $this->getUpdatedMetadataXmlStrFromEntry($entryResultForMetadataUpdate, $tepmlateXmlObj, $currentXmlObj, $outputMetadataArr);			
				$metadataPlugin->metadata->update($metadataId, $xmlData, null);
			}
		}
		else
		{
			
			$xmlObj = new SimpleXMLElement("<metadata></metadata>");
			$xmlData = $this->getUpdatedMetadataXmlStrFromEntry($entryResultForMetadataUpdate, $tepmlateXmlObj, $xmlObj, $outputMetadataArr);
	
			$metadataPlugin->metadata->add($outputMetadataProfileId, KalturaMetadataObjectType::ENTRY, $entryId, $xmlData);
		}
	}
	
	private function getUpdatedMetadataXmlStrFromEntry(KalturaBaseEntry $entryResultForMetadataUpdate, SimpleXMLElement $templateXmlObj, SimpleXMLElement $currentXmlObj, array $outputMetadataArr)
	{		
		KalturaLog::debug("current xml object - " . print_r($currentXmlObj, true));
		KalturaLog::debug("output metadata array - " . print_r($outputMetadataArr, true));
		
		foreach($templateXmlObj as $metadataFieldName => $templateXmlObjItem)
		{
			$element = $currentXmlObj->xpath($metadataFieldName);
			if(isset($element[0]) &&  isset($element[0][0]) && $element[0][0] != '')
			{
				$templateXmlObj->$metadataFieldName = $element[0][0];
				continue;
			}

			foreach($outputMetadataArr as $outputMetadataArrItem)
			{
				if($outputMetadataArrItem->key == $metadataFieldName)
				{
					$entryFieldName = $outputMetadataArrItem->value;
					$entryFieldValue = (string)$entryResultForMetadataUpdate->$entryFieldName;
					$templateXmlObj->$metadataFieldName = $entryFieldValue;
					break;
				}
			}
		}
		return $templateXmlObj->asXml();
	}
	
	private function updateEntryFromMetadata($metadataPlugin, array $inputMetadataArr, KalturaBaseEntry $entryObj, $metadataFilter)
	{
		$entryId = $entryObj->id;
		$metadataInputResult = $metadataPlugin->metadata->listAction($metadataFilter, null);
	
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
			if ($element->hasAttribute('type') != false)
			{
				$key = $element->getAttribute('name');
				$emptyXmlObj->addChild($key);
			}
		}

		KalturaLog::debug("metadata profile schema - " . $emptyXmlObj->asXml());

		return $emptyXmlObj;
	}
}
