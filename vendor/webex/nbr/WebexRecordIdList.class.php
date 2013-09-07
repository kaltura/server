<?php
require_once(__DIR__ . '/WebexRecordId.class.php');

/**
 * @package External
 * @subpackage WSDL
 */
class WebexRecordIdList extends SoapObject
{
	/**
	 * @var WebexRecordId
	 */
	public $RecordIdList;
	
	protected function getAttributeType($attributeName)
	{
		if($attributeName == 'RecordIdList')
			return 'WebexRecordId';
			
		return null;
	}
}
