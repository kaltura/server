<?php

require_once ( "kalturaAction.class.php" );

class kmcAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->beta = $this->getRequestParameter( "beta" );
		sfView::SUCCESS;
	}
}
?>