<?php

class userPageAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$entry_id = $this->getRequestParameter( "entry_id" );
		
		$entry = entryPeer::retrieveByPK($entry_id);
		
		if ($entry)
		{
			$this->redirect( $entry->getUserLandingPage() );
		}
			
		die;
	}
}
?>
