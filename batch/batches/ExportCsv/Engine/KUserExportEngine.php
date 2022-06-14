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
	
	protected function getTitleHeader()
	{
		return "#-----------------------------------------------\n" .
			"Report: Users\n" .
			"Please note that the data below is filtered based on the filter applied in the report\n" .
			"#-----------------------------------------------";
	}
	
	protected function formatValue($value, $valueType)
	{
		$dateFormatTypes = array('createdAt', 'lastLoginTime');
		
		if ($valueType == 'status')
		{
			return $this->getEnumName($value, 'KalturaUserStatus');
		}
		else if (in_array($valueType, $dateFormatTypes))
		{
			return date('Y-m-d H:i:s', $value);
		}
		return $value;
	}
}