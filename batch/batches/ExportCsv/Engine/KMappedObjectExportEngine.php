<?php

/**
 * @package Scheduler
 * @subpackage ExportCsv
 */
abstract class KMappedObjectExportEngine extends KObjectExportEngine
{
	abstract protected function getFilterOrderBy();
	abstract protected function getItemList($filter, $pager);
	abstract protected function getDefaultHeaderRowToCsv();
	abstract protected function getDefaultRowValues($item);
	abstract protected function getMetadataObjectType();
	abstract protected function getTitleHeader();

	protected function getMappedFieldsAsAssociativeArray($mappedFields)
	{
		$ret = array();
		if($mappedFields)
		{
			foreach($mappedFields as $mappedField)
			{
				$predefinedFormat = false;
				if (isset($mappedField->predefinedFormat) && $mappedField->predefinedFormat)
				{
					$predefinedFormat = true;
				}
				$ret[$mappedField->key] = array('value' => $mappedField->value, 'format' => $predefinedFormat);
			}
		}
		return $ret;
	}

	public function fillCsv (&$csvFile, &$data)
	{
		KalturaLog::info ('Exporting content items');

		$filter = clone $data->filter;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;
		
		if (isset($data->options->defaultHeader) && $data->options->defaultHeader)
		{
			$this->addTitleHeaderToCsv($csvFile);
		}

		$mappedFields = $this->getMappedFieldsAsAssociativeArray($data->mappedFields);

		$this->addHeaderRowToCsv($csvFile, $data->additionalFields, $mappedFields);

		$lastCreatedAtObjectIdList = array();
		$totalCount = 0;

		$filter->orderBy = $this->getFilterOrderBy();
		do
		{
			try
			{
				$itemList = $this->getItemList($filter, $pager);
			}
			catch(Exception $e)
			{
				KalturaLog::info('Could not list items' . $e->getMessage());
				return;
			}

			if(!$itemList->objects)
			{
				break;
			}

			$returnedSize = $itemList->objects ? count($itemList->objects) : 0;

			$lastObject = $itemList->objects[$returnedSize - 1];
			$filter->createdAtGreaterThanOrEqual = $lastObject->createdAt;
			$newCreatedAtListObject = array();

			//contain only the items that are were not in the former list
			$uniqItems = array();
			foreach ($itemList->objects as $item)
			{
				if(!in_array($item->id, $lastCreatedAtObjectIdList))
				{
					$uniqItems[] = $item;
					if($item->createdAt == $lastObject->createdAt)
					{
						$newCreatedAtListObject[]= $item->id;
					}
				}
			}

			$lastCreatedAtObjectIdList = $newCreatedAtListObject;
			$this->addItemsToCsv($uniqItems, $csvFile, $data->metadataProfileId, $data->additionalFields, $mappedFields);
			$totalCount += count($uniqItems);
			KalturaLog::debug('Adding More  - ' .count($uniqItems). ' totalCount - ' . $totalCount);

			unset($newCreatedAtListObject);
			unset($uniqItems);
			unset($itemList);

			if(function_exists('gc_collect_cycles')) // php 5.3 and above
			{
				gc_collect_cycles();
			}
		} while ($pager->pageSize == $returnedSize);
	}
	
	protected function addTitleHeaderToCsv($csvFile)
	{
		$titleHeader = $this->getTitleHeader();
		KCsvWrapper::sanitizedFputCsv($csvFile, array($titleHeader));
	}

	protected function addHeaderRowToCsv($csvFile, $additionalFields, $mappedFields = null)
	{
		$headerRow = $this->getDefaultHeaderRowToCsv();

		foreach ($mappedFields as $key => $value)
		{
			$headerRow .= ',' . $key;
		}

		if($additionalFields)
		{
			foreach ($additionalFields as $field)
			{
				$headerRow .= ','.$field->fieldName;
			}
		}

		KCsvWrapper::sanitizedFputCsv($csvFile, explode(',', $headerRow));

		return $csvFile;
	}

	/**
	 * The function grabs all the fields values for each item and adding them as a new row to the csv file
	 */
	protected function addItemsToCsv($items, &$csvFile, $metadataProfileId, $additionalFields, $mappedFields)
	{
		$itemIds = array();
		$csvRows = array();

		foreach ($items as $item)
		{
			$itemIds[] = $item->id;
			$csvRows = $this->initializeCsvRowValues($item, $additionalFields, $csvRows, $mappedFields);
		}

		if($metadataProfileId)
		{
			$itemsMetadataObjects = $this->retrieveUsersMetadata($itemIds, $metadataProfileId);
			if ($itemsMetadataObjects)
			{
				$csvRows = $this->fillAdditionalFieldsFromMetadata($itemsMetadataObjects, $additionalFields, $csvRows);
			}
		}

		foreach ($csvRows as $key => $val)
		{
			KCsvWrapper::sanitizedFputCsv($csvFile, $val);
		}
	}

