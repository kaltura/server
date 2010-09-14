<?php
class getRelatedKshowsAction extends kalturaAction
{
	public function execute ( )
	{ 		
		$kshow_id = $this->getRequestParameter( 'kshow_id' , '');
		$this->kshowdataarray = myKshowUtils::getRelatedShowsData( $kshow_id, null, 12 );
		$this->getResponse()->setHttpHeader ( "Content-Type" , "text/xml; charset=utf-8" );
		$this->getController()->setRenderMode ( sfView::RENDER_CLIENT );
	}
}

