<?php

require_once ( "kalturaSystemAction.class.php" );

class viewUsersAction extends kalturaSystemAction
{
	protected function createkuserData($kuser, $order)
	{
		
		$kuserData = array(
			"id" => $kuser->getId(),
		  	"screenname" => $kuser->getScreenName(),
		  	"fullname" => $kuser->getScreenName() . ( $kuser->getFullName() ? ' ('.$kuser->getFullName().')' : '' ),
		  	"age" => $kuser->getAge(),
		  	"gender" => ( $kuser->getGender() != 0 ?  ($kuser->getGender() == 1 ? 'Male' : 'Female' ) : 'Undisclosed') ,
		  	"city" => $kuser->getCity(),
		  	"country" => $kuser->getCountry(),
		  	"image" => $kuser->getPicturePath(),
			"tags" => $kuser->getTags(),
			"views" => $kuser->getViews(),
			"createdAt" => $kuser->getFormattedCreatedAt(),
			"fans" => $kuser->getFans(),
			"entries" => $kuser->getEntries() ,
			"shows" =>$kuser->getProducedKshows() ,
			"roughcuts" => $kuser->getRoughcutCount() ,
			);
			
		return $kuserData;
	}
	
	public function execute()
	{
		$this->forceSystemAuthentication();
	
		$partner_id =  $this->getRequestParameter('partner_id', -1 );
		if ( $partner_id >= 0 )
		{
			myPartnerUtils::applyPartnerFilters( $partner_id );
		}

		$this->partner_id = $partner_id;
		
		$order = $this->getRequestParameter('sort', kuser::KUSER_SORT_MOST_VIEWED);
		$page = $this->getRequestParameter('page', 1);
		
		$pager = kuserPeer::getAllUsersOrderedPager( $order, 10, $page );

		$kuser_list = $pager->getResults();
		
		dashboardUtils::updateKusersRoughcutCount ( $kuser_list );
		
		$kusersData = array();
		foreach ($kuser_list  as $kuser)
			$kusersData[] = $this->createkuserData($kuser, $order);
		
		// following variables will be used by the view
		$this->firstTime = $this->getRequestParameter('first', 1) == 1;
		$this->order = $order;
	  	$this->page = $page;
	  	$this->lastPage = $pager->getLastPage();
	  	$this->numResults = $pager->getNbResults();
	  	$this->kusersData = $kusersData;
	  	
	  	// allow the action buttons to show only for entires the user on their own pages
	  	$this->allowactions =  true; 
	  	
	}
}

?>