<?php
require_once( 'mySearchUtils.class.php');
require_once( 'mySmartPager.class.php');
require_once( 'baseObjectUtils.class.php');

// TODO - don't extend sfAction & clean up the old code !!
abstract class AJAX_getObjectsAction //extends sfAction
{
	const SEARCH_MODE_FULLTEXT = true;
	  
	const PAGE_SIZE = 5;
	const EXPIRY_IN_SECONDS = 200;
	
	protected $category;
	protected $or_category;
	
	protected $id_list ;
	protected $sort_alias;
	
	public  $skip_count = true;
	static protected $s_top_list_cache;
	
	abstract protected function getPagerName() ;
	abstract protected function getFiler ();
	abstract protected function getComlumnNames ();
	abstract protected function getSearchableColumnName ();
	abstract protected function getFilterPrefix ();
	abstract protected function getPeerMethod ();
	abstract protected function getPeerCountMethod ();
	// each derived action can interpret differently the sort_keyword and can create a set of orders accordingly
	abstract protected function getSortArray ( );
	abstract protected function getDefaultSort( );
//	abstract protected function getTopImpl ();

	
	protected function modifyCriteria ( Criteria $criteria )
	{
		// do nothing 
	}
	
	// returns a criterion to OR with the tagword-comlex-criterion
	protected function getOrCriterion ( Criteria $criteria )
	{
		return null;
		// do nothing 
	}
	
	
	public function execute ()
	{
		// for search - use the alternative connection
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		if ( ! myResponseUtils::hasPageExpired ( $this->getContext()->getResponse() ) )
			return sfView::NONE;
		
		$filter = $this->getFiler() ;// new kuserFilter ();
		$mode = @$_REQUEST["mode"];

		$page_size = ( $mode == "ALL" ? self::PAGE_SIZE :  2 * self::PAGE_SIZE );
		
		$my_pager = new mySmartPager( $this , $this->getPagerName() , $page_size );
		
		$featured = "";
		$id_list = mySearchUtils::getIdList( $mode , $featured );
		$this->setIdList( $id_list );
		
		$this->fetchPage( $this , $filter , $my_pager );

		// cache the result
		// TODO - make this work !!
		//$this->getResponse()->addCacheControlHttpHeader('max_age=60'); 
		myResponseUtils::setCacheHeaders ( $this->context->getResponse() ,  time() , self::EXPIRY_IN_SECONDS );
	}

	public function setIdList ( $id_list )
	{
//		echo ( "setIdList: " . print_r ( $id_list , true ) );
		$this->id_list = $id_list;
	}
	
	public function fetchPage ( /*sfAction */ $action , $filter , $my_pager , $base_criteria = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		$keywords = @$_REQUEST["keywords"];
		
		// $sort_alias is what is sent from the browser
		$sort_alias = $this->sort_alias != null ? $this->sort_alias : @$_REQUEST["sort"];
//		$keywords_array = mySearchUtils::getKeywordsFromStr ( $keywords );
		
		if ( $base_criteria != null )
			$c = $base_criteria;
		else
			$c = new EntrySphinxCriteria();
		
		$filter->addSearchMatchToCriteria( $c , $keywords , $this->getSearchableColumnName() );
		// each entity can do specific modifications to the criteria
		$this->modifyCriteria ( $c );

		if ( $this->skip_count )
		{
			$my_pager->attachCriteria( $c , $this->getPeerMethod() , $this->getPeerCountMethod() /*"doStubCount" */);
			//$res = $my_pager->fetchPage(null , true , 0);
			$res = $my_pager->fetchPage(null);// , true , 0);
		}
		else
		{
			$my_pager->attachCriteria( $c , $this->getPeerMethod() , $this->getPeerCountMethod() );				
			$res = $my_pager->fetchPage();
		}

		return $res;
	}
	
	public function setSortAlias ( $alias )
	{
		$this->sort_alias = $alias;
	}
	/*
	 *  this will store the top X results according to a static criteria in some cache.
	 * the size of the result list will be twice the size of the displayed list - 
	 * the first part of the displayed list will be the top X/2 results 
	 * the second will be some random for choosing the next X/2 from the rest
	 */
	public function getTop ( )
	{
//		return $this->getTopImpl();
	}
	
	protected function getSortArrayFromSortAlias ( $sort_alias )
	{
		$sort_array = $this->getSortArray();
		$column_list = @$sort_array[$sort_alias];
		if ( $column_list == NULL )
		{
			$column_list = $this->getDefaultSort();
		}
		return $column_list;
	}
	
	/*
	 * columns_list can be either a string or an array of strings
	 */
	protected function applySortByArray ( $criteria , $filter , $column_list )
	{
		if ( $column_list == NULL ) return;
		
		if ( is_string ( $column_list) )
		{
			return $filter->addOrderToCriteria ( $criteria , $column_list );
		}
		
		// can also be an array of strings
		foreach ( $column_list as $column )
		{
			$filter->addOrderToCriteria ( $criteria , $column );
		}
				
	}
}
?>