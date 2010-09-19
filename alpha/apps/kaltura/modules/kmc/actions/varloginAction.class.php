<?php
class varloginAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->beta = $this->getRequestParameter( "beta" );
		$this->kmc_login_version 	= kConf::get('kmc_login_version');
				
		sfView::SUCCESS;
	}
}
?>