<?php
class kTagFlowManager implements kObjectCreatedEventConsumer, kObjectDeletedEventConsumer, kObjectChangedEventConsumer
{
    const TAGS_FIELD_NAME = "tags";
    
    const PARTNER_ID_FIELD = "partner_id";
    
    public static $specialCharacters = array ('\\', '!', '*', '"', );
    public static $specialCharactersReplacement = array ('\\\\', '\\!', '\\*', '\\"');
    
    const NULL_PC = "NO_PC";
    
	/* (non-PHPdoc)
     * @see kObjectDeletedEventConsumer::objectDeleted()
     */
    public function objectDeleted (BaseObject $object, BatchJob $raisedJob = null)
    {
    	if (! ($object instanceof categoryEntry))
    	{
        	self::decrementExistingTagsInstanceCount($object->getTags(), $object->getPartnerId(), get_class($object));  
    	}
    	else 
    	{
    		$privacyContexts = $object->getPrivacyContext() != "" ? explode(",", $object->getPrivacyContext()) : array();
    		if (!count($privacyContexts))
    				$privacyContexts[] = kEntitlementUtils::DEFAULT_CONTEXT; 
			$entry = $this->getEntryByIdNoFilter($object->getEntryId());
    		self::decrementExistingTagsInstanceCount($entry->getTags(), $entry->getPartnerId(), get_class($entry), $privacyContexts);
    	}  
        return true;
    }

	/* (non-PHPdoc)
     * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
     */
    public function shouldConsumeDeletedEvent (BaseObject $object)
    {
        if (defined("taggedObjectType::". strtoupper(get_class($object))))
        {
	        if (property_exists($object, self::TAGS_FIELD_NAME) && $object->getTags() != "")
	        {
	            return true;
	        }
        	
        } 
        
        if ($object instanceof categoryEntry)
        {
        	$entry = $this->getEntryByIdNoFilter($object->getEntryId());
        	if ($entry && $entry->getTags())
        		return true;
        }

        
        return false;
        
    }
    

	/* (non-PHPdoc)
     * @see kObjectCreatedEventConsumer::objectCreated()
     */
    public function objectCreated (BaseObject $object)
    {
    	try
    	{
    		if (!($object instanceof categoryEntry))
    		{
		        self::addOrIncrementTags($object->getTags(), $object->getPartnerId(), get_class($object));
    		}
    		else
    		{
    			/* @var $object categoryEntry */
     			$privacyContexts = $object->getPrivacyContext() != "" ? self::trimObjectTags($object->getPrivacyContext()) : array();
    			if (!count($privacyContexts))
    				$privacyContexts[] = kEntitlementUtils::DEFAULT_CONTEXT; 
    			$entry = $this->getEntryByIdNoFilter($object->getEntryId());
    			self::addOrIncrementTags($entry->getTags(), $entry->getPartnerId(), get_class($entry), $privacyContexts);
    		}
    	}
    	catch(Exception $e)
    	{
    		KalturaLog::err($e);
    	}
    }

