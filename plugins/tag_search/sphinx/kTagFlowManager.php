<?php
class kTagFlowManager implements kObjectCreatedEventConsumer, kObjectDeletedEventConsumer, kObjectChangedEventConsumer
{
    const TAGS_FIELD_NAME = "tags";
    
    const PARTNER_ID_FIELD = "partner_id";
    
    private static $specialCharacters = array ('!', '*', '"');
    private static $specialCharactersReplacement = array ('\\!', '\\*', '\\"');
    
	/* (non-PHPdoc)
     * @see kObjectDeletedEventConsumer::objectDeleted()
     */
    public function objectDeleted (BaseObject $object, BatchJob $raisedJob = null)
    {
        $tagsIdsToRemove = $this->checkExistForDelete($object);
        $tagsToRemove = TagPeer::retrieveByPKs($tagsIdsToRemove);
        foreach ($tagsToRemove as $tagToRemove)
        {
            $tagToRemove->delete();
        }
    
        return true;
    }

	/* (non-PHPdoc)
     * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
     */
    public function shouldConsumeDeletedEvent (BaseObject $object)
    {
        if (!defined("taggedObjectType::". strtoupper(get_class($object))))
            return;
        
        if (property_exists($object, self::TAGS_FIELD_NAME) && $object->getTags() != "")
        {
            return true;
        }
        return false;
        
    }

	/* (non-PHPdoc)
     * @see kObjectCreatedEventConsumer::objectCreated()
     */
    public function objectCreated (BaseObject $object)
    {
        $tagsToAdd = $this->checkExistsForAdd($object);
        if (count($tagsToAdd))
            $this->addTags($tagsToAdd, $this->getObjectIdByClassName(get_class($object)), $object->getPartnerId());
        
    }

	/* (non-PHPdoc)
     * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
     */
    public function shouldConsumeCreatedEvent (BaseObject $object)
    {
        if (!defined("taggedObjectType::". strtoupper(get_class($object))))
            return;
        
        if (property_exists($object, self::TAGS_FIELD_NAME) && $object->getTags() != "")
        {
            return true;
        }
        return false;
        
    }
	/* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectChanged()
     */
    public function objectChanged (BaseObject $object, array $modifiedColumns)
    {
        $tagsIdsToRemove = array();
        $oldTags = $object->getColumnsOldValue(self::getClassConstValue(get_class($object->getPeer()), self::TAGS_FIELD_NAME));
        if ($oldTags && $oldTags != "")
            $tagsIdsToRemove = $this->checkExistForDelete($object,$oldTags );
        $tagsToRemove = TagPeer::retrieveByPKs($tagsIdsToRemove);
        
        foreach ($tagsToRemove as $tagToRemove)
        {
            $tagToRemove->delete();
        } 

        $tagsToAdd = $this->checkExistsForAdd($object);
        $this->addTags($tagsToAdd, $this->getObjectIdByClassName(get_class($object)), $object->getPartnerId());
    }

	/* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
     */
    public function shouldConsumeChangedEvent (BaseObject $object, array $modifiedColumns)
    {
        if (!defined("taggedObjectType::". strtoupper(get_class($object))))
            return;
        
        if (property_exists($object, self::TAGS_FIELD_NAME) && in_array(self::getClassConstValue(get_class($object->getPeer()), self::TAGS_FIELD_NAME), $modifiedColumns) )
        {
            return true;
        }
        return false;
        
    }

    /**
     * Function which checks the object tags agains DB
     * and returns the tags strings which are new and need to be saved.
     * @param BaseObject $object
     * @return array
     */
	protected function checkExistsForAdd (BaseObject $object)
	{
	    KalturaLog::info("In Object Added handler");
	    $objectTags = $this->trimObjectTags($object->getTags());
	    $objectTags = str_replace(self::$specialCharacters, self::$specialCharactersReplacement, $objectTags);
	    if (!count($objectTags))
	    {
	        return array();
	    }
	    
	    $c = self::getTagObjectsByTagStringsCriteria($objectTags, $this->getObjectIdByClassName(get_class($object)), $object->getPartnerId());
	    $c->applyFilters();
	    
	    $numTagsFound = $c->getRecordsCount(); 
	   
	    if (!$numTagsFound)
	    {
	        return $objectTags;
	    }
	    
	    KalturaLog::debug("found tags ids: ".print_r($c->getFetchedIds(), true));
	    
	    $crit = new Criteria();
	    $crit->addAnd(TagPeer::ID , $c->getFetchedIds(), KalturaCriteria::IN);
	    $foundTagObjects = TagPeer::doSelect($crit);
	    KalturaLog::debug("found tags length: ".count($foundTagObjects));
	    $foundTags = array();
	    foreach ($foundTagObjects as $foundTag)
	    {
	        $foundTag->incrementInstanceCount();
	        $foundTags[] = $foundTag->getTag();
	    }
	    
	    return array_diff($objectTags, $foundTags);
	}
	
