<?php
class blockMailAction extends sfAction
{
	public function execute ()
	{
		// see how myBlockedEmailUtils create the URL
		$combined_email = $this->getRequestParameter( "e" ) ;
		$valid = myBlockedEmailUtils::blockEmail( $combined_email );
		
		$str = "You will no loger receive any mail from " . kConf::get('www_host') . ". Have a good day !";
		return $this->renderText ( $str );
		//return sfView::NONE;
		
	}
}
?>