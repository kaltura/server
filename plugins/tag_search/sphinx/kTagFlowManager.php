<?php
class kTagFlowManager implements kObjectCreatedEventConsumer, kObjectDeletedEventConsumer, kObjectChangedEventConsumer
{
    const TAGS_FIELD_NAME = "tags";
    
    const PARTNER_ID_FIELD = "partner_id";
    
    public static $specialCharacters = array ('!', '*', '"');
    public static $specialCharactersReplacement = array ('\\!', '\\*', '\\"');
    
    const NULL_PC = "NO_PC";
    
	/* (non-PHPdoc)
     * @see kObjectDeletedEventConsumer::objectDeleted()
     */
    public function objectDeleted (BaseObject $object, BatchJob $raisedJob = null)
    {
    	if (! ($object instanceof categoryEntry))
    	{
        	$this->decrementExistingTagsInstanceCount($object);  
    	}
    	else 
    	{
    		$category = categoryPeer::retrieveByPK($object->getCategoryId());
    		$privacyContexts = $category->getPrivacyContexts() != "" ? explode(",", $category->getPrivacyContexts()) : array();
    		if (!count($privacyContexts))
    				$privacyContexts[] = kEntitlementUtils::DEFAULT_CONTEXT; 
    		$entry = entryPeer::retrieveByPK($object->getEntryId());
    		$this->decrementExistingTagsInstanceCount($entry, null, $privacyContexts);
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
        	$entry = entryPeer::retrieveByPK($object->getEntryId());
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
		        $this->addOrIncrementTags($object);
    		}
    		else
    		{
    			/* @var $object categoryEntry */
    			$category = categoryPeer::retrieveByPK($object->getCategoryId());
    			$privacyContexts = $category->getPrivacyContexts() != "" ? explode(",", $category->getPrivacyContexts()) : array();
    			if (!count($privacyContexts))
    				$privacyContexts[] = kEntitlementUtils::DEFAULT_CONTEXT; 
    			$this->addOrIncrementTags(entryPeer::retrieveByPK($object->getEntryId()),null, $privacyContexts);
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
        	$entry = entryPeer::retrieveByPK($object->getEntryId());
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
        if ($object instanceof entry)
        {
        	$criteria = new Criteria();
        	$criteria->add(categoryEntryPeer::ENTRY_ID,$object->getId());
        	$categoryEntries = categoryEntryPeer::doSelect($criteria);
        	if (count($categoryEntries))
        	{
        		$privacyContexts = array();
	        	foreach ($categoryEntries as $categoryEntry)
	        	{
	        		/* @var $categoryEntry categoryEntry */
	        		$category = categoryPeer::retrieveByPK($categoryEntry->getCategoryId());
	        		if ($category->getPrivacyContexts() != "")
	        		{
	        			$privacyContexts = array_merge($privacyContexts, explode(',', $category->getPrivacyContexts()));
	        		}
	        		else 
	        		{
	        			$privacyContexts[] = kEntitlementUtils::DEFAULT_CONTEXT; 
	        		}
	        	}
	        	$privacyContexts[] = self::NULL_PC;
	        	$privacyContexts = array_unique($privacyContexts);
        	}
        }
        
        if ($object instanceof category)
        {
        	if (in_array(categoryPeer::PRIVACY_CONTEXTS, $modifiedColumns))
        	{
        		
        	}
        }
        
        $oldTags = $object->getColumnsOldValue(self::getClassConstValue(get_class($object->getPeer()), self::TAGS_FIELD_NAME));
        $newTags = $object->getTags();
        $tagsForDelete = implode(',', array_diff(explode(',', $oldTags), explode(',', $newTags)));
        $tagsForUpdate = implode(',', array_diff(explode(',', $newTags), explode(',', $oldTags)));
        $privacyContexts = null;
        
        if ($oldTags && $oldTags != "")
            $this->decrementExistingTagsInstanceCount($object,$tagsForDelete,$privacyContexts);

        $this->addOrIncrementTags($object, $tagsForUpdate, $privacyContexts);
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
        		$criteria = new Criteria();
        		$criteria->add(categoryEntryPeer::CATEGORY_ID, $object->getId());
        		$categoryEntries = categoryEntryPeer::doSelect($criteria);
        		foreach ($categoryEntries as $categoryEntry)
        		{
        			/* @var $categoryEntry categoryEntry */
        			$entry = entryPeer::retrieveByPK($categoryEntry->getEntryId());
        			if ($entry->getTags())
        				return true;
        		}
        	}
        }
        
