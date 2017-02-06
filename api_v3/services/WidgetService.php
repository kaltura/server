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
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('widget'); 	
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
		
		if(!is_null($widget->enforceEntitlement) && $widget->enforceEntitlement == false && kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_DISABLE_ENTITLEMENT_FOR_WIDGET_WHEN_ENTITLEMENT_ENFORCEMENT_ENABLE);
		
		if ($widget->entryId !== null)
		{
			$entry = entryPeer::retrieveByPK($widget->entryId);
			if (!$entry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $widget->entryId);
		}
		elseif ($widget->enforceEntitlement != null && $widget->enforceEntitlement == false)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_DISABLE_ENTITLEMENT_WITH_NO_ENTRY_ID);
		}
		
		$dbWidget = $widget->toInsertableWidget();
		$dbWidget->setPartnerId($this->getPartnerId());
		$dbWidget->setSubpId($this->getPartnerId() * 100);
		$widgetId = $dbWidget->calculateId($dbWidget);

		$dbWidget->setId($widgetId);
		
		if ($entry && $entry->getType() == entryType::PLAYLIST)
			$dbWidget->setIsPlayList(true);
			
		$dbWidget->save();
		$savedWidget = widgetPeer::retrieveByPK($widgetId);
		
		$widget = new KalturaWidget(); // start from blank
		$widget->fromObject($savedWidget, $this->getResponseProfile());
		
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
		
		if(!is_null($widget->enforceEntitlement) && $widget->enforceEntitlement == false && kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_DISABLE_ENTITLEMENT_FOR_WIDGET_WHEN_ENTITLEMENT_ENFORCEMENT_ENABLE);
		
		if ($widget->entryId !== null)
		{
			$entry = entryPeer::retrieveByPK($widget->entryId);
			if (!$entry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $widget->entryId);
		}
		elseif ($widget->enforceEntitlement != null && $widget->enforceEntitlement == false)
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_DISABLE_ENTITLEMENT_WITH_NO_ENTRY_ID);
		}
			
		$widgetUpdate = $widget->toUpdatableWidget();
		
		if ($entry && $entry->getType() == entryType::PLAYLIST)
		{
			$dbWidget->setIsPlayList(true);
		}
		else 
		{
			$dbWidget->setIsPlayList(false);
		}

		$allow_empty = true ; // TODO - what is the policy  ? 
		baseObjectUtils::autoFillObjectFromObject ( $widgetUpdate , $dbWidget , $allow_empty );
		
		$dbWidget->save();
		// TODO: widget in cache, should drop from cache

		$widget->fromObject($dbWidget, $this->getResponseProfile());
		
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
		$widget->fromObject($dbWidget, $this->getResponseProfile());
		
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
		$widget->fromObject($newWidget, $this->getResponseProfile());
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
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria ( $c );
		$list = widgetPeer::doSelect( $c );
		
		$newList = KalturaWidgetArray::fromDbArray($list, $this->getResponseProfile());
		
		$response = new KalturaWidgetListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}