<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class viewShowsAction extends kalturaSystemAction
{
	protected function createKShowData($kshow, $order)
	{
		$k = new kshow();
		
		$kshowData = array(
			"id" => $kshow->getId(),
		  	"name" => $kshow->getName(),
		  	"description" => $kshow->getDescription(),
		  	"image" => $kshow->getThumbnailPath(),
			"rank" => $kshow->getNormalizedRank(),
			"views" => $kshow->getViews(),
			"entries" => $kshow->getEntries(),
			"createdAt" => $kshow->getFormattedCreatedAt(),
			"updatedAt" => $kshow->getFormattedUpdatedAt(),
			"comments" => $kshow->getComments(),
			"contributors" => $kshow->getContributors() ,
			"roughcuts" => $kshow->getRoughcutCount() ,
			);
			
		return $kshowData;
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
				
		$order = $this->getRequestParameter('sort', kshow::KSHOW_SORT_MOST_VIEWED);
		$page = $this->getRequestParameter('page', 1);
		//$this->producer_id = $this->getRequestParameter('producer_id', 0 );
		//$this->kaltura_part_of_flag = $this->getRequestParameter('partof', 0 );
		
		$pager = kshowPeer::getOrderedPager( $order, 10, $page );
	    
		$kshow_list = $pager->getResults();
		
		dashboardUtils::updateKshowsRoughcutCount ( $kshow_list );
		
		$kshowsData = array();
		foreach ( $kshow_list as $kshow)
			$kshowsData[] = $this->createKShowData($kshow, $order);
		
		// following variables will be used by the view
		$this->firstTime = $this->getRequestParameter('first', 1) == 1;
		$this->order = $order;
	  	$this->page = $page;
	  	$this->lastPage = $pager->getLastPage();
	  	$this->numResults = $pager->getNbResults();
	  	$this->kshowsData = $kshowsData;
	  	
		
			  	
	  	// allow the action buttons to show only for shows the user produced, and only for authenticated users, on their own pages
	  	$this->allowactions = true;// !$this->kaltura_part_of_flag && $this->getUser()->isAuthenticated() && $this->getUser()->getAttribute('id') == $this->producer_id; 
	  	
	}
}

?>