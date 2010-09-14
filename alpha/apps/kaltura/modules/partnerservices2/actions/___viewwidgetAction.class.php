<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "addkshowAction.class.php");



/**
 * 1. Will create a kshow with name and summary for a specific partner.
 * 2. Will generate widget-html for this kshow.
 */
class viewwidgetAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "viewWidget",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						),
					"optional" => array (
						"entry_id" => array ("type" => "string", "desc" => ""),
						"kshow_id" => array ("type" => "string", "desc" => ""),
						"widget_id" => array ("type" => "string", "desc" => ""),
						"host" => array ("type" => "integer", "desc" => ""),																							
						)
					),
				"out" => array (
					"html" => array ("type" => "string", "desc" => "Html code for widget")
					),
				"errors" => array (
				)
			); 
	}

	// check to see if already exists in the system = ask to fetch the puser & the kuser
	// don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_ID_ONLY;
	}

	protected function addUserOnDemand ( )
	{
		return self::CREATE_USER_FROM_PARTNER_SETTINGS;
	}

	// TODO - add privileges
	protected function ticketType ()
	{
		return  self::REQUIED_TICKET_REGULAR;
		// validate for all partners that are not kaltura (partner_id=0)
		$partner_id = $this->getP ( "partner_id");
		return ( $partner_id != 0 ? self::REQUIED_TICKET_ADMIN : self::REQUIED_TICKET_NONE );
	}
	/*
	 public function execute( $add_extra_debug_data = true )
	 {
		// will inject data so the base class will act as it the partner_id is 0
		$this->injectIfEmpty ( array (
		"partner_id" => "0" ,
		"subp_id" => "0" ,
		"uid" => "_00" ));

		return parent::execute();
		}
		*/
	// TODO - should use the widget mechanism
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->response_type = kalturaWebserviceRenderer::RESPONSE_TYPE_HTML ; //

		$widget_id = $this->getP ( "widget_id");
		
		$kshow_id = $this->getP ( "kshow_id");
		$entry_id = $this->getP ( "entry_id");
		$widget_type = $this->getP ( "widget_type" , 1); // TODO -decide on a good default;
		
		if  ( $widget_id  )
		{
			$this->addMsg( "<b>" , $this->createGenericWidgetHtml2 ( $partner_id , $subp_id  , $puser_id  ) );
			return;
		}

		if (in_array($partner_id, array(321,449)))  $widget_type = 60;
		
		list ( $kshow , $entry , $error , $error_obj ) = myKshowUtils::getKshowAndEntry( $kshow_id  , $entry_id );
		if ( $error_obj )
		{
			$this->addError ( $error_obj );
			return ;
		}
				
		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		if ( !$kshow )
		{
			$this->addMsg( "<b>" , "Error: no such $kshow_id" );
			return ;
		}

		$this->addMsg( "<b>" , $this->createGenericWidgetHtml ( $partner_id , $subp_id  , $kshow_id , $puser_id , $widget_type ) );

	}


	private function createGenericWidgetHtml ( $partner_id , $subp_id , $kshow_id , $user_id , $widget_type )
	{
		$WIDGET_HOST = requestUtils::getHost();

		// add the version as an additional parameter
		$domain = $WIDGET_HOST;
		$swf_url = "/index.php/widget/$kshow_id/-1/2/$widget_type/-1";

		$height = 300 + 105 + 20;
		$width = 400;

		$widget = /*$extra_links .*/
		'<object id="kaltura_player_' . (int)microtime(true) . '" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
		'<param name="allowScriptAccess" value="always" />'.
		'<param name="allowNetworking" value="all" />'.
		'<param name="allowFullScreen" value="true" />'.
		'<param name="bgcolor" value=#000000 />'.
		'<param name="movie" value="'.$domain. $swf_url . '"/>'.
		'<param name="flashVars" value="CW=gotoCW&Editor=gotoEditor"/>'.
		'<param name="wmode" value="opaque"/>'.
		"<a href='http://www.kaltura.com' style='color:#bcff63; text-decoration:none; '>Kaltura</a>" .
		'</object>' ;

		return $widget ;
	}
	
	private function createGenericWidgetHtml2 ( $partner_id , $subp_id , $puser_id  )
	{
		$widget_id = $this->getP ( "widget_id");
		$kshow_id = $this->getP ( "kshow_id");
		$entry_id = $this->getP ( "entry_id");
		$widget_type = $this->getP ( "widget_type" , 1); // TODO -decide on a good default;
		$host = $this->getP ( "host" , null ); 

		$WIDGET_HOST = requestUtils::getHost();

		// add the version as an additional parameter
		$domain = ( $host ? "http://" . $host : $WIDGET_HOST );
		$swf_url = "/index.php/extwidget/kwidget/wid/$widget_id/kid/$kshow_id";

		$height = 0;
		$width = 0;
		
		$widget = widgetPeer::retrieveByPK( $widget_id );
		if ( $widget )
		{
			$ui_conf = $widget->getUiConf();
			if ( $ui_conf  )
			{
				$height = $ui_conf->getHeight();
				$width = $ui_conf->getWidth();
			}
		}
		
		if ( !$height ) 	$height = 300 + 105 + 20;
		if ( !$width ) 	$width = 400;
		

		$params = array ( );
		if ( $kshow_id ) $params [] = "kshowId=$kshow_id";
		if ( $entry_id ) $params [] = "entryId=$entry_id";
		$flash_vars  = implode( "&" , $params);
		
		$widget = /*$extra_links .*/
		'<object id="kaltura_player_' . (int)microtime(true) . '" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
		'<param name="allowScriptAccess" value="always" />'.
		'<param name="allowNetworking" value="all" />'.
		'<param name="allowFullScreen" value="true" />'.
		'<param name="bgcolor" value=#000000 />'.
		'<param name="movie" value="'.$domain. $swf_url . '"/>'.
		'<param name="flashVars" value="' . $flash_vars . '"/>'.
		'<param name="wmode" value="opaque"/>'.
		"<a href='http://www.kaltura.com' style='color:#bcff63; text-decoration:none; '>Kaltura</a>" .
		'</object>' ;

		$html = "<div>$widget</div><br><div><code>" . htmlentities( $widget ) . "</code>";
		
		return $html ;
	}	
}
?>