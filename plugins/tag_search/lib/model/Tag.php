<?php


/**
 * Skeleton subclass for representing a row from the 'tag' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.tagSearch
 * @subpackage model
 */
class Tag extends BaseTag implements IIndexable
{
	const PRIVACY_CONTEXT_INDEX_PREFIX = "pc";
	
	const OBJECT_TYPE_INDEX_PREFIX = "ot";
	
	/* (non-PHPdoc)
     * @see IIndexable::getIntId()
     */
    public function getIntId () 
    {
        return $this->id;
        
    }

	/* (non-PHPdoc)
     * @see IIndexable::getEntryId()
     */
    public function getEntryId ()
    {
        // We have no interest in returning entryId for the tag.
        return null;
        
    }

	/* (non-PHPdoc)
     * @see IIndexable::getObjectIndexName()
     */
    public function getObjectIndexName ()
    {
        return TagSearchPlugin::INDEX_NAME;
        
    }
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexNullableFields()
	 */
	public static function getIndexNullableFields()
	{
		return array();
	}

	/* (non-PHPdoc)
     * @see IIndexable::getIndexFieldsMap()
     */
    public function getIndexFieldsMap ()
    {
        return array(
           'int_id' => 'intId',
           'tag' => 'tag',
           'partner_id' => 'partnerId',
           'object_type' => 'indexObjectType',
           'created_at' => 'createdAt',
           'instance_count' => 'instanceCount',
           'privacy_context' => 'indexPrivacyContext',
       );
        
    }
    
    private static $indexFieldTypes = array(
        'int_id' => IIndexable::FIELD_TYPE_INTEGER,
        'tag' => IIndexable::FIELD_TYPE_STRING,
        'partner_id' => IIndexable::FIELD_TYPE_STRING,
        'object_type' => IIndexable::FIELD_TYPE_STRING,
        'created_at' => IIndexable::FIELD_TYPE_DATETIME,
    	'instance_count' => IIndexable::FIELD_TYPE_INTEGER,
    	'privacy_context' => IIndexable::FIELD_TYPE_STRING,
	);

	
	/* (non-PHPdoc)
     * @see IIndexable::getIndexFieldType()
     */
    public function getIndexFieldType ($field)
    {
        if(isset(self::$indexFieldTypes[$field]))
			return self::$indexFieldTypes[$field];
			
		return null;
    }
    
	/* (non-PHPdoc)
	 * @see lib/model/om/Baseentry#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
	
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectReadyForIndexEvent($this));
	}
	
	/* (non-PHPdoc)
	 * @see BasecategoryKuser::postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate($con);
		
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
	}
	
	/**
	 * Function to increase instance count by 1.
	 */
	public function incrementInstanceCount ()
	{
	    $this->setInstanceCount($this->getInstanceCount() + 1);
	    $this->save();
	}
    
	/**
	 * Function to decrease instance count by 1.
	 */
	public function decrementInstanceCount ()
	{
	    $this->setInstanceCount(max(array (intval($this->getInstanceCount() - 1), 0)));
	    $this->save();
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
    
	/* (non-PHPdoc)
	 * @see IIndexable::getSearchIndexFieldsEscapeType()
	 */
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}

	public function getIndexPrivacyContext ()
	{
		return $this->getPartnerId() . self::PRIVACY_CONTEXT_INDEX_PREFIX . $this->getPrivacyContext();
	}
	
	public function getIndexObjectType ()
	{
		return $this->getPartnerId() . self::OBJECT_TYPE_INDEX_PREFIX. $this->getObjectType();
	}
	
	public static function getIndexedFieldValue ($fieldName, $fieldValue, $partnerId)
	{
		$prefix = null;
		if ($fieldName == "TagPeer::PRIVACY_CONTEXT")
		{
			$prefix = self::PRIVACY_CONTEXT_INDEX_PREFIX;
		}
		else if ($fieldName == "TagPeer::OBJECT_TYPE")
		{
			$prefix = self::OBJECT_TYPE_INDEX_PREFIX;
		}
		if (!$prefix)
			return $fieldValue;
			
		if (is_scalar($fieldValue))
			return $partnerId . $prefix . $fieldValue;
			
		if (is_array($fieldValue))
		{
			$indexedFieldValue = array();
			foreach ($fieldValue as &$singleFieldValue)
			{
				$indexedFieldValue[] = $partnerId. $prefix . $singleFieldValue;
			}
			
			return $indexedFieldValue;
		}
	}
	
} // Tag