	/**
	 * adds the default fields values and the additional fields as nulls
	 */
	protected function initializeCsvRowValues($item, $additionalFields, $csvRows, $mappedFields)
	{
		$defaultRowValues = $this->getDefaultRowValues($item);

		//add mapped fields
		foreach($mappedFields as $key => $fields)
		{
			$value = $fields['value'];
			
			//if only key
			if(!isset($value))
			{
				$itemValue = isset($item->$key) ? $item->$key : '';
				$defaultRowValues[$key] = $itemValue;
				if ($fields['format'])
				{
					$defaultRowValues[$key] = $this->formatValue($itemValue, $key);
				}
				continue;
			}

			$fieldMap = explode(':', $value);

			//if simple value
			if(count($fieldMap) == 1)
			{
				$itemField = $value;
				$itemValue = isset($item->$itemField) ? $item->$itemField : '';
				$defaultRowValues[$key] = $itemValue;
				if ($fields['format'])
				{
					$defaultRowValues[$key] = $this->formatValue($itemValue, $value);
				}
				continue;
			}

			//if value maps to a sub fields
			$fieldName = $fieldMap[0];
			$subFieldName = $fieldMap[1];

			if(isset($item->$fieldName))
			{
				$fieldValues = json_decode($item->$fieldName);
				if($fieldValues)
				{
					$defaultRowValues[$key] = isset($fieldValues->$subFieldName) ? $fieldValues->$subFieldName : '';
				}
			}
			else
			{
				$defaultRowValues[$key] = '';
			}
		}

		$additionalKeys = array();
		foreach ($additionalFields as $field)
		{
			$additionalKeys[$field->fieldName] = '';
		}

		$csvRows[$item->id] = array_merge($defaultRowValues, $additionalKeys);;

		return $csvRows;
	}

	/**
	 * Retrieve all the metadata objects for all the users in specific page
	 */
	protected function retrieveUsersMetadata($itemIds, $metadataProfileId)
	{
		$result = array();

		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 0;

		$filter = new KalturaMetadataFilter();
		$filter->objectIdIn = implode(',', $itemIds);
		$filter->metadataObjectTypeEqual = $this->getMetadataObjectType();
		$filter->metadataProfileIdEqual = $metadataProfileId;

		$metadataClient = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		do
		{
			$pager->pageIndex++;

			try
			{
				$ret = $metadataClient->metadata->listAction($filter, $pager);
			}
			catch (Exception $e)
			{
				KalturaLog::info("Couldn't list metadata objects for metadataProfileId: [$metadataProfileId]" . $e->getMessage());
				break;
			}

			if(count($ret->objects))
			{
				$result = array_merge($result, $ret->objects);
			}

		}
		while(count($ret->objects) >= $pager->pageSize);

		return $result;
	}

	/**
	 * Extract specific value from xml using given xpath
	 */
	protected function getValueFromXmlElement($xml, $xpath)
	{
		$strValue = null;
		try
		{
			$xmlObj = new SimpleXMLElement($xml);
		}
		catch(Exception $ex)
		{
			return null;
		}

		$value = $xmlObj->xpath($xpath);
		if(is_array($value) && count($value) == 1)
		{
			$strValue = (string)$value[0];
		}
		else if (count($value) == 1)
		{
			KalturaLog::err("Unknown element in the base xml when quering the xpath: [$xpath]");
		}

		return $strValue;
	}

	/**
	 * the function run over each additional field and returns the value for the given field xpath
	 */
	protected function fillAdditionalFieldsFromMetadata($itemsMetadataObjects, $additionalFields, $csvRows)
	{
		foreach($itemsMetadataObjects as $metadataObj)
		{
			foreach ($additionalFields as $field)
			{
				if($field->xpath)
				{
					KalturaLog::info("current field xpath: [$field->xpath]");
					$strValue = $this->getValueFromXmlElement($metadataObj->xml, $field->xpath);
					if($strValue)
					{
						$objectRow = $csvRows[$metadataObj->objectId];
						$objectRow[$field->fieldName] = $strValue;
						$csvRows[$metadataObj->objectId] = $objectRow;
					}
				}
			}
		}
		return $csvRows;
	}
	
	protected function formatValue($value, $valueType)
	{
		return $value;
	}
	
	protected function getEnumName($value, $enumClass)
	{
		$oClass = new ReflectionClass($enumClass);
		$constants = $oClass->getConstants();
		foreach ($constants as $enumName => $enumValue)
		{
			if ($value == $enumValue)
			{
				return $enumName;
			}
		}
	}
}
