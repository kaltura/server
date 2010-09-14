<?php

require_once ( "kalturaAction.class.php" );

class reportsAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->ks = $this->getP ( "ks" );
		$this->partner_id = $this->getP ( "partner_id" );
		
		if ( ! $this->ks )
		{
			$ks = null;
			$this->partner_id = 0;
			kSessionUtils::createKSessionNoValidations( $this->partner_id ,
				 0 , $ks ,8640000 , true , "" , "" );	
			$this->ks = $ks;
			
		}
		
		
		$this->subp_id = $this->getP ( "subp_id" );
		$this->uid = $this->getP ( "uid" );

		$this->screen_name = $this->getP ( "screen_name" );
		$this->email = $this->getP ( "email" );
		
		$this->beta = $this->getRequestParameter( "beta" );
		
		sfView::SUCCESS;
	}
}
?>