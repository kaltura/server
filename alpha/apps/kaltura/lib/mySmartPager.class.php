<?php

require_once ( "mySmartPagerRenderer.class.php" );
/**
 * what makes this so smart ?
 * this pager basicaly has 4 parts:
 * 1. the UI
 * 2. the javascript (currently under utils.js)
 * 3. the wrapper of the sfPropelPager 
 * 4. cache that fetchs a big bulk of primary keys of tables according to a select (that isn't necessarily based on indexes).
 * 	these PKs are stored in a fast cache and for each page (which is a fragment of the bulk that was initially fetched) the access uses
 *  the in [PK1 , PK2, PK3.. ] to optimize the next page onwards.
 * TODO - the smart part - caching results by PK
 */

class mySmartPager 
{
	const NONE = "_NONE_";
	
	// TODO - each will have a different default
	const SOME_DEFAULT = 0;
	const NUMBER_OF_PAGES_IN_PAGER = 3;
	
	const COUNT_CACHE_EXPIRY_IN_SECONDS = 120; // cache the count of the criteria for 2 minutes 
	
	private $m_criteria_class = NULL;
	
	private $m_prefix = NULL;
	private $m_action = NULL; 
	private $m_order;
	private $m_page;
	private $m_page_size;
	private $m_number_of_results = 0;
	private $m_data = array ();
	
	private $m_pager = NULL;
	
	private $m_cache_key = null;
	
	private static $s_pager_count_cache;
	private $m_count_came_from_cache = false;
	
	public function mySmartPager ( /*sfAction*/ $action , $criteria_class , $default_page_size , $prefix = NULL )
	{
		self::initCache();
		
		$this->m_action = $action;
		$this->m_criteria_class = $criteria_class;
		
		// auto-attach to action so will be available for view layer. the pagerr variable will be called $my_pager" in the UI
		// hack - should leave or remove ?? 
		$action->my_pager = $this;

		$this->m_prefix = ( $prefix != NULL ? $prefix : $criteria_class  );

		$this->m_order = $this->m_action->getRequestParameter('sort' , self::SOME_DEFAULT );
		$this->m_page = $this->m_action->getRequestParameter('page', 1);
		$this->m_page_size = $default_page_size ; //$this->m_action->getRequestParameter('page_size', $default_page_size );

		if ( $this->m_page_size < 2 ) $this->m_page_size = 2;
		if ( $this->m_page < 1 ) $this->m_page = 1;
		//if ( $this->m_page > $this->m_page_size ) $this->m_page = $this->m_page_size;

		// the sfPropelPager get a first parameter - the clas it pages
		$this->m_pager = new kPropelPager( $criteria_class , $this->m_page_size );
		//var_dump ( $this );
	}
	
	public function attachCriteria ( criteria $c , $peer_method = NULL , $peer_count_method = NULL )
	{
	    
	    $this->m_pager->setCriteria($c);
	    $this->m_pager->setPage( $this->m_page);
	    // TODO - should join with something specific ?
	    
	    if ( $peer_method != NULL )
	    {
	    	$this->m_pager->setPeerMethod($peer_method);
	    }

	    if ( $peer_count_method != NULL )
	    {
	    	$this->m_pager->setPeerCountMethod( $peer_count_method );
	    }
	    
	    $this->m_cache_key =null;
	}
	
	/**
	 * it returns the fetched entries wether manipulated by the callback function of untouched.
	 */
	public function fetchPage ( $callbackFunction = NULL ,$should_cache_count = true , $fixed_count =-1)
	{
		// this moved from the attachCriteria
		// TODO - the count time is very great here and can be cached !
		if ( $should_cache_count )
		{
			if ( $fixed_count >= 0 )
			{
				$cached_count = 	$fixed_count;
			}
			else
			{
				$cached_count = $this->getCountFromCache();
			
				if ( $cached_count < 0 ) 
					$cached_count = NULL;
			}
				
/*
 * 
 changes to sfPropelPager
added the count to init
public function init(  $cached_count = NULL ) 

just under the require_once:
    require_once($classPath);
    if ( $cached_count != NULL )
    {
        $count = $cached_count;
    }
    else
    {
        $count = call_user_func(array($this->getClassPeer(), $this->getPeerCountMethod()), $cForCount);
    }


*/
				
			$this->m_pager->init( $cached_count );  // a new parameter I added to sfPropelPager
		}
		else 
		{
			$this->m_pager->init(  );
		}
		
		 // TODO - need this ?
		 $cursor = $this->m_pager->getFirstIndice();
		
		foreach ($this->m_pager->getResults() as $entry)
		{
			++$cursor;
			if ( $callbackFunction != NULL )
			{
				$this->m_data[] = $callbackFunction ( $entry , $cursor , $this->m_order );
			}
			else
			{
				$this->m_data[] = $entry;
			}
		}
		
		$this->m_number_of_results = $this->m_pager->getNbResults();

		if ( $should_cache_count )
		{
			if ( $this->m_number_of_results == 0 )
			{
				$this->setCountInCache ( self::NONE  );
			}
			else
			{
				$this->setCountInCache ($this->m_number_of_results  );
			}
		}
		return $this->m_data;
	}

	private static function initCache()
	{
		if ( self::$s_pager_count_cache == NULL )
		{
			self::$s_pager_count_cache = new myCache( "smartPager" , self::COUNT_CACHE_EXPIRY_IN_SECONDS );
		}
	}
	
