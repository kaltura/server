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
        if(is_null($oldTags)) {
		    $oldTags = '';
        }
        
        $newTags = $object->getTags();
        if(is_null($newTags)) {
		    $newTags = '';
        }
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
     */
    public static function addOrIncrementTags ($tagsForUpdate, $partnerId, $objectClass, $privacyContexts = array(self::NULL_PC))
    {
	    $objectTags = self::trimObjectTags($tagsForUpdate);
	    if (!count($objectTags))
	    {
	    	return;
	    }
	    $foundTagObjects = self::getFoundTags($objectTags, $partnerId, $objectClass, $privacyContexts);
	    $tagsToAddList = self::getTagsToAdd($foundTagObjects, $objectTags, $privacyContexts);
	    if (count($tagsToAddList))
	    {
		    self::addTags($tagsToAddList, self::getObjectTypeByClassName($objectClass), $partnerId);
	    }
	    if (count($foundTagObjects))
	    {
		    self::updateTagsInstanceCount($foundTagObjects, true);
	    }
    }

    /**
     * @param array $objectTags
     * @param int $partnerId
     * @param string $objectClass
     * @param array $privacyContexts
     * @return array
     */
    private static function getFoundTags(array $objectTags, $partnerId, $objectClass, $privacyContexts = array())
    {
        $c = self::getTagObjectsByTagStringsCriteria($objectTags, self::getObjectTypeByClassName($objectClass), $partnerId);
        if (!is_null($privacyContexts) && count($privacyContexts))
        {
            $c->addAnd(TagPeer::PRIVACY_CONTEXT, $privacyContexts, Criteria::IN);
        }
        TagPeer::setUseCriteriaFilter(false);
        $foundTagObjects = TagPeer::doSelect($c);
        TagPeer::setUseCriteriaFilter(true);
        return $foundTagObjects;
    }

    /**
     * @param array $foundTagObjects
     * @param array $objectTags
     * @param array $privacyContexts
     * @return array
     */
    private static function getTagsToAdd (array $foundTagObjects , array $objectTags, $privacyContexts = array())
    {
    	//Any requested tag should be added with all privacy contexts as long as it does not exist (the tag with this privacy context).
	    $privacyContextByTag = array();
	    foreach ($foundTagObjects as $tag)
	    {
		    $privacyContextByTag[$tag->getTag()][] = $tag->getPrivacyContext();
	    }
	    $tagsToAddList = array();
	    $privacyContexts = $privacyContexts ? $privacyContexts : array(self::NULL_PC);
	    foreach ($objectTags as $tag)
	    {
		    if (!isset($privacyContextByTag[$tag]))
	        {
		        $tagsToAddList[$tag] = $privacyContexts;
	        }
	        else
	        {
		        $privacyContextsDiff = array_diff($privacyContexts,$privacyContextByTag[$tag]);
		        if(count($privacyContextsDiff))
		        {
			        $tagsToAddList[$tag] = $privacyContextsDiff;
		        }
	        }
        }
        return $tagsToAddList;
    }


    private static function updateInstanceCountList(array $foundTagObjects, $time, $toIncrement)
    {
        $foundTagIds = array();
        try
        {
            $connection = Propel::getConnection();
            foreach ($foundTagObjects as $tag)
            {
	            $foundTagIds[]=$tag->getId();
            }
            $idsString= "(".implode( ",",$foundTagIds).")";
            if($toIncrement==true)
            {
                $updateSql = "update tag set instance_count=instance_count+1 , updated_at = '".$time."' where id in ".$idsString.";";
            }
            else
            {
                $updateSql = "update tag set instance_count=instance_count-1 , updated_at = '".$time."' where id in ".$idsString."and instance_count>0;";
            }
            $stmt = $connection->prepare($updateSql);
            $stmt->execute();
        }
        catch (PropelException $e)
        {
            KalturaLog::err($e);
        }
    }


    /**
     * @param array $foundTagObjects
     */
    private static function updateTagsInstanceCount(array $foundTagObjects,$toIncrement)
    {
        $date_time = new DateTime();
        $time = $date_time->format('Y-m-d H:i:s');
        self::updateInstanceCountList($foundTagObjects,$time,$toIncrement);
        foreach ($foundTagObjects as $foundTag)
        {
            if($toIncrement)
            {
                $foundTag->setInstanceCount($foundTag->getInstanceCount()+1);
            }
            else
            {
                $foundTag->setInstanceCount(max($foundTag->getInstanceCount()-1,0));
            }
            $foundTag->setUpdatedAt($time);
            $foundTag->indexToSearchIndex();
        }
    }

    /**
     * Decrements instance count of tags found on a deleted object
     * @param string $tagsToCheck
     * @param int $partnerId
     * @param string $objectClass
     * @param array $privacyContexts
     */
	public static function decrementExistingTagsInstanceCount ($tagsToCheck, $partnerId, $objectClass, $privacyContexts = array(self::NULL_PC))
	{
		$objectTags = self::trimObjectTags($tagsToCheck);
		if (!count($objectTags))
		{
			return;
		}
		$tagsToDecrement = self::getFoundTags($objectTags, $partnerId, $objectClass, $privacyContexts);
		TagPeer::setUseCriteriaFilter(true);
		if(count($tagsToDecrement))
		{
			self:: updateTagsInstanceCount($tagsToDecrement,false);
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
	    $constant = "$className::" . strtoupper($constString);
	    return defined($constant) ? constant($constant) : null;
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
			if ($tag)
			{
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
