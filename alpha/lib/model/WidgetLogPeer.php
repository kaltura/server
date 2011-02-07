<?php

/**
 * Subclass for performing query and update operations on the 'widget_log' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class WidgetLogPeer extends BaseWidgetLogPeer
{
	public static function getWidgetOrderedPager( $order, $pageSize, $page , $criteria = null)
	{
		$c = $criteria;
		if ( $c == null )	$c = new Criteria();
		
		if ( $order )
		{
			$order_dir = 1;
			if ( $order[0] == '+' || $order[0] == '-' )
			{
				$order_dir = $order[0] == '+' ? $order_dir = 1 : $order_dir = -1;
				$order = substr($order ,1 );
			}
			$fixed_order = "widget_log." . strtoupper( $order );
			$should_sort =  in_array ( $fixed_order , array ( WidgetLogPeer::ID , WidgetLogPeer::KSHOW_ID , WidgetLogPeer::ENTRY_ID , 
				WidgetLogPeer::REFERER , WidgetLogPeer::VIEWS , WidgetLogPeer::PLAYS , WidgetLogPeer::IP1_COUNT, WidgetLogPeer::CREATED_AT ) );

			if ( $should_sort )
			{
				if ( $order_dir == 1 )
					$c->addAscendingOrderByColumn( $fixed_order );
				else
					$c->addDescendingOrderByColumn( $fixed_order );
			}
			
		}
		
		$pager = new sfPropelPager('WidgetLog', $pageSize);
		$pager->setPeerMethod( "doSelectJoinentry" );
		$pager->setPeerCountMethod( "doCountJoinentry" );
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
}
