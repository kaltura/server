<?php

class getWidgetThumbnailAction extends sfAction
{
	/**
	 * Will forward to the the thumbnail of the kshows using the widget id
	 */
	public function execute()
	{
		$widget_id = $this->getRequestParameter( "wid" );
		$widget = widgetPeer::retrieveByPK( $widget_id );

		if ( !$widget )
		{
			die();	
		}
		
		// because of the routing rule - the entry_id & kmedia_type WILL exist. be sure to ignore them if smaller than 0
		$kshow_id= $widget->getKshowId();
		
		if ($kshow_id)
		{
			$kshow = kshowPeer::retrieveByPK($kshow_id);
			if ($kshow->getShowEntry())
				$this->redirect($kshow->getShowEntry()->getBigThumbnailUrl());
		}
	}
}
?>
