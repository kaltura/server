<?php

class widgetAction extends sfAction
{
	/**
	 * Will forward to the regular browse page according to the band_id 
	 */
	public function execute()
	{
		// because of the routing rule - the entry_id & kmedia_type WILL exist. be sure to ignore them if smaller than 0
		$kshow_id= $this->getRequestParameter( "kshow_id" );
		if (strpos($kshow_id, ".") !== false)
			die;
		$entry_id= $this->getRequestParameter( "entry_id" , -1 );
		$kmedia_type= $this->getRequestParameter( "kmedia_type" , -1);
		$widget_type = $this->getRequestParameter( "widget_type" , 1);
		$show_version = $this->getRequestParameter( "version" , null);
		$kdata = $this->getRequestParameter( "kdata" , null );

		if ( $show_version < 0 )
			$show_version = null;
			
		$referer = @$_SERVER['HTTP_REFERER'];
		$ip = @$_SERVER['REMOTE_ADDR'] ;// to convert back, use long2ip
		
		WidgetLog::createWidgetLog( $referer , $ip , $kshow_id , $entry_id , $kmedia_type , $widget_type );
		
		if ( $entry_id < 0 ) $entry_id = null;
		
		$dynamic_date = "KShowID=$kshow_id" .
		( $show_version ? "&ShowVersion=$show_version" : "" ) .
		( $entry_id ? "&EntryID=$entry_id" : "" ) .
		( $entry_id ? "&KmediaType=$kmedia_type" : "");
		$dynamic_date .= "&isWidget=$widget_type&referer=".urlencode($referer);
		$dynamic_date .= "&kdata=$kdata";
		
		$host = requestUtils::getHost() ; //"http://www.kaltura.com" ; 
		if ( $widget_type == 10)
		{
			$this->redirect(  $host . "/swf/weplay.swf?" . $dynamic_date );
		}
		elseif( $widget_type == 20)
		{
			$this->redirect(  $host . "/swf/remixAPlayer.swf?" . $dynamic_date );
		}
		elseif( $widget_type == 21)
		{
			$this->redirect(  $host . "/swf/remixamerica.swf?" . $dynamic_date );
		}
		elseif( $widget_type == 30)
		{
			$this->redirect(  $host . "/swf/cocacola.swf?" . $dynamic_date );
		}
		elseif( $widget_type == 40)
		{
			$this->redirect(  $host . "/swf/360flexwidget.swf?" . $dynamic_date );
		}
		elseif( $widget_type == 60)
		{
			$this->redirect(  $host . "/swf/athletixnationwidget.swf?forceAutoPlay=1&" . $dynamic_date );
		}
		else
		{
			$this->redirect(  $host . "/swf/kplayer.swf?" . $dynamic_date );
		}
	}
}
?>
