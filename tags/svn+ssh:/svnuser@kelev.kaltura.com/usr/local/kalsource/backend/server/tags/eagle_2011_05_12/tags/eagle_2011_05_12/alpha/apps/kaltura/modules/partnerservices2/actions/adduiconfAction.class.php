<?php
/**
 * @package api
 * @subpackage ps2
 */
class adduiconfAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "addUiConf",
				"desc" => "Create a new uiConf" ,
				"in" => array (
					"mandatory" => array ( 
						"uiconf" => array ("type" => "uiConf", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"uiconf" => array ("type" => "uiConf", "desc" => "")
					),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_GENERIC_ENTRY ,
					APIErrors::INVALID_KSHOW_ID
				)
			); 
	}
	
	protected function getDetailed()
	{
		return $this->getP ( "detailed" , false );
	}
	
	protected function getObjectPrefix () {  return "uiconf"; }

	protected function setObjType ( $ui_conf ) {}
	
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->applyPartnerFilterForClass ( new uiConfPeer() , $partner_id );
		
		$detailed = $this->getDetailed() ; //$this->getP ( "detailed" , false );
		$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
		
		// get the new properties for the kuser from the request
		$ui_conf = new uiConf();
		$ui_conf->setPartnerId( $partner_id );  // set this once before filling the object and once after 
		
		// this is called for the first time to set the type and media type for fillObjectFromMap
		$this->setObjType ( $ui_conf );
		
		$obj_wrapper = objectWrapperBase::getWrapperClass( $ui_conf , 0 );
		
		$field_level = $this->isAdmin() ? 2 : 1;
		$updateable_fields = $obj_wrapper->getUpdateableFields( $field_level );
		
		// TODO - always use fillObjectFromMapOrderedByFields rather than fillObjectFromMap
		$fields_modified = baseObjectUtils::fillObjectFromMapOrderedByFields( 
			$this->getInputParams() , $ui_conf , $this->getObjectPrefix() . "_" , $updateable_fields );
		// check that mandatory fields were set
		// TODO
		if ( count ( $fields_modified ) > 0 )
		{
			$ui_conf->setPartnerId( $partner_id );

			// this is now called for the second time to force the obj_type 
			$this->setObjType ( $ui_conf );

			$ui_conf->save();
										
			$this->addMsg ( $this->getObjectPrefix() , objectWrapperBase::getWrapperClass( $ui_conf , $level ) );
			$this->addDebug ( "added_fields" , $fields_modified );
		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_UI_CONF, $this->getObjectPrefix() );
		}
	}
}
?>