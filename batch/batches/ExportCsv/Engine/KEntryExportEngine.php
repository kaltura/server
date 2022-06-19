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
		
		$typeMapping = array(
			'VIDEO' => 'Video',
			'IMAGE' => 'Image',
			'AUDIO' => 'Audio',
			'LIVE_STREAM_FLASH' => 'Live',
			'LIVE_STREAM_WINDOWS_MEDIA' => 'Live',
			'LIVE_STREAM_REAL_MEDIA' => 'Live',
			'LIVE_STREAM_QUICKTIME' => 'Live',
		);
		
		$statusMapping = array(
			'ERROR_IMPORTING' => 'Error Uploading',
			'ERROR_CONVERTING' => 'Error',
			'IMPORT' => 'Uploading',
			'PRECONVERT' => 'Converting',
			'READY' => 'Ready',
			'DELETED' => 'Deleted',
			'PENDING' => 'Pending',
			'MODERATE' => 'Moderate',
			'BLOCKED' => 'Blocked',
			'NO_CONTENT' => 'No Media',
		);
		
		if ($valueType == 'mediaType')
		{
			$enumName = $this->getEnumName($value, 'KalturaMediaType');
			if (!isset($typeMapping[$enumName]))
			{
				return $enumName;
			}
			return $typeMapping[$enumName];
		}
		else if ($valueType == 'status')
		{
			$enumName = $this->getEnumName($value, 'KalturaEntryStatus');
			if (!isset($statusMapping[$enumName]))
			{
				return $enumName;
			}
			return $statusMapping[$enumName];
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