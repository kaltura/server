<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class addwidgetAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "addWidget",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"widget" => array ("type" => "widget", "desc" => ""),
					),
					"optional" => array (
					)
				),
				"out" => array (
					"widget" => array ("type" => "widget", "desc" => "")
				),
				"errors" => array (
					APIErrors::NO_FIELDS_SET_FOR_WIDGET  ,
				)
			);
	}

	//protected function ticketType()			{	return self::REQUIED_TICKET_ADMIN;	} // TODO - and admin ticket

	public function needKuserFromPuser ( )	{	return self::KUSER_DATA_NO_KUSER;	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// get the new properties for the kuser from the request
		$widget = new widget();

		$obj_wrapper = objectWrapperBase::getWrapperClass( $widget , 0 );

		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $widget , "widget_" , $obj_wrapper->getUpdateableFields() );
		// check that mandatory fields were set
		// TODO
		$new_widget = null;
		if ( count ( $fields_modified ) > 0 )
		{
			// see if to create a widget from a widget or from a kshow
			if ( $widget->getSourceWidgetId() )
			{
				$widget_from_db = widgetPeer::retrieveByPK( $widget->getSourceWidgetId() );
					
				$new_widget = widget::createWidgetFromWidget( $widget_from_db , $widget->getKshowId(), $widget->getEntryId(), $widget->getUiConfId() ,
					$widget->getCustomData() , $widget->getPartnerData() , $widget->getSecurityType() );
				if ( !$new_widget )
				{
					 $this->addError( APIErrors::INVALID_KSHOW_AND_ENTRY_PAIR , $widget->getKshowId(), $widget->getEntryId() );
					 return;
				}
			}
			else
			{
				$kshow_id = $widget->getKshowId();

				if ( $kshow_id )
				{
					$kshow = kshowPeer::retrieveByPK( $kshow_id );
					if ( ! $kshow )
					{
						$this->addError( APIErrors::KSHOW_DOES_NOT_EXISTS ) ;// This field in unique. Please change ");
						return;
					}
				}
				else
				{
					$kshow = new kshow();
					$kshow->setId(0);
					$kshow->setPartnerId($partner_id);
					$kshow->setSubpId($subp_id);
				}
				$new_widget = widget::createWidget( $kshow , $widget->getEntryId() , null  , $widget->getUiConfId() ,
					$widget->getCustomData() , $widget->getPartnerData() , $widget->getSecurityType() );
			}

			$this->addMsg ( "widget" , objectWrapperBase::getWrapperClass( $new_widget , objectWrapperBase::DETAIL_LEVEL_DETAILED) );
			$this->addDebug ( "added_fields" , $fields_modified );
		}
		else
		{
			$this->addError( APIErrors::NO_FIELDS_SET_FOR_WIDGET ) ;
		}
	}
}
?>