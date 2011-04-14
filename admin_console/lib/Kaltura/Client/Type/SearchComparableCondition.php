<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_SearchComparableCondition extends Kaltura_Client_Type_SearchCondition
{
	public function getKalturaObjectType()
	{
		return 'KalturaSearchComparableCondition';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_SearchConditionComparison
	 */
	public $comparison = null;


}

