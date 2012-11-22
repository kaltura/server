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
     * @see IIndexable::getIndexFieldsMap()
     */
    public function getIndexFieldsMap ()
    {
        return array(
           'int_id' => 'intId',
           'tag' => 'tag',
           'partner_id' => 'partnerId',
           'object_type' => 'objectType',
           'created_at' => 'createdAt',
           'instance_count' => 'instanceCount',
       );
        
    }
    
    private static $indexFieldTypes = array(
        'int_id' => IIndexable::FIELD_TYPE_INTEGER,
        'tag' => IIndexable::FIELD_TYPE_STRING,
        'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
        'object_type' => IIndexable::FIELD_TYPE_INTEGER,
        'created_at' => IIndexable::FIELD_TYPE_DATETIME,
    	'instance_count' => IIndexable::FIELD_TYPE_INTEGER
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
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
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
	    $this->setInstanceCount($this->getInstanceCount() - 1);
	    $this->save();
	}
	
	/* (non-PHPdoc)
     * @see IIndexable::setUpdatedAt()
     */
    public function setUpdatedAt ($time)
    {
        // TODO Auto-generated method stub
        
    }
	
	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
    
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
	}

} // Tag