        return false;
        
    }

    /**
     * Function which checks the object tags agains DB
     * and returns the tags strings which are new and need to be saved. Existing tags instance count is incremented.
     * @param BaseObject $object
     * @return array
     */
	protected function addOrIncrementTags (BaseObject $object, $tagsForUpdate = null, array $privacyContexts = null)
	{
	    KalturaLog::info("In Object Added handler");
	    $objectTags = $tagsForUpdate ? $this->trimObjectTags($tagsForUpdate) : $this->trimObjectTags($object->getTags());
	    $objectTags = str_replace(self::$specialCharacters, self::$specialCharactersReplacement, $objectTags);
	    if (!count($objectTags))
	    {
	        return;
	    }
	    
	    $c = self::getTagObjectsByTagStringsCriteria($objectTags, $this->getObjectTypeByClassName(get_class($object)), $object->getPartnerId());
		if (is_null($privacyContexts))
		{
			if (count($privacyContexts))
				$c->addAnd(TagPeer::PRIVACY_CONTEXT, Tag::getIndexedFieldValue("TagPeer::PRIVACY_CONTEXT", $privacyContexts, $object->getPartnerId()), Criteria::IN);
			}
		else
		{
			$c->addAnd(TagPeer::PRIVACY_CONTEXT, Tag::getIndexedFieldValue("TagPeer::PRIVACY_CONTEXT", self::NULL_PC, $object->getPartnerId()));
		}
	    $c->applyFilters();
	    
	    $numTagsFound = $c->getRecordsCount(); 
	   
	    if (!$numTagsFound)
	    {
	         $tagsToAdd = array();
			foreach ($objectTags as $tag)
			{
				$tagsToAdd[$tag] = array();
				$tagsToAdd[$tag] = $privacyContexts ? $privacyContexts : array(self::NULL_PC);
			}
	        return $this->addTags($tagsToAdd, self::getObjectTypeByClassName(get_class($object)), $object->getPartnerId());
	    	
	    }
	    
	    KalturaLog::info("Found tags ids: ".print_r($c->getFetchedIds(), true));
	    
	    $crit = new Criteria();
	    $crit->addAnd(TagPeer::ID , $c->getFetchedIds(), KalturaCriteria::IN);
	    TagPeer::setUseCriteriaFilter(false);
	    $foundTagObjects = TagPeer::doSelect($crit);
	    TagPeer::setUseCriteriaFilter(true);
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

	    return $this->addTags($tagsToAdd, $this->getObjectTypeByClassName(get_class($object)), $object->getPartnerId());

	}
	
	
	/**
	 * Decrements instance count of tags found on a deleted object
	 * @param BaseObject $object
	 * @param string $tagsToCheck
	 * @param array $privacyContexts
	 */
	protected function decrementExistingTagsInstanceCount (BaseObject $object, $tagsToCheck = null, $privacyContexts = null)
	{
	    $objectTags = $tagsToCheck ? $this->trimObjectTags($tagsToCheck) : $this->trimObjectTags($object->getTags());
	    $objectTags = str_replace(self::$specialCharacters, self::$specialCharactersReplacement, $objectTags);
		$c = self::getTagObjectsByTagStringsCriteria($objectTags,  $this->getObjectTypeByClassName(get_class($object)), $object->getPartnerId());
		if (!is_null($privacyContexts))
		{
			if (count($privacyContexts))
				$c->addAnd(TagPeer::PRIVACY_CONTEXT, Tag::getIndexedFieldValue("TagPeer::PRIVACY_CONTEXT", $privacyContexts, $object->getPartnerId()), Criteria::IN);
		}
		else
		{
			$c->addAnd(TagPeer::PRIVACY_CONTEXT, Tag::getIndexedFieldValue("TagPeer::PRIVACY_CONTEXT", self::NULL_PC, $object->getPartnerId()));
		}
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
	protected function addTags ($tagsToAdd, $objectType, $partnerId)
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
	    	        $tag->setPrivacyContext($privacyContext);
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
	protected function getObjectTypeByClassName ($className)
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
	    $c->addAnd(TagPeer::OBJECT_TYPE, Tag::getIndexedFieldValue("TagPeer::OBJECT_TYPE", $objectType, $partnerId), KalturaCriteria::EQUAL);
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