	/**
     * Function which checks the object tags agains DB
     * and returns an array of tags ids which need to be deleted.
     * @param BaseObject $object
     * @return array
     */
	protected function checkExistForDelete (BaseObject $object, $tagsToCheck = null)
	{
	    $objectTags = $tagsToCheck ? $this->trimObjectTags($tagsToCheck) : $this->trimObjectTags($object->getTags());
	    $objectTags = str_replace(self::$specialCharacters, self::$specialCharactersReplacement, $objectTags);
	    $tagsToKeep = array();
	    foreach($objectTags as $objectTag)
	    {
	        $peer = $object->getPeer();
	        
    	    $c = KalturaCriteria::create(get_class($object));
    	    $c->addAnd(self::PARTNER_ID_FIELD, $object->getPartnerId(), KalturaCriteria::EQUAL);
    	    $c->addAnd($peer::TAGS, $objectTag, KalturaCriteria::LIKE);
    	    $c->addAnd($peer::ID, array($object->getId()), KalturaCriteria::NOT_IN);
    	    $selectResults = $peer->doSelect($c);
    	    
    	    foreach ($selectResults as $selectResult)
    	    {
    	        $resultTags = $this->trimObjectTags($selectResult->getTags());
    	        if (in_array($objectTag, $resultTags) )
    	        {
//    	            if(isset($tagsToKeep[$objectTag]))
//    	                $tagsToKeep[$objectTag]++;
//    	            else
//    	                $tagsToKeep[$objectTag] = 1;
    	                
    	            if (!in_array($objectTag, $tagsToKeep))
    	            {
    	                $tagsToKeep[] = $objectTag;
    	            }
    	        }
    	    }
	    }
	    
	    KalturaLog::debug("tags to keep: ".print_r($tagsToKeep, true));
	    
	    if (count($tagsToKeep))
	    {
    	    //Decrement instance count for the tags that we keep
    	    $c = self::getTagObjectsByTagStringsCriteria($tagsToKeep, $this->getObjectIdByClassName(get_class($object)), $object->getPartnerId());
    	    $tagsToKeepObjects = TagPeer::doSelect($c);
    	    foreach ($tagsToKeepObjects as $tagToKeepObject)
    	    {
    	        /* @var $tagToKeepObject Tag */
    	        $tagToKeepObject->decrementInstanceCount();
    	    }
	    }
	    
	    //Return the IDs of the rest of the tags for removal.
	    $tagsToRemove = array_diff($objectTags, $tagsToKeep);
	    KalturaLog::debug("tags to delete: ".print_r($tagsToRemove, true));
	    
	    if ($tagsToRemove)
	    {
	    
    	    $c = self::getTagObjectsByTagStringsCriteria($tagsToRemove, $this->getObjectIdByClassName(get_class($object)) , $object->getPartnerId());
    	    $c->applyFilters();
    	    $recordsToRemove = $c->getRecordsCount();
    	    return $c->getFetchedIds();
	    }
	    
	    return array();
	    
	    
	}
	 
	/**
	 * Function creates new propel Tag objects and saves them.
	 * @param array $tagToAdd
	 */
	protected function addTags ($tagsToAdd, $objectType, $partnerId)
	{
	   
	    foreach ($tagsToAdd as $tagToAdd)
	    {
	        if (strlen($tagToAdd) >= TagSearchPlugin::MIN_TAG_SEARCH_LENGTH)
	        {
    	        $tag = new Tag();
    	        $tag->setTag(trim($tagToAdd));
    	        $tag->setObjectType($objectType);
    	        $tag->setPartnerId($partnerId);
    	        $tag->save();
	        }
	    }
	}
	
	/**
	 * Get class name and returns the class's enum
	 * @param string $className
	 * @return int
	 */
	protected function getObjectIdByClassName ($className)
	{
	    return self::getClassConstValue("taggedObjectType", $className);
	}
	
	/**
	 * Function extract the value of a string constant 
	 * @param string $className
	 * @param string $constString
	 */
	protected static function getClassConstValue ($className, $constString)
	{
	    return constant("$className::" . strtoupper($constString));
	}
	
	/**
	 * Function returns criteria for a search for tags with an array of tag strings.
	 * @param array $tagStrings
	 * @param int $objectType
	 * @param int $partnerId
	 * @return KalturaCriteria
	 */
	public static function getTagObjectsByTagStringsCriteria ($tagStrings, $objectType, $partnerId)
	{
	    $c = KalturaCriteria::create(TagPeer::OM_CLASS);
	    $c->addAnd(TagPeer::TAG, $tagStrings, KalturaCriteria::IN);
	    $c->addAnd(TagPeer::PARTNER_ID, $partnerId, KalturaCriteria::EQUAL);
	    $c->addAnd(TagPeer::OBJECT_TYPE, $objectType, KalturaCriteria::EQUAL);
	    return $c;
	}
	
	/**
	 * Function removes spaces between the tags
	 * @param string $tagsString
	 * @return array
	 */
	protected function trimObjectTags ($tagsString)
	{
	    $tags = explode(",", $tagsString);
		$tagsToReturn = array();
        foreach($tags as $tag)
        {
        	$tag = trim($tag);
            if ($tag){
            	$tagsToReturn[] = $tag;
            }
        }
        return $tagsToReturn;		
	}
	
}