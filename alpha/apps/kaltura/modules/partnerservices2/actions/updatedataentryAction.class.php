<?php
/**
 * @package api
 * @subpackage ps2
 */
class updatedataentryAction extends updateentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "updateDataEntry",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"entry_id" => array ("type" => "string", "desc" => ""),
						"entry" => array ("type" => "entry", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"playlist" => array ("type" => "entry", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	public function requiredPrivileges () { return "" ; } //"edit:<kshow_id>" ; }
	
	public function verifyEntryPrivileges ( $entry ) 
	{
		return $this->verifyPrivileges ( "edit" , $entry->getId() ); // user was granted explicit permissions when initiatd the ks
	}
	
	
	protected function getObjectPrefix () { return "entry"; } 

	protected function validateInputEntry ( $entry ) 
	{
		if ( $entry->getType() != entryType::DATA )
		{
			throw new APIException ( APIErrors::INVALID_ENTRY_TYPE , "ENTRY_TYPE_DATA" );
		}
	}
	
	protected function validateEntry ( $entry ) 
	{
		 
	}
	
}
?>