	/* (non-PHPdoc)
     * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
     */
    public function shouldConsumeCreatedEvent (BaseObject $object)
    {
        if (defined("taggedObjectType::". strtoupper(get_class($object))))
        {
	        if (property_exists($object, self::TAGS_FIELD_NAME) && $object->getTags() != "")
	        {
	            return true;
	        }
        	
        } 
        
        if ($object instanceof categoryEntry)
        {
        	$entry = $this->getEntryByIdNoFilter($object->getEntryId());
        	if ($entry && $entry->getTags())
        		return true;
        }

        
        return false;
        
    }
	/* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectChanged()
     */
    public function objectChanged (BaseObject $object, array $modifiedColumns)
    {
    	$privacyContexts = null;
        if ($object instanceof entry)
        {
        	$criteria = new Criteria();
        	$criteria->add(categoryEntryPeer::ENTRY_ID,$object->getId());
        	$categoryEntries = categoryEntryPeer::doSelect($criteria);
        	
    		$privacyContexts = array(self::NULL_PC);
        	if (count($categoryEntries))
        	{
	        	foreach ($categoryEntries as $categoryEntry)
	        	{
	        		/* @var $categoryEntry categoryEntry */
	        		if ($categoryEntry->getPrivacyContext() != "")
	        		{
	        			$privacyContexts = array_merge($privacyContexts, self::trimObjectTags($categoryEntry->getPrivacyContext()));
	        		}
	        		else 
	        		{
	        			$privacyContexts[] = kEntitlementUtils::DEFAULT_CONTEXT; 
	        		}
	        	}
	        	$privacyContexts = array_unique($privacyContexts);
        	}
        }        
        $oldTags = $object->getColumnsOldValue(self::getClassConstValue(get_class($object->getPeer()), self::TAGS_FIELD_NAME));
        $newTags = $object->getTags();
        $tagsForDelete = implode(',', array_diff(explode(',', $oldTags), explode(',', $newTags)));
        $tagsForUpdate = implode(',', array_diff(explode(',', $newTags), explode(',', $oldTags)));
        
        if ($oldTags && $oldTags != "")
            self::decrementExistingTagsInstanceCount($tagsForDelete, $object->getPartnerId(), get_class($object), $privacyContexts);

        self::addOrIncrementTags($tagsForUpdate, $object->getPartnerId(), get_class($object), $privacyContexts);
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
        
        if ($object instanceof category)
        {
        	if (in_array(categoryPeer::PRIVACY_CONTEXTS, $modifiedColumns))
        	{
        		$currentPCs = self::trimObjectTags($object->getPrivacyContexts());
        		$oldPCs = self::trimObjectTags($object->getColumnsOldValue(categoryPeer::PRIVACY_CONTEXTS));
        		self::addReIndexTagsJob ($object->getId(), implode(',', array_diff($oldPCs, $currentPCs)), implode(',', array_diff($currentPCs, $oldPCs)), $object->getPartnerId());
        	}
        }
        
        return false;
        
    }

