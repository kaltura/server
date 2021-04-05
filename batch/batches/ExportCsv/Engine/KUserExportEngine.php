<?php
/**
 * @package Scheduler
 * @subpackage ExportCsv
 */
class KUserExportEngine extends KMappedObjectExportEngine
{
	protected function getFilterOrderBy()
	{
		return KalturaUserOrderBy::CREATED_AT_ASC;
	}

	protected function getItemList($filter, $pager)
	{
		return KBatchBase::$kClient->user->listAction($filter, $pager);
	}

	protected function getDefaultHeaderRowToCsv()
	{
		return 'User ID,First Name,Last Name,Email';
	}

	protected function getDefaultRowValues($item)
	{
		return array(
			'id' => $item->id,
			'firstName' => $item->firstName,
			'lastName' => $item->lastName,
			'email' =>$item->email
		);
	}

	protected function getMetadataObjectType()
	{
		return MetadataObjectType::USER;
	}
}