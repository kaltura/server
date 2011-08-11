<?php

/**
 * Subclass for representing a row from the 'widget' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class widget extends Basewidget
{
	const WIDGET_SECURITY_TYPE_NONE = 1;
	const WIDGET_SECURITY_TYPE_TIMEHASH = 2;
	const WIDGET_SECURITY_TYPE_MATCH_IP = 3;
	const WIDGET_SECURITY_TYPE_FORCE_KS = 4;	

	const WIDGET_SECURITY_POLICY_NONE = 1;
	const WIDGET_SECURITY_POLICY_ROOT= 2; // security_type is always the same as the root widget's and can never be modified  
	
	
	public static function createDefaultWidgetForPartner ( $partner_id , $subp_id , $kdp_ui_conf_id = 200 )
	{
		if ( ! $kdp_ui_conf_id ) $kdp_ui_conf_id = 200;
		
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		$widget_id = $partner->getDefaultWidgetId(); 
		
		if ( $kdp_ui_conf_id != null && $kdp_ui_conf_id != 200 ) $widget_id .= "_{$kdp_ui_conf_id}";
		
		try
		{
			// create widget associated with the kdp_ui_conf
			$widget = new widget();
			$widget->setId ( $widget_id ); 
			$widget->setPartnerId( $partner_id);
			$widget->setSubpId( $subp_id );
			$widget->setUiConfId( $kdp_ui_conf_id );
			$widget->save();
			return $widget;
		}
		catch ( PropelException $pex )
		{
			// this migth very well indicate there is already such an id - return it;
			return widgetPeer::retrieveByPK( $widget_id )	;		
		}
		catch ( Exception $ex )
		{
			// unrecoverable exception 
			return null;
		}
	}
	
	
	/**
	 * If 
	 */
	public static function createWidgetFromWidget ( $parent_widget_obj_or_id , $kshow_id, $entry_id = null , $ui_conf_id = null , $custom_data = null , 
		$partner_data = null , $security_type = null )
	{
		if ( $parent_widget_obj_or_id == null )  throw new Exception ( "Cannot createWidget from empty object" );
		
		if ( $parent_widget_obj_or_id instanceof widget )
		{
			$source_widget = $parent_widget_obj_or_id;
		}
		else
		{
			// assume its an id
			$source_widget = widgetPeer::retrieveByPK( $parent_widget_obj_or_id ) ;
			if ( ! $source_widget ) 
				throw new Exception ( "Cannot createWidget from a none-existing widget [$parent_widget_obj_or_id]" );
		}	
		
		$kshow = null;
		$widget_kshow_id = $source_widget->getKshowId();
		if (!$widget_kshow_id)
		{
			// fetch the kshow/entry according to the kshow_id/entry_id rules
			list ( $kshow , $entry , $error , $error_obj ) = myKshowUtils::getKshowAndEntry ( $kshow_id , $entry_id );
			$widget_kshow_id = $kshow_id;
			if ( $source_widget->getEntryId() )
			{
				$entry_id = $source_widget->getEntryId() ;
			}
		}
		
		if (  !$kshow) 
		{
			$kshow = kshowPeer::retrieveByPK( $widget_kshow_id );
			if ( !$kshow ) 
				return null;
		}
		
		return self::createWidget( $kshow , $entry_id , $source_widget ,  $ui_conf_id , $custom_data , 
			$partner_data , $security_type );
		
	}
	
	public static function createWidget ( $kshow , $entry_id = null , $parent_widget_obj_or_id = null , $ui_conf_id = 1 , $custom_data = null , 
		$partner_data = null , $security_type = null  )
	{
		$widget = new widget();
		$widget->setKshowId( $kshow->getId() );
		$widget->setEntryId( $entry_id );
		$widget->setPartnerId( $kshow->getPartnerId() );
		$widget->setSubpId( $kshow->getSubpId() );

		$source_widget = null;
		if ( $parent_widget_obj_or_id != null )
		{
			if ( $parent_widget_obj_or_id instanceof widget )
			{
				$source_widget = $parent_widget_obj_or_id;
				$widget->setSourceWidgetId( $parent_widget_obj_or_id->getId());				
			}
			else
			{
				// assume its an id
				$source_widget = widgetPeer::retrieveByPK( $parent_widget_obj_or_id ) ;
				$widget->setSourceWidgetId( $parent_widget_obj_or_id );
			}	
		}
		
		$widget->setUiConfId( $ui_conf_id );
		if ( $source_widget ) 
		{
			$widget->setRootWidgetId( $source_widget->getRootWidgetId() );
			if ( $ui_conf_id == null )
			{
				// if none was explicitly set - use the parent_widget's ui_conf
				$widget->setUiConfId( $source_widget->getUiConfId() );
			}
		}

		$widget->setCustomData( $custom_data );
		$widget->setPartnerData ( $partner_data );
		$widget->setSecurityType( $security_type );
		
		$widget->setId( self::calculateId ( $widget ) );
		
		if ( $widget->getUiConfId() == null )
		{
			// set the default to be 1
			$widget->setUiConfId( 1 );
		}	

		if ( $widget->getRootWidgetId() == null)
		{
			// set the root to be self - 
			$widget->setRootWidgetId( $widget->getId() );
		}
				
		$widget->save();

		return $widget;
	}
	
	
	// don't stop until a unique hash is created for this widget
	public static function calculateId ( $widget )
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ( $i = 0 ; $i < 10 ; ++$i)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existing_widget = widgetPeer::retrieveByPk( $id );
			
			if ( ! $existing_widget ) return $id;
		}
		
		die();
	}
	
	
	public function getWidgetHtml ( $player_name = null , $ui_conf_id = null )
	{
		$partner = PartnerPeer::retrieveByPK( $this->getPartnerId() );
		$templatePartnerId = $partner ? $partner->getTemplatePartnerId() : 0;
		
	     // add the version as an additional parameter
		$domain = myPartnerUtils::getHost($this->getPartnerId());
//		$swf_url = "/index.php/widget/{$this->getKshowId()}/-1/2/{$this->getUiConfId()}/-1"; 
		$security_type = $this->getSecurityType();
		
		$ui_conf = $this->getuiConf();
/*		
		if ( $ui_conf_id )
		{
			$ui_conf = uiConfPeer::retrieveByPK($ui_conf_id );
		}
		else
		{
			$ui_conf = $this->getuiConf();
		}
	*/	
		if ( ! $ui_conf ) 
		{
			return "Cannot find ui_conf [" . $this->getUiConfId() . "]" ;
			//throw new APIException( APIErrors::INVALID_UI_CONF_ID_FOR_WIDGET , $this->getUiConfId() , $this->getId() ); 	
			//throw new APIException( APIErrors::INVALID_UI_CONF_ID , $this->getUiConfId() ); //
		}

		$swf_url = "/index.php/kwidget/wid/{$this->getId()}" ; 
		
		if ( $ui_conf )
			$swf_url .= "/uiconf_id/" . $this->getUiConfId();
		
		$swf_url .=  ( $security_type == self::WIDGET_SECURITY_TYPE_TIMEHASH ? "|timehash|" : "" );
			
	   	$height = $ui_conf->getHeight();
	   	$width = $ui_conf->getWidth();
	    
	   	if ( $height <= 0 ) $height = 340;
	   	if ( $width <= 0 ) $width = 400;
	   	
	   	$seo_visible = "";
	   	
	   	if ($templatePartnerId) // dont include seo links for white label partners
	   	{
	   		$player_name = 'player_' . (int)microtime(true);
	   		$seo_hidden = "";
	   	}
	   	else
	   	{
		   	$seo_hidden = '<a href="http://corp.kaltura.com">video platform</a>' .
		   		'<a href="http://corp.kaltura.com/video_platform/video_management">video management</a>' .
		   		'<a href="http://corp.kaltura.com/solutions/video_solution">video solutions</a>' .
		   		'<a href="http://corp.kaltura.com/video_platform/video_publishing">video player</a>' ;
	   	}
	   	
	   	if ( $player_name == null )
	   		$player_name = 'kaltura_player_' . (int)microtime(true);

	   	$widget = /*$extra_links .*/
			 '<object name="'.$player_name.'" id="' . $player_name . '" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
				'<param name="allowScriptAccess" value="always" />'.
				'<param name="allowNetworking" value="all" />'.
				'<param name="allowFullScreen" value="true" />'.
				'<param name="bgcolor" value="#000000" />'.
				'<param name="movie" value="'.$domain. $swf_url . '"/>'.
				'<param name="flashVars" value=""/>'.
				 $seo_hidden . 
				'</object>' ; 
	
		return $widget ;
	}
	
	
	// will allow to replace the attached uiconf with another depending on policy and type
	// TODO- decide when OK  
	public function overrideUiConfId ( $uiconf_id )
	{
		if ( $uiconf_id )	$this->ui_conf_id = $uiconf_id;
	}
}