    /**
     * Function which checks the object tags agains DB
     * and returns the tags strings which are new and need to be saved. Existing tags instance count is incremented.
     * @param string $tagsForUpdate
     * @param int $partnerId
     * @param string $objectClass
     * @return array
     */
	public static function addOrIncrementTags ($tagsForUpdate, $partnerId, $objectClass, array $privacyContexts = null)
	{
	    $objectTags = self::trimObjectTags($tagsForUpdate);
	    if (!count($objectTags))
	    {
	        return;
	    }
	    
	    $c = self::getTagObjectsByTagStringsCriteria($objectTags, self::getObjectTypeByClassName($objectClass), $partnerId);
		if (!is_null($privacyContexts))
		{
			if (count($privacyContexts))
				$c->addAnd(TagPeer::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
			}
		else
		{
			$c->addAnd(TagPeer::PRIVACY_CONTEXT, self::NULL_PC);
		}
		TagPeer::setUseCriteriaFilter(false);
	    $foundTagObjects = TagPeer::doSelect($c);
	    TagPeer::setUseCriteriaFilter(true);
	    
	    if (!$foundTagObjects || !count($foundTagObjects))
	    {
	        $tagsToAdd = array();
			foreach ($objectTags as $tag)
			{
				$tagsToAdd[$tag] = array();
				$tagsToAdd[$tag] = $privacyContexts ? $privacyContexts : array(self::NULL_PC);
			}
	        return self::addTags($tagsToAdd, self::getObjectTypeByClassName($objectClass), $partnerId);
	    	
	    }
	    
	    $foundTagsToPc = array();
		$tagsToAdd = array();
		
	    foreach ($foundTagObjects as $foundTag)
	    {
	    	/* @var $foundTag Tag */
	        $foundTag->incrementInstanceCount();
	        if (!isset($foundTagsToPc[$foundTag->getTag()]))
	        {
	        	$foundTagsToPc[$foundTag->getTag()] = array();
	        }	
	        $foundTagsToPc[$foundTag->getTag()][] = $foundTag->getPrivacyContext();
	      	
	    }
        foreach (array_diff($objectTags, array_keys($foundTagsToPc)) as $missingTag)
        {
        	$missingPrivacyContexts = $privacyContexts ? $privacyContexts : array(self::NULL_PC);
        	//If the tag itself is missing from the DB, we must add it with all the specified privacy contexts
        	$tagsToAdd[$missingTag] = $missingPrivacyContexts;
        }
        if (!is_null($privacyContexts) && count($privacyContexts))
        {
	        foreach ($foundTagsToPc as $tag=>$foundPrivacyContexts)
	        {
	        	$missingPrivacyContexts = array_diff($privacyContexts, $foundPrivacyContexts);
	        	if (!is_null($missingPrivacyContexts) && count($missingPrivacyContexts))
	        	{
	        		$tagsToAdd [$tag] = $missingPrivacyContexts;
	        	}
	        }
        }

	    return self::addTags($tagsToAdd, self::getObjectTypeByClassName($objectClass), $partnerId);

	}
	
	
	/**
	 * Decrements instance count of tags found on a deleted object
	 * @param string $tagsToCheck
	 * @param int $partnerId
	 * @param string $objectClass
	 * @param array $privacyContexts
	 */
	public static function decrementExistingTagsInstanceCount ($tagsToCheck, $partnerId, $objectClass, $privacyContexts = null)
	{
	    $objectTags = self::trimObjectTags($tagsToCheck);
	 	if (!count($objectTags))
	    {
	        return;
	    }
		$c = self::getTagObjectsByTagStringsCriteria($objectTags,  self::getObjectTypeByClassName($objectClass), $partnerId);
		if (!is_null($privacyContexts))
		{
			if (count($privacyContexts))
				$c->addAnd(TagPeer::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
		}
		else
		{
			$c->addAnd(TagPeer::PRIVACY_CONTEXT, self::NULL_PC);
		}
		$c->addGroupByColumn(TagPeer::PRIVACY_CONTEXT);

		TagPeer::setUseCriteriaFilter(false);
		$tagsToDecrement = TagPeer::doSelect($c);
		TagPeer::setUseCriteriaFilter(true);
		foreach ($tagsToDecrement as $tag)
		{
			/* @var $tag Tag */
			$tag->decrementInstanceCount();
		}
	
	}
	 
	/**
	 * Function creates new propel Tag objects and saves them.
	 * @param array $tagToAdd
	 */
	protected static function addTags ($tagsToAdd, $objectType, $partnerId)
	{
	   
	    foreach ($tagsToAdd as $tagToAdd => $privacyContexts)
	    {
	    	foreach ($privacyContexts as $privacyContext)
	    	{
		        if (strlen($tagToAdd) >= TagSearchPlugin::MIN_TAG_SEARCH_LENGTH)
		        {
	    	        $tag = new Tag();
	    	        $tag->setTag(trim($tagToAdd));
	    	        $tag->setObjectType($objectType);
	    	        $tag->setPartnerId($partnerId);
			        $tag->setPrivacyContext($privacyContext ? $privacyContext : self::NULL_PC);
	    	        $tag->save();
		        }
	    	}
	    }
	}
	
	/**
	 * Get class name and returns the class's enum
	 * @param string $className
	 * @return int
	 */
	protected static function getObjectTypeByClassName ($className)
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
	protected static function trimObjectTags ($tagsString)
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
        return array_unique($tagsToReturn);		
	}

	
	/**
	 * @param int $categoryId
	 * @param string $pcToDecrement
	 * @param string $pcToIncrement
	 * @return BatchJob
	 */
	protected static function addReIndexTagsJob ($categoryId, $pcToDecrement, $pcToIncrement, $partnerId = null)
	{
		$jobType = TagSearchPlugin::getBatchJobTypeCoreValue(IndexTagsByPrivacyContextJobType::INDEX_TAGS);
		$data = new kIndexTagsByPrivacyContextJobData();
		$data->setChangedCategoryId($categoryId);
		$data->setDeletedPrivacyContexts($pcToDecrement);
		$data->setAddedPrivacyContexts($pcToIncrement);
		
		$batchJob = new BatchJob();
		$batchJob->setObjectId($categoryId);
		$batchJob->setObjectType(BatchJobObjectType::CATEGORY);
		if (!$partnerId)
			$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$batchJob->setPartnerId($partnerId);
		KalturaLog::log("Creating tag re-index job for categoryId [" . $data->getChangedCategoryId() . "] ");
		return kJobsManager::addJob($batchJob, $data, $jobType);
	}
	
	private function getEntryByIdNoFilter($entryId)
	{
		entryPeer::setUseCriteriaFilter(false);
    	$entry = entryPeer::retrieveByPK($entryId);
		entryPeer::setUseCriteriaFilter(true);
		
		return $entry;
	}
}