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
	
	protected function formatValue($value, $valueType)
	{
		if ($valueType == 'status')
		{
			return $this->getEnumName($value, 'KalturaUserStatus');
		}
		else if ($valueType == 'createdAt' || $valueType == 'lastLoginTime')
		{
			return date('Y-m-d H:i:s', $value);
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