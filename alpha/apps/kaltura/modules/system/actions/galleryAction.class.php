<?php
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );
require_once(MODULES . 'partnerservices2/actions/listentriesAction.class.php' );

class galleryAction extends kalturaSystemAction
{
	/**
	 * Will investigate a single entry
	 */
	public function execute()
	{
		$partial = $this->getP ( "partial");
		$this->widget = null;
		
		$this->forceSystemAuthentication();

//		myDbHelper::$use_alternative_con = null;
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
		
		// dont' filter out anything
		entryPeer::setUseCriteriaFilter(false);
		
		$partner_id = $this->getP ( "partner_id" );
		
		$this->entries = $this->widget_id = null;
		$this->count = 0;
		$this->page = $this->getP ( "page" , 0 ) ;
		$this->page_size = $this->getP ( "page_size" , 25 );
		$this->ready_only = $this->getP ( "ready_only" , 0 );
		$this->gte_int_id = $this->getP ( "gte_int_id" , null );
		
		$this->widget_id = $this->getP ( "widget_id" );
		$this->ui_conf_id = $this->getP ( "ui_conf_id" );
		$this->is_playlist = $this->getP ( "is_playlist" );
		$this->playlist_id = $this->getP ( "playlist_id" );
		
		$offset = ($this->page)* $this->page_size;
			
		if ( $partner_id !== null )
		{
			$c = new Criteria();
			if ( $partner_id != "ALL" ) // is is a special backdoor word for viewing all partners
				$c->add ( entryPeer::PARTNER_ID , $partner_id );
			if ( $this->ready_only )
			{
				$c->add ( entryPeer::STATUS , 2 );
			}
			
			if ( $entry_ids = $this->getP ( "entry_ids" ) )
			{
				$entry_id_arr = explode ( "," , $entry_ids );
				$c->Add ( entryPeer::ID , $entry_id_arr , Criteria::IN );
			}
			
			$search_text = $this->getP ( "filter__like_search_text");
			if ( $search_text )
			{
				$c->add ( entryPeer::SEARCH_TEXT , "%$search_text%" , Criteria::LIKE );
			}
			
			if ( $this->gte_int_id  )
			{
				$c->add ( entryPeer::INT_ID , $this->gte_int_id , Criteria::GREATER_EQUAL );
			}

			if (  $this->getP ("filter__in_type_all") )
			{
			}
			else
			{			
				$media_type_arr = array ( $this->getP ("filter__in_type_1" ),
						$this->getP ("filter__in_type_2" ),
						$this->getP ("filter__in_type_5" ),
						$this->getP ("filter__in_type_6" ) );
				$c->add ( entryPeer::MEDIA_TYPE , $media_type_arr , Criteria::IN );  
			}
			
			if (  $this->getP ("filter__in_status_all" ) )
			{
			}
			else
			{
				
				$status_arr = array ( 
						$this->getP ("filter__in_type_0" ),
						$this->getP ("filter__in_type_1" ),
						$this->getP ("filter__in_type_2" ),
						$this->getP ("filter__in_type_3" ),
						$this->getP ("filter__in_type_6" ));
				if ( $this->getP ("filter__in_status_err" ) )
				{
					$status_arr[]=-1;
					$status_arr[]=-2;
				}					
				$c->add ( entryPeer::STATUS , $status_arr , Criteria::IN );  
			}
				
			if ( $this->getP ("filter__gte_created_at" ))
			{
				$c->addAnd ( entryPeer::CREATED_AT , $this->getP ("filter__gte_created_at" ) , Criteria::GREATER_EQUAL );
			}

			if ( $this->getP ("filter__lte_created_at" ))
			{
				$to_date = $this->getP ("filter__lte_created_at" );
				$timeStamp = strtotime( $to_date );
				$timeStamp += 24 * 60 * 60 ; // inc one day 
				$to_date_str =  date("Y-m-d", $timeStamp);
				$c->addAnd ( entryPeer::CREATED_AT , $to_date_str , Criteria::LESS_EQUAL );
			}
			
			$this->count = entryPeer::doCount( $c );					
			
			$c->addAscendingOrderByColumn ( entryPeer::INT_ID );
			
			$c->setLimit ( $this->page_size  );	
			$c->setOffset( $offset );
			
			$this->entries = entryPeer::doSelect( $c );
			
			if ( ! $partial )
			{
				// no need for widget if displaying partial page
				$d = new Criteria();
				$d->add ( widgetPeer::PARTNER_ID , $partner_id );
				if ( $this->widget_id )
				{
					$d->add ( widgetPeer::ID , $this->widget_id );
				}
				else
				{
					$d->add ( widgetPeer::SOURCE_WIDGET_ID , "" );
				}
				$this->widget = widgetPeer::doSelectOne( $d );
				
				if ( ! $this->widget  )
				{
					$d = new Criteria();
					$d->add ( widgetPeer::PARTNER_ID , $partner_id );
					$d->addAscendingOrderByColumn( widgetPeer::CREATED_AT );
				 
					$this->widget = widgetPeer::doSelectOne( $d );
				}
			}
		}
		
		if ($this->entries == null ) $this->entries = array();
		$this->partner_id = $partner_id ; 
		
		if ( $partial ) return "PartialSuccess";
	}
}
?>