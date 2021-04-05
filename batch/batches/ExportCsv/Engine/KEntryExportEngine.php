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
}