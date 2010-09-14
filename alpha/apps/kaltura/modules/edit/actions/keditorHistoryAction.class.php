<?php
include_once( 'myResponseUtils.class.php');
class keditorHistoryAction extends sfAction
{
	public function execute()
	{
		myResponseUtils::neverExpire ( $this->context->getResponse() );
		$this->getResponse()->setHttpHeader ( "Content-Type" , "text/html; charset=utf-8" );
		$this->getController()->setRenderMode ( sfView::RENDER_CLIENT );

	}
}
?>