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
		
		$statusMapping = array(
			'BLOCKED' => 'Blocked',
			'ACTIVE' => 'Active',
			'DELETED' => 'Deleted',
		);
		
		if ($valueType == 'status')
		{
			$enumName = $this->getEnumName($value, 'KalturaUserStatus');
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
		return $value;
	}
}