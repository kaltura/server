<?php
require_once ( "kalturaSystemAction.class.php" );

class createWidgetsAction extends kalturaSystemAction
{
	
	/**
	 * 
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$kshow_ids = $this->getP ( "kshow_ids") ;
		$partner_id = $this->getP ( "partner_id") ;
//		$subp_id = $this->getP ( "subp_id") ;
		$source_widget_id= $this->getP ( "source_widget_id" , 201 ) ;
		$submitted = $this->getP ( "submitted");
		$method = $this->getP ( "method" , "partner" );
		$create = $this->getP ( "create" );
		$limit = $this->getP ( "limit" , 20 );
		if ( $limit > 300 ) $limit = 300;
		
		$this->kshow_ids = $kshow_ids;
		$this->partner_id = $partner_id;
//		$this->subp_id = $subp_id;
		$this->source_widget_id = $source_widget_id;
		$this->method = $method;
		$this->create = $create;
		$this->limit = $limit;
		
		$errors = array( );
		$res = array();
		$this->errors = $errors;
		
		if ( $submitted )
		{
			// fetch all kshows that don't have widgets
			$c = new Criteria();
			$c->setLimit ( $limit );
			if ( $method == "list" )
			{
				$c->add ( kshowPeer::ID , @explode ( "," , $kshow_ids ) , Criteria::IN );				
			}
			else
			{
				$c->add ( kshowPeer::PARTNER_ID , $partner_id );
				if ( $create )
				{
					// because we want to create - select those kshows that are not marked as "have widgets"
					$c->add ( kshowPeer::INDEXED_CUSTOM_DATA_3 , NULL , Criteria::EQUAL );
				}
			}
			$c->addAscendingOrderByColumn( kshowPeer::CREATED_AT );
			// start at a specific int_id
			// TODO
			$kshows = KshowPeer::doSelect( $c );
			$kshow_id_list = $this->getIdList ( $kshows , $partner_id , $errors );
			
			$fixed_kshows = array();
			
//			$res [] = print_r ( $kshow_id_list ,true );
			$this->res = $res;			//return;
			$this->errors = $errors;
			
			if ( $kshow_id_list )
			{
			//	$kshow_id_list_copy = array_  $kshow_id_list ;
				$widget_c = new Criteria();
				$widget_c->add ( widgetPeer::PARTNER_ID , $partner_id );
				$widget_c->add ( widgetPeer::KSHOW_ID , $kshow_id_list , Criteria::IN );
				$widgets = widgetPeer::doSelect( $widget_c );
				
				// - IMPORTANT - add the kshow->setIndexedCustomData3 ( $widget_id ) for wikis

				
				foreach ( $widgets as $widget )
				{
					$kshow_id = $widget->getKshowId();
					if ( in_array ( $kshow_id, $fixed_kshows ) ) continue;
					// mark the kshow as one that has a widget
					$kshow = $this->getKshow ( $kshows , $kshow_id );
					$kshow->setIndexedCustomData3( $widget->getId());
					$kshow->save();
					unset ( $kshow_id_list[$kshow_id]);
					$fixed_kshows[$kshow_id]=$kshow_id;
//					print_r ( $kshow_id_list );
				}

			// create widgets for those who are still on the list === don't have a widget				
				foreach ( $kshow_id_list as $kshow_id )
				{
					if ( in_array ( $kshow_id, $fixed_kshows ) ) continue;
					$kshow = $this->getKshow ( $kshows , $kshow_id );
					$widget = widget::createWidget( $kshow , null , $source_widget_id ,null);
					$kshow->setIndexedCustomData3( $widget->getId());
					$kshow->save();
					$fixed_kshows[$kshow_id]=$kshow_id;
				}
			
			}
			
					
			// create a log file of the kaltura-widget tagss for wiki
			$partner = PartnerPeer::retrieveByPK( $partner_id );
			if  ( $partner )
			{
				$secret = $partner->getSecret ();	
				foreach ( $kshows as $kshow )
				{
					$kshow_id = $kshow->getId();
					$article_name = "Video $kshow_id";
					$widget_id = $kshow->getIndexedCustomData3(); // by now this kshow should have the widget id 
					$subp_id = $kshow->getSubpId();
					$md5 = md5 ( $kshow_id  . $partner_id  .$subp_id . $article_name . $widget_id .  $secret );
					$hash = substr ( $md5 , 1 , 10 );
					$values = array ( $kshow_id , $partner_id , $subp_id , $article_name ,$widget_id , $hash);
					
					$str = implode ( "|" , $values);
					$base64_str = base64_encode( $str );
					
					$res [] = "kalturaid='$kshow_id'	kwid='$base64_str'	'$str'\n";
				}
			}
		}
		
		$this->res = $res;
	}
	
	
	private function getIdList ( $objs , $partner_id , &$errors )
	{
		if ( is_array ( $objs  ))
		{
			$id = array();
			foreach ( $objs as $obj )
			{
				if ( $partner_id == $obj->getPartnerId() )
				{
					$id[] = $obj->getId();
				}
				else
				{
					$errors[] = $obj->getId() . " is of partner " . $obj->getPartnerId() . " instead of $partner_id";
				}
			}
			return $id;
		}
		return null;
	}
	
	private function getKshow ( $kshows , $kshow_id )
	{
		foreach ( $kshows as $kshow )
		{
			if( $kshow_id == $kshow->getId() ) return $kshow;
		}
		return null;
	}
}

 
?>