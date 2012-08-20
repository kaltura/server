<?php
/**
 * @package api
 * @subpackage ps2
 */
require_once 'addentrybaseAction.class.php';

/**
 * @package api
 * @subpackage ps2
 */
class adddataentryAction extends addentrybaseAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addDataEntry",
				"desc" => "Create a new data entry - data is embeded in the request, not uploaded vie file upload or URL" ,
				"in" => array (
					"mandatory" => array ( 
						"entry" => array ("type" => "entry", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"entry" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY ,
					APIErrors::INVALID_KSHOW_ID
				)
			); 
	}
	
	protected function getDetailed()
	{
		return $this->getP ( "detailed" , true );
	}
	
	protected function getObjectPrefix () {  return "entry"; }

	protected function setTypeAndMediaType ( $entry ) 
	{
		$entry->setType ( entryType::DATA );
		// media_type can be either ENTRY_MEDIA_TYPE_XML or ??
	}
	
	protected function validateEntry ( $entry ) 
	{
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// validate the xml of the dynamic playlist and update the update the entry_group table for a static playlist   
		$res = parent::executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser );
	}
}
?>