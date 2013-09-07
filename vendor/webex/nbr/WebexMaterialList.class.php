<?php
require_once(__DIR__ . '/WebexRecordId.class.php');

/**
 * @package External
 * @subpackage WSDL
 */
class WebexMaterialList extends SoapObject
{
	/**
	 * @var array
	 */
	public $NBRMaterialList;
	
	protected function getAttributeType($attributeName)
	{
		return null;
	}
}
