<?php
/**
 * @package api
 * @subpackage filters
 */
abstract class KalturaFilter extends KalturaObject
{
	const LT = "lt";
	const LTE = "lte";
	const GT = "gt";
	const GTE = "gte";
	const EQ = "eq";
	const LIKE = "like";
	const XLIKE = "xlike";
	const LIKEX = "likex";
	const IN = "in";
	const NOT_IN = "notin";
	const NOT = "not";
	const BIT_AND = "bitand";
	const BIT_OR = "bitor";
	const MULTI_LIKE_OR = "mlikeor";
	const MULTI_LIKE_AND = "mlikeand";
	const MATCH_OR = "matchor";
	const MATCH_AND = "matchand";
	const NOT_CONTAINS = "notcontains";

	protected function getMapBetweenObjects ( )
	{
		return array_merge(parent::getMapBetweenObjects(), array("orderBy" => "_order_by"));
	}
	
	protected function getOrderByMap ( )
	{
		return array();
	}
	
	/**
	 * @var string $orderBy
	 */
	public $orderBy;
	
	/**
	 * @var KalturaSearchItem
	 */
	public $advancedSearch;
	
	/**
	 * @return baseObjectFilter
	 */
	abstract protected function getCoreFilter();
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
		{
			$object_to_fill = $this->getCoreFilter();
		}
		
	    // translate the order by properties
	    $newOrderBy = "";
	    $orderByMap = $this->getOrderByMap();
	    if ($orderByMap)
		{
		    $orderProps = explode(",", $this->orderBy);
		    foreach($orderProps as $prop)
		    {
		         if (isset($orderByMap[$prop]))
		         {
		             $newOrderBy .= ($orderByMap[$prop] . ","); 
		         }
		    }
		}
		if (strpos($newOrderBy,",") === strlen($newOrderBy) - 1)
		    $newOrderBy = substr($newOrderBy, 0, strlen($newOrderBy) - 1);
		
		$this->orderBy = $newOrderBy;
		
		$typeReflector = KalturaTypeReflectorCacher::get(get_class($this));
		
		foreach ( $this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
		 	if ( is_numeric( $this_prop) ) 
		 		$this_prop = $object_prop;
		 		
			$value = $this->$this_prop;
			if (is_null($value))
				continue;
			
			$propertyInfo = $typeReflector->getProperty($this_prop);
			if(!$propertyInfo)
			{
				KalturaLog::alert("Cannot load property info for attribute [$this_prop] in object [" . get_class($this) . "] try delete the cache");
				continue;
			}
			
			if($propertyInfo->isDynamicEnum())
			{
				$propertyType = $propertyInfo->getType();
				$enumType = call_user_func(array($propertyType, 'getEnumClass'));
				$value = kPluginableEnumsManager::apiToCore($enumType, $value);
			}
			elseif($propertyInfo->getDynamicType()&& strlen($value))
			{
				$propertyType = $propertyInfo->getDynamicType();
				$enumType = call_user_func(array($propertyType, 'getEnumClass'));
				
				$values = explode(',', $value);
				$finalValues = array();
				foreach($values as $val)
					$finalValues[] = kPluginableEnumsManager::apiToCore($enumType, $val);
				$value = implode(',', $finalValues);
			}
			
		 	// convert the v3 prop name to the naming convension of the core filter
		 	$filter_prop_name = self::translatePropNames ( $object_prop );
		 	$object_to_fill->set($filter_prop_name, $value);
		 }		
		 		
		if(is_object($this->advancedSearch))
		{
			if($this->advancedSearch instanceof KalturaSearchItem)
			{
				$advancedSearch = $this->advancedSearch->toObject();
				if($advancedSearch)
					$object_to_fill->setAdvancedSearch($advancedSearch);
			}
		}
			
		return $object_to_fill;		
	}	
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$reflector = KalturaTypeReflectorCacher::get(get_class($this));
		
		foreach ($this->getMapBetweenObjects() as $this_prop => $object_prop )
		{
			if ( is_numeric( $this_prop) ) 
			    $this_prop = $object_prop;
			    
		    if (array_key_exists($object_prop, $source_object->fields))
		    {
		    	$value = $source_object->get($object_prop);
		    	$property = $reflector->getProperty($this_prop);
                if($property->isDynamicEnum())
                {
					$propertyType = $property->getType();
					$enumType = call_user_func(array($propertyType, 'getEnumClass'));
                	$value = kPluginableEnumsManager::coreToApi($enumType, $value);
                }
                elseif($property->getDynamicType())
                {
					$propertyType = $property->getDynamicType();
					$enumType = call_user_func(array($propertyType, 'getEnumClass'));
					if(!is_null($value))
					{
	                	$values = explode(',', $value);
	                	$finalValues = array();
	                	foreach($values as $val)
	                		$finalValues[] = kPluginableEnumsManager::coreToApi($enumType, $val);
	                	$value = implode(',', $finalValues);
					}
                }
                	
		    	$this->$this_prop = $value;
		    }
		    else
		    {
		    	KalturaLog::alert("field [$object_prop] was not found on filter object class [" . get_class($source_object) . "]");
		    }
		}
		
		$newOrderBy = "";
	    $orderByMap = $this->getOrderByMap();
	    if ($orderByMap)
		{
		    $orderProps = explode(",", $this->orderBy);
		    foreach($orderProps as $prop)
		    {
				$key = array_search($prop, $orderByMap);
				if ($key !== false)
				{
					$newOrderBy .= ($key . ","); 
				}
		    }
		}
		if (strpos($newOrderBy,",") === strlen($newOrderBy) - 1)
		    $newOrderBy = substr($newOrderBy, 0, strlen($newOrderBy) - 1);
		    
	    $this->orderBy = $newOrderBy;
	
	    $advancedSearch = $source_object->getAdvancedSearch();
		if(is_object($advancedSearch) && $advancedSearch instanceof AdvancedSearchFilterItem)
		{
			$apiClass = $advancedSearch->getKalturaClass();
			if(!class_exists($apiClass))
			{
				KalturaLog::err("Class [$apiClass] not found");
			}
			else 
			{
				$this->advancedSearch = new $apiClass();
				$this->advancedSearch->fromObject($advancedSearch);
			}
		}
	}
	
	private static function translatePropNames ( $prop_name_with_operator )
	{
		return $prop_name_with_operator;
//		@list ( $field , $operator ) = explode ( "_" , $this_prop_name );
//		if ( ! $operator ) $operator = "eq";
//		return "_{$operator}_"
	}
	
	protected function preparePusersToKusersFilter( $puserIdsCsv )
	{
		$kuserIdsArr = array();
		$puserIdsArr = explode(',',$puserIdsCsv);
		$kuserArr = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), $puserIdsArr);

		foreach($kuserArr as $kuser)
		{
			$kuserIdsArr[] = $kuser->getId();
		}

		if(!empty($kuserIdsArr))
		{
			return implode(',',$kuserIdsArr);
		}

		return -1; // no result will be returned if no puser exists
	}
	
	protected function prepareKusersToPusersFilter( $kuserIdsCsv )
	{
		$puserIdsArr = array();
		$kuserIdsArr = explode(',',$kuserIdsCsv);
		$kuserArr = kuserPeer::retrieveByPKs($kuserIdsArr);

		foreach($kuserArr as $kuser)
		{
			$puserIdsArr[] = $kuser->getPuserId();
		}

		if(!empty($puserIdsArr))
		{
			return implode(',',$puserIdsArr);
		}

		return -1; // no result will be returned if no puser exists
	}
	
}
