<?php

class kBusinessConvertDL
{
	public static function parseFlavorDescription(flavorParamsOutputWrap $flavor)
	{
		$description = '';
		if(is_array($flavor->_errors) && count($flavor->_errors))
		{
			$errDesc = '';
			foreach($flavor->_errors as $section => $errors)
				$errDesc .= "$section errors: " . join("; ", $errors) . "\n";
				
			KalturaLog::log("Flavor errors: $errDesc");
			$description .= $errDesc;
		}
			
		if(is_array($flavor->_warnings) && count($flavor->_warnings))
		{
			$errDesc = '';
			foreach($flavor->_warnings as $section => $errors)
				$errDesc .= "$section warnings: " . join("; ", $errors) . "\n";
				
			KalturaLog::log("Flavor warnings: $errDesc");
			$description .= $errDesc;
		}
		return $description;
	}
	
	protected static function isFlavorLower(flavorParams $target, flavorParams $compare)
	{
		// currently check only the bitrate
		return ($target->getVideoBitrate() < $compare->getVideoBitrate());
	}
	
	public static function filterTagFlavors(array $flavors)
	{
		KalturaLog::log("Filter Tag Flavors, " . count($flavors) . " flavors supplied");
		
		// check if there is a complete flavor
		$hasComplied = false;
		$hasForced = false;
		$originalFlavorParamsIds = array();
		foreach($flavors as $flavorParamsId => $flavor)
		{
			$originalFlavorParamsIds[] = $flavor->getFlavorParamsId();
			if(!$flavor->_isNonComply)
				$hasComplied = true;
				
			if($flavor->_force)
				$hasForced = true;
		}
		
		$originalFlavorParams = array();
		$dbOriginalFlavorParams = flavorParamsPeer::retrieveByPKs($originalFlavorParamsIds);
		foreach($dbOriginalFlavorParams as $dbFlavorParams)
			$originalFlavorParams[$dbFlavorParams->getId()] = $dbFlavorParams;
		
		// return only complete flavors
		if($hasComplied)
			KalturaLog::log("Has complied flavors");
		if($hasForced)
			KalturaLog::log("Has forced flavors");
		if($hasComplied || $hasForced)
			return $flavors;
		
		// find the lowest flavor
		$lowestFlavorParamsId = null;
		foreach($flavors as $flavorParamsId => $flavor)
		{
			if(!$flavor->IsValid())
				continue;
				
			// is lower than the selected
			if(!isset($originalFlavorParams[$flavor->getFlavorParamsId()]))
				continue;
				
			$currentOriginalFlavor = $originalFlavorParams[$flavor->getFlavorParamsId()];
			
			// is first flavor to check
			if(is_null($lowestFlavorParamsId))
			{				
				$lowestFlavorParamsId = $flavorParamsId;
				continue;
			}
			
			$lowestOriginalFlavor = $originalFlavorParams[$flavors[$lowestFlavorParamsId]->getFlavorParamsId()];
			if(self::isFlavorLower($currentOriginalFlavor, $lowestOriginalFlavor))
				$lowestFlavorParamsId = $flavorParamsId;
		}
		
		if($lowestFlavorParamsId)
		{
			KalturaLog::log("Lowest flavor selected [$lowestFlavorParamsId]");
			$flavors[$lowestFlavorParamsId]->_create_anyway = true;
		}
		
		return $flavors;
	}
	
	/**
	 * compareFlavors compares to flavorParamsOutput and decide which should be performed first
	 * 
	 * @param flavorParamsOutput $a
	 * @param flavorParamsOutput $b
	 */
	public static function compareFlavors(flavorParamsOutput $a, flavorParamsOutput $b)
	{
		$flavorA = $a->getId();
		$flavorB = $b->getId();
	
		if($a->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS && $b->getReadyBehavior() > flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS)
		{
			KalturaLog::debug("flavor[$flavorB] before flavor[$flavorA] at line[" . __LINE__ . "]");
			return 1;
		}
		
		if($a->getReadyBehavior() > flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS && $b->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS)
		{
			KalturaLog::debug("flavor[$flavorA] before flavor[$flavorB] at line[" . __LINE__ . "]");
			return -1;
		}
			
		if($a->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL && $b->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED)
		{
			KalturaLog::debug("flavor[$flavorB] before flavor[$flavorA] at line[" . __LINE__ . "]");
			return 1;
		}
			
		if($a->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED && $b->getReadyBehavior() == flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL)
		{
			KalturaLog::debug("flavor[$flavorA] before flavor[$flavorB] at line[" . __LINE__ . "]");
			return -1;
		}
			
		if($a->getVideoBitrate() > $b->getVideoBitrate())
		{
			KalturaLog::debug("flavor[$flavorB] before flavor[$flavorA] at line[" . __LINE__ . "]");
			return 1;
		}
			
		KalturaLog::debug("flavor[$flavorA] before flavor[$flavorB] at line[" . __LINE__ . "]");
		return -1;
	}
	
	/**
	 * compareFlavorsByHeight compares to flavorParamsOutput objects by height
	 * 
	 * @param flavorParamsOutput $a
	 * @param flavorParamsOutput $b
	 */
	public static function compareFlavorsByHeight(flavorParamsOutput $a, flavorParamsOutput $b)
	{
		if($a->getHeight() > $b->getHeight())
			return 1;
			
		return -1;
	}
}