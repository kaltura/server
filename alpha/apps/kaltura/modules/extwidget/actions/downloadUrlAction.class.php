<?php

class downloadUrlAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		// add expire headers in order to prevent the caching of the page.
		// otherwise when request the download of an existing entry with a new conversion format the old redirect might return
		header("Cache-Control:");
		header("Expires: Sun, 19 Nov 2000 08:52:00 GMT");
		$this->url = $this->getRequestParameter( "url" );
	}
}
?>
