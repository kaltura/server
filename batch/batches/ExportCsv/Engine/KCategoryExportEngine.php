<?php
/**
 * @package Scheduler
 * @subpackage ExportCsv
 */

class KCategoryExportEngine extends KMappedObjectExportEngine
{
	protected function getFilterOrderBy()
	{
		return KalturaCategoryOrderBy::CREATED_AT_ASC;
	}

	protected function getItemList($filter, $pager)
	{
		if(!$filter->statusIn && !$filter->statusEqual)
		{
			$filter->statusIn = KalturaCategoryStatus::UPDATING . "," . KalturaCategoryStatus::ACTIVE;
		}
		return KBatchBase::$kClient->category->listAction($filter, $pager);
	}

	protected function getDefaultHeaderRowToCsv()
	{
		return 'Category Id,Full Name';
	}

	protected function getDefaultRowValues($item)
	{
		return array(
			'id' => $item->id,
			'fullName' => $item->fullName,
		);
	}

	protected function getMetadataObjectType()
	{
		return MetadataObjectType::CATEGORY;
	}
	
	protected function getTitleHeader()
	{
		return "#-----------------------------------------------\n" .
			"Report: Categories\n" .
			"Please note that the data below is filtered based on the filter applied in the report\n" .
			"#-----------------------------------------------";
	}
	
	protected function formatValue($value, $valueType)
	{
		$dateFormatTypes = array('createdAt', 'updatedAt');
		
		if (in_array($valueType, $dateFormatTypes))
		{
			return date('Y-m-d H:i:s', $value);
		}
		return $value;
	}
}