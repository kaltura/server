<?php

/**
 * widget service for full widget management
 *
 * @service widget
 * @package api
 * @subpackage services
 */
class WidgetService extends KalturaBaseService 
{
	// use initService to add a peer to the partner filter
	/**
	 * @ignore
	 */
	public function initService ($partner_id , $puser_id , $ks_str , $service_name , $action )
	{
		parent::initService ($partner_id , $puser_id , $ks_str , $service_name , $action );
		parent::applyPartnerFilterForClass ( new widgetPeer() ); 	
	}

	/**
	 * Add new widget, can be attached to entry or kshow
	 * SourceWidget is ignored.
	 * 
	 * @action add
	 * @param KalturaWidget $widget
	 * @return KalturaWidget
	 */
	function addAction(KalturaWidget $widget)
	{
		if ($widget->sourceWidgetId === null && $widget->uiConfId === null)
		{
			throw new KalturaAPIException(KalturaErrors::SOURCE_WIDGET_OR_UICONF_REQUIRED);
		}
		
		if ($widget->sourceWidgetId !== null)
		{
			$sourceWidget = widgetPeer::retrieveByPK($widget->sourceWidgetId);
			if (!$sourceWidget) 
				throw new KalturaAPIException(KalturaErrors::SOURCE_WIDGET_NOT_FOUND, $widget->sourceWidgetId);
				
			if ($widget->uiConfId === null)
				$widget->uiConfId = $sourceWidget->getUiConfId();
		}
		
		if ($widget->uiConfId !== null)
		{
			$uiConf = uiConfPeer::retrieveByPK($widget->uiConfId);
			if (!$uiConf)
				throw new KalturaAPIException(KalturaErrors::UICONF_ID_NOT_FOUND, $widget->uiConfId);
		}
		
		if ($widget->entryId !== null)
		{
			$entry = entryPeer::retrieveByPK($widget->entryId);
			if (!$entry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $widget->entryId);
		}
		
		$dbWidget = $widget->toWidget();
		$dbWidget->setPartnerId($this->getPartnerId());
		$dbWidget->setSubpId($this->getPartnerId() * 100);
		$widgetId = $dbWidget->calculateId($dbWidget);

		$dbWidget->setId($widgetId);
		$dbWidget->save();
		$savedWidget = widgetPeer::retrieveByPK($widgetId);
		
		$widget = new KalturaWidget(); // start from blank
		$widget->fromWidget($savedWidget);
		
		return $widget;
	}

	/**
 	 * Update exisiting widget
 	 * 
	 * @action update
	 * @param string $id 
	 * @param KalturaWidget $widget
	 * @return KalturaWidget
	 */	
	function updateAction( $id , KalturaWidget $widget )
	{
		$dbWidget = widgetPeer::retrieveByPK( $id );
		
		if ( ! $dbWidget )
			throw new KalturaAPIException ( APIErrors::INVALID_WIDGET_ID , $id );
		
		$widgetUpdate = $widget->toWidget();

		$allow_empty = true ; // TODO - what is the policy  ? 
		baseObjectUtils::autoFillObjectFromObject ( $widgetUpdate , $dbWidget , $allow_empty );
		
		$dbWidget->save();
		// TODO: widget in cache, should drop from cache

		$widget->fromWidget( $dbWidget );
		
		return $widget;
	}

	/**
	 * Get widget by id
	 *  
	 * @action get
	 * @param string $id 
	 * @return KalturaWidget
	 */		
	function getAction( $id )
	{
		$dbWidget = widgetPeer::retrieveByPK( $id );

		if ( ! $dbWidget )
			throw new KalturaAPIException ( APIErrors::INVALID_WIDGET_ID , $id );
		$widget = new KalturaWidget();
		$widget->fromWidget( $dbWidget );
		
		return $widget;
	}

	/**
	 * Add widget based on existing widget.
	 * Must provide valid sourceWidgetId
	 * 
	 * @action clone
	 * @paran KalturaWidget $widget
	 * @return KalturaWidget
	 */		
	function cloneAction( KalturaWidget $widget )
	{
		$dbWidget = widgetPeer::retrieveByPK( $widget->sourceWidgetId );
		
		if ( ! $dbWidget )
			throw new KalturaAPIException ( APIErrors::INVALID_WIDGET_ID , $widget->sourceWidgetId );

		$newWidget = widget::createWidgetFromWidget( $dbWidget , $widget->kshowId, $widget->entryId, $widget->uiConfId ,
			null , $widget->partnerData , $widget->securityType );
		if ( !$newWidget )
			throw new KalturaAPIException ( APIErrors::INVALID_KSHOW_AND_ENTRY_PAIR , $widget->kshowId, $widget->entryId );

		$widget = new KalturaWidget;
		$widget->fromWidget( $newWidget );
		return $widget;
	}
	
	/**
	 * Retrieve a list of available widget depends on the filter given
	 * 
	 * @action list
	 * @param KalturaWidgetFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaWidgetListResponse
	 */		
	function listAction( KalturaWidgetFilter $filter=null , KalturaFilterPager $pager=null)
	{
		if (!$filter)
			$filter = new KalturaWidgetFilter;
			
		$widgetFilter = new widgetFilter ();
		$filter->toObject( $widgetFilter );
		
		$c = new Criteria();
		$widgetFilter->attachToCriteria( $c );
		
		$totalCount = widgetPeer::doCount( $c );
		if ( $pager )	$pager->attachToCriteria( $c );
		$list = widgetPeer::doSelect( $c );
		
		$newList = KalturaWidgetArray::fromWidgetArray( $list );
		
		$response = new KalturaWidgetListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}