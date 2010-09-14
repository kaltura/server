<?php
class varloginAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->beta = $this->getRequestParameter( "beta" );
		
		sfView::SUCCESS;
	}
}
?>