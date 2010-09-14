<?php

require_once ( "kalturaSystemAction.class.php" );

class viewWidgetsAction extends kalturaSystemAction
{
	public function execute()
	{
		die;
		$this->forceSystemAuthentication();

		$order = $this->getRequestParameter('sort', 'id' );
		$page = $this->getRequestParameter('page', 1);
		$referer = $this->getRequestParameter('referer', "");
		
		$c = new Criteria();
		
		if ($referer)
		{
			$c->add(WidgetLogPeer::REFERER, "%$referer%", Criteria::LIKE);
		}
		
		$c->addAnd(WidgetLogPeer::REFERER, "%diff=%", Criteria::NOT_LIKE);
		$c->addAnd(WidgetLogPeer::REFERER, "%kaltura:%", Criteria::NOT_LIKE);
		
		$partner_id =  $this->getRequestParameter('partner_id', -1 );
		if ( $partner_id >= 0 )
		{
			$c->add(WidgetLogPeer::PARTNER_ID, $partner_id);
			//fixme: replace with myPartnerUtils::applyPartnerFilters( $partner_id );
		}

		$this->partner_id = $partner_id;
		
		$pager = WidgetLogPeer::getWidgetOrderedPager( $order, 100, $page, $c );

		$widget_log_list = $pager->getResults();
		
		
		// following variables will be used by the view
		$this->firstTime = $this->getRequestParameter('first', 1) == 1;
		$this->order = $order;
	  	$this->page = $page;
	  	$this->lastPage = $pager->getLastPage();
	  	$this->numResults = $pager->getNbResults();
	  	$this->widget_log_list = $widget_log_list;
	  	$this->referer = $referer;
	  	
	  	// allow the action buttons to show only for entires the user on their own pages
	  	$this->allowactions =  true; 
	  	
	}
}

?>
