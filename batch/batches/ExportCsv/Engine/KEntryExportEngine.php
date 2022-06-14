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
	
	protected function getTitleHeader()
	{
		return "#-----------------------------------------------\n" .
			"Report: Entries\n" .
			"Please note that the data below is filtered based on the filter applied in the report\n" .
			"#-----------------------------------------------";
	}
	
	protected function formatValue($value, $valueType)
	{
		$dateFormatTypes = array('createdAt', 'updatedAt');
		
		if ($valueType == 'mediaType')
		{
			return $this->getEnumName($value, 'KalturaMediaType');
		}
		else if ($valueType == 'status')
		{
			return $this->getEnumName($value, 'KalturaEntryStatus');
		}
		else if (in_array($valueType, $dateFormatTypes))
		{
			return date('Y-m-d H:i:s', $value);
		}
		else if ($valueType == 'duration')
		{
			// Duration format is minutes:seconds
			$formattedDuration = intdiv($value, 60) . ':' . strval($value % 60);
			return $formattedDuration;
		}
		return $value;
	}
}