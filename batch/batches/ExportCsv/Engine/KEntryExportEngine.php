<?php
/**
 * @package Scheduler
 * @subpackage ExportCsv
 */

class KEntryExportEngine extends KMappedObjectExportEngine
{
	protected function getFilterOrderBy()
	{
		return KalturaBaseEntryOrderBy::CREATED_AT_ASC;
	}

	protected function getItemList($filter, $pager)
	{
		return KBatchBase::$kClient->baseEntry->listAction($filter, $pager);
	}

	protected function getDefaultHeaderRowToCsv()
	{
		return 'id,name';
	}

	protected function getDefaultRowValues($item)
	{
		return array(
			'id' => $item->id,
			'name' => $item->name ? $item->name : 'N/A',
		);
	}

	protected function getMetadataObjectType()
	{
		return MetadataObjectType::ENTRY;
	}
	
	protected function formatValue($value, $valueType)
	{
		if ($valueType == 'mediaType')
		{
			return $this->getEnumName($value, 'KalturaMediaType');
		}
		else if ($valueType == 'status')
		{
			return $this->getEnumName($value, 'KalturaEntryStatus');
		}
		else if ($valueType == 'createdAt' || $valueType == 'updatedAt')
		{
			return date('Y-m-d H:i:s', $value);
		}
		else if ($valueType == 'duration')
		{
			$formattedDuration =  intdiv($value, 60) . ':' . strval($value % 60);
			return $formattedDuration;
		}
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