	private function getCountFromCache()
	{
		// the key to the cache is the creteria (as string) - it's long but safe.
		// TODO - if string too long - can remove some irrelevent stuff - as done in sfPropekPager
/*
    $cForCount = clone $this->getCriteria();
    $cForCount->setOffset(0);
    $cForCount->setLimit(0);
    $cForCount->clearGroupByColumns();
*/
		$cache_key =  $this->getCacheKey();
		$cached_count = self::$s_pager_count_cache->get ( $cache_key );
		if ( $cached_count != NULL )
		{
			$this->m_count_came_from_cache = true;
		}
		else
		{
			$this->m_count_came_from_cache = false;
		}
		
		KalturaLog::log( "mySmartPager get [" . $this->m_criteria_class . "] [$cache_key] [" . $this->m_count_came_from_cache . "] [$cached_count]" );
		
		if ( $cached_count == self::NONE )
			$cached_count = 0;  // so we don't mix up with null
		return $cached_count;
	}

	private function setCountInCache ( $count_to_cache )
	{
		// if we constantley set the value from the cache - it will never relly be updated from the DB.
		// we should not extend the ttl of the value if we originally used the cached value.  
		if ($this->m_count_came_from_cache )
		{
			return ;
		}
		
		$cache_key =  $this->getCacheKey();
		
		KalturaLog::log( "mySmartPager set [" . $this->m_criteria_class . "] [$cache_key] [" . $this->m_count_came_from_cache . "] [$count_to_cache]" );
		self::$s_pager_count_cache->put ( $cache_key , $count_to_cache , self::COUNT_CACHE_EXPIRY_IN_SECONDS  );
	}
	
	private function getCacheKey ()
	{
		if ( $this->m_cache_key == null )
		{
	//		return "cache_key_" . $this->m_criteria_class;
			$c =  $this->m_pager->getCriteria();
	/*
	 	$cForCount = clone $c;
	    $cForCount->setOffset(0);
	    $cForCount->setLimit(0);
	    $cForCount->clearGroupByColumns();
	*/
			$str = @$c->toString();
			$param_map = $c->getMap();
			$str .= print_r( $param_map , TRUE );
			
			reset ( $param_map );
			//echo ( $str . "\n");
			$cache_key =  md5($str);
			
			$this->m_cache_key = $cache_key;
		//sfLogger::getInstance()->info ( "mySmartPager key [" . $this->m_criteria_class . "] [$cache_key] [$str]" );
		}
		return $this->m_cache_key;
	}
	
	public function infoToString()
	{
		return   ( "page:$this->m_page page_size:$this->m_page_size elements:" . count($this->m_data) );
	}
	/**
	 * this function creates an array with the pager's current state.
	 * this info should be passed onto the UI to properly display the pager
	 */
	public function getPagerInfo ()
	{
		// the paramters starting with a "." are part of the js pager object.
		// by starting with a "." they will be evaluated as variables in the context of the pager.
		// the other parameters should be placed as is in elements with the proper id 
		// that were initially created by  createHtmlPager
		
		// current_page
		$cp = $this->m_page;
		$output = array(
			".currentPage" => $cp,
			".maxPage" => $this->m_pager->getLastPage(),
			".objectsInPage" => count($this->m_data)	
//			, $this->m_prefix."CurrentPage" => $cp 
//			, $this->m_prefix."MaxPage" => $this->m_pager->getLastPage() 
			, "pagerHtml" => $this->createHtmlPager()	
			);
			
		$this->appendPageData( $output , $cp );
			
		return $output;
	}
	
	
	public function getData() 
	{
		return $this->m_data;
	}
	
	public function getNumberOfResults ()
	{
		return $this->m_number_of_results;
	}

	public function getNumberOfPages ()
	{
		return $this->m_pager->getLastPage();
	}
	
	public function createHtmlPager( $pager_id = null)
	{
		if ($this->getNumberOfResults() <= $this->m_page_size)
			return "";
		else 
			return mySmartPagerRenderer::createHtmlPager( $this->m_pager->getLastPage() , $this->m_page, $pager_id);
	}
	

	// cp - current_page
	private function appendPageData ( &$output , $cp)
	{
		// if on first page - left page should be the first
		// if on second page onwards - the current page should be in the middle
		$start_page = ( $cp == 1 ? 1 : $cp -1 );
		$end_page = min ( $start_page + self::NUMBER_OF_PAGES_IN_PAGER , $this->m_pager->getLastPage() );

		for ( $i=0 ; $i < self::NUMBER_OF_PAGES_IN_PAGER ; ++$i )
		{
			$page = $start_page + $i;
			if ( $page <= $end_page )
			{
				// should print
				$text = self::printPage ( $this->m_page_size , $page) ;
			}	
			else
			{
				// should hide !
				$page = 0;
				$text = "";
			}
			
			$output [ $this->m_prefix.$i ] = $text ;
			$output [ $this->m_prefix.$i.".page" ] = $page;
		}

	}
	
	private static function printPage ( $page_size , $current_page , $total_object_count = NULL )
	{
		$b = $current_page * $page_size;
		$a = $b - $page_size +1;

		if ( $total_object_count )
		{
			$b = min ( $b , $total_object_count );
		}
		
		$str = $a;
		if ( $b > $a )
		{
			$str .=  "-" . $b;
		}
		
		return  $str;
	}
}
?>