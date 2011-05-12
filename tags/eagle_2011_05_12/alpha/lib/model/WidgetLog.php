<?php

/**
 * Subclass for representing a row from the 'widget_log' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class WidgetLog extends BaseWidgetLog
{
	
	public static function getKshowEntryId($kshow_id, $entry_id)
	{
		if ($entry_id == -1)
		{
			$kshow = kshowPeer::retrieveByPK($kshow_id)	;
			if ($kshow)
				$entry_id = $kshow->getShowEntryId();
		}
		
		return $entry_id;
	}
	
	public static function createWidgetLog ( $referer , $ip ,  $kshow_id , $entry_id , $kmedia_type , $widget_type , $action = 0 )
	{
		$entry_id = self::getKshowEntryId($kshow_id, $entry_id);
		
		$unwanted_pattern = "/MyToken=[^\&]*/i";
		$fixed_referer = preg_replace ( $unwanted_pattern , "" , $referer );
		
		$unwanted_pattern = "/#.*/i";
		$fixed_referer = preg_replace ( $unwanted_pattern , "" , $fixed_referer );
		
		$c = new Criteria();
		$c->add ( WidgetLogPeer::REFERER , $fixed_referer );
		$c->add ( WidgetLogPeer::ENTRY_ID , $entry_id ); 
		$c->add ( WidgetLogPeer::KSHOW_ID , $kshow_id );
		
		$widget_log = WidgetLogPeer::doSelectOne( $c );
		
		$longIP = ip2long( $ip );// to convert back, use long2ip
		if ( $widget_log )
		{
			if ( $longIP != -1 )
			{
				if ( $longIP == $widget_log->getIp1() ) $widget_log->setIp1Count(  $widget_log->getIp1Count()+1 ) ;
				elseif ( $longIP == $widget_log->getIp2() ) $widget_log->setIp2Count(  $widget_log->getIp2Count()+1 ) ;
				else 
				{
					if ( $widget_log->getIp1Count() < $widget_log->getIp2Count() )
					{
						$widget_log->setIp1Count( 1 );
						$widget_log->setIp1 ($longIP);
					}
					else
					{
						$widget_log->setIp2Count( 1 );
						$widget_log->setIp2 ($longIP);
					}
				}
				
				// make sure that ipCount1 is always the bigger one - to make sorting easiser
				if ( $widget_log->getIp2Count() > $widget_log->getIp1Count())
				{
					// swap
					$temp_count = $widget_log->getIp1Count(  );
					$temp_ip = $widget_log->getIp1();
					$widget_log->setIp1Count(  $widget_log->getIp2Count() );
					$widget_log->setIp1 ( $widget_log->getIp2());						
					$widget_log->setIp2Count( $temp_count );
					$widget_log->setIp2 ( $temp_ip );						
				}
			}
		}
		else
		{
			$widget_log = new WidgetLog();
			$widget_log->setIp1Count( 1 );
			$widget_log->setIp1 ($longIP);		
			$widget_log->setKshowId( $kshow_id);
			$widget_log->setEntryId( $entry_id);
			$widget_log->setKmediaType( $kmedia_type);
			$widget_log->setWidgetType( $widget_type);
			$widget_log->setReferer( $fixed_referer );
			
			if ($entry_id)
			{
				$entry = entryPeer::retrieveByPK($entry_id);
				if ($entry)
				{
					$widget_log->setPartnerId( $entry->getPartnerId());
					$widget_log->setSubpId($entry->getSubpId());
				}
			}
		}

		if ( $action == 0 )
		{
			$widget_log->setViews ( $widget_log->getViews() + 1 );
		}
		elseif ( $action == 1 )
		{
			$widget_log->setPlays ( $widget_log->getPlays() + 1 );
		} 
	
		$widget_log->save();		
		
	}
	
	public static function incPlaysIfExists  ( $kshow_id , $entry_id  )
	{
		$entry_id = self::getKshowEntryId($kshow_id, $entry_id);
		
		$c = new Criteria();
		$c->add ( WidgetLogPeer::ENTRY_ID , $entry_id ); 
		$c->add ( WidgetLogPeer::KSHOW_ID , $kshow_id );
		$c->addAscendingOrderByColumn( WidgetLogPeer::ID ); // the first found will always stay the first found 
		$widget_log = WidgetLogPeer::doSelectOne( $c );		
		
		// update the statistics of the first existing widget_log
		if ( $widget_log )
		{
			$widget_log->setPlays ( $widget_log->getPlays() + 1 );		
			$widget_log->save();	
		}
		else
		{
			// if does not exist - do nothing.
			// there is no use incrementing the plays if the widget was never created
		}
	}
	
	public function getIp1AsText()
	{
		return long2ip( $this->getIp1());
	}

	public function getIp2AsText()
	{
		return long2ip( $this->getIp2());
	}
	
}
