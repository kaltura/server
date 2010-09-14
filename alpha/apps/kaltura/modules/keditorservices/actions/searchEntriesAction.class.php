<?php
require_once ( "kalturaAction.class.php");
require_once ( "mySmartPager.class.php");

//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');
//require_once(MODULES.'search/actions/AJAX_getEntriesAction.class.php');
//search/actions/entryFilter.class.phprequire_once ( "../apps/kaltura/modules/search/actions/entryFilter.class.php");


class searchEntriesAction extends kalturaAction
{
	public function execute ( )
	{
		// keywords & page
		// keywords
//		$keywords = $this->request->getRequestParameter ( "keywords" , "" );

		// page
//		$page = $this->request->getRequestParameter ( "page" , "1" );

		$partner_id = $this->getP ( "partner_id" , null );
		if ( false && $partner_id )
		{
			$criteria_filter = enrtyPeer::getCriteriaFilter();
			$criteria = $criteria_filter->getFilter();
			$criteria->addAnd ( entryPeer::PARTNER_ID , "(" . entryPeer::PARTNER_ID . "<100 OR " . entryPeer::PARTNER_ID . "=$partner_id )" , Criteria::CUSTOM );
			entryPeer::enable();
		}
		
		$page_size = 20;
		
		$entry_filter = new entryFilter ();	
		$entry_pager = new mySmartPager ( $this , "entry" , $page_size );

		$act = new AJAX_getEntriesAction();
		$act->setIdList( NULL );
		$act->setSortAlias( "ids" );
		$act->setPublicOnly( true );
		$act->skip_count = false;
		
		$this->entry_results = $act->fetchPage( $this , $entry_filter , $entry_pager );
		
		$this->getResponse()->setHttpHeader ( "Content-Type" , "text/xml; charset=utf-8" );
		$this->number_of_results = $entry_pager->getNumberOfResults();
		$this->number_of_pages = $entry_pager->getNumberOfPages();
		
		
	}
}
?>