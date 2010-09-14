<?php
require_once ( "kalturaSystemAction.class.php" );

class findPartnerAction extends kalturaSystemAction
{

	public function execute()
	{
		$this->forceSystemAuthentication();

		$hint = $this->getP ( "hint" );
		$search_partnerName = $this->getP ( "p_partnerName" , true ); 
		$search_description = $this->getP ( "p_description" , true );
		$search_url1 = $this->getP ( "p_url1" , true );
		$search_adminName = $this->getP ( "p_adminName" , true );
		$search_adminEmail = $this->getP ( "p_adminEmail" , true );
		
		
		
		$limit = $this->getP ( "limit" , 300 );
		$offset = $this->getP ( "offset" , 0 );
		
		$text_to_search = "%$hint%";
		$c = new Criteria();
		
		$crit = $c->getNewCriterion ( PartnerPeer::PARTNER_NAME , $text_to_search , Criteria::LIKE );

		if ( $search_description ) 	$crit->addOr ( $c->getNewCriterion ( PartnerPeer::DESCRIPTION , $text_to_search , Criteria::LIKE ) );
		if ( $search_url1 ) $crit->addOr ( $c->getNewCriterion ( PartnerPeer::URL1 , $text_to_search , Criteria::LIKE ));
		if ( $search_adminName ) $crit->addOr ( $c->getNewCriterion ( PartnerPeer::ADMIN_NAME, $text_to_search , Criteria::LIKE ));
		if ( $search_adminEmail ) $crit->addOr ( $c->getNewCriterion ( PartnerPeer::ADMIN_EMAIL , $text_to_search , Criteria::LIKE ));
		
		$c->addAnd ( $crit );
		
//		$this->count =  PartnerPeer::doCount( $c );
		
		$c->setLimit ( $limit );
		
		if ( $offset > 0 )
		{
			$c->setOffset( $offset );
		}
		
		if ( $hint )
			$this->partner_list = PartnerPeer::doSelect( $c );
		else
			$this->partner_list = array(); 
/*		
		$arr = array();
		foreach ( $this->partner_list as $partner )
		{
			$p = array (
				"id" => $partner->getId() ,
				"partnerName" => $partner->getPartnerName() ,
				"description" => $partner->getDescription() ,
				"url1" => $partner->getUrl1() ,
				"adminName" => $partner->getAdminName() ,
				"adminEmail" => $partner->getAdminEmail() ,
				 );  
			$arr[] = $p;
		}
*/
		$this->hint = $hint;
		
//		return $this->renderText(json_encode ( $arr ) );
	}
}
?>