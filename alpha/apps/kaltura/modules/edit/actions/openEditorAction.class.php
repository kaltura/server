<?php
require_once ( "kalturaAction.class.php");
/**
 * edit actions.
 *
 * @package    kaltura
 * @subpackage edit
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class openEditorAction extends kalturaAction
{
	public function execute()
	{
		$kshow_id = $this->getRequestParameter('kshow_id' , 0 );
		
		$this->uid = $this->getLoggedInPuserId() ;
		if ( ! $this->uid ) 
		{	
			// anonymous
			$this->uid = kuser::ANONYMOUS_PUSER_ID;
			$kshow_id = kshow::SANDBOX_ID; // sandbox
		}
		
		$this->ks = myPartnerServicesClient::createKalturaSession( $this->uid );
		$back_url = $this->getP ( "back_url");
		$this->widget_host = requestUtils::getHost();
		$partner_id = 0;
		$subp_id = 100;
		$partner_name  = "Kaltura";
		
		    $editor_params = array( "partner_id" => $partner_id , 
		    						"subp_id" => $subp_id , 
		    						"uid" => $this->uid , 
		    						"ks" => $this->ks ,
		    						"kshow_id" => $kshow_id ,
		    						"logo_url" => "/swf/kalturaLogo.png" , 
		    						"btn_txt_back" => "Back" , 
		    						"btn_txt_publish" => "Publish" ,
									"back_url" => $back_url ,
									"partner_name" => $partner_name );
									
			$editor_params_str = http_build_query( $editor_params , '' , "&" )		;		
							
			$editor_url = "/index.php/edit?$editor_params_str";		

	  	$this->getController()->redirect($editor_url, 0, 302);
    	throw new sfStopException();
		
	}
}
?>