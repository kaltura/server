<?php

require_once 'propel/engine/builder/om/php5/PHP5ObjectBuilder.php';

/**
 * Generates a PHP5 base Object class for user object model (OM).
 *
 * This class produces the base object class (e.g. BaseMyTable) which contains all
 * the custom-built accessor and setter methods.
 *
 * @package    infra.propel.php5
 */
class KalturaObjectBuilder extends PHP5ObjectBuilder 
{

	/**
	 * Adds the methods related to refreshing, saving and deleting the object.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addManipulationMethods(&$script)
	{
		parent::addManipulationMethods($script);
		
		$this->addSaveHooks($script);
	}
	
	/**
	 * Adds the save hook methods.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addSaveHooks(&$script)
	{
		$table = $this->getTable();
		$createdAtColumn = $table->getColumn('created_at');
		$updatedAtColumn = $table->getColumn('updated_at');
		
		$script .= "
	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO \$con
	 * @return boolean
	 */
	public function preInsert(PropelPDO \$con = null)
	{";
		
		if($createdAtColumn)
		$script .= "
    	\$this->setCreatedAt(time());
    	";
		
		if($updatedAtColumn)
		$script .= "
		\$this->setUpdatedAt(time());";
		
		$script .= "
		return true;
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO \$con 
	 */
	public function postInsert(PropelPDO \$con = null)
	{
		" . $this->getPeerClassname() . "::setUseCriteriaFilter(false);
		\$this->reload();
		" . $this->getPeerClassname() . "::setUseCriteriaFilter(true);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent(\$this));
		
		if(\$this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent(\$this->copiedFrom, \$this));
	}

	/**
	 * Code to be run before updating the object in database
	 * @param PropelPDO \$con
	 * @return boolean
	 */
	public function preUpdate(PropelPDO \$con = null)
	{
		if(\$this->isModified())
		{";
		
		if($updatedAtColumn)
		$script .= "
			\$this->setUpdatedAt(time());";
		
		$script .= "
			kEventsManager::raiseEvent(new kObjectChangedEvent(\$this, \$this->modifiedColumns));
		}
		return true;
	}
	";
		
	}
	
	/**
	 * Adds the copy() method, which (in complex OM) includes the $deepCopy param for making copies of related objects.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addCopy(&$script)
	{
		$this->addCopyInto($script);

		$table = $this->getTable();

		$script .= "
	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean \$deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     ".$this->getObjectClassname()." Clone of current object.
	 * @throws     PropelException
	 */
	public function copy(\$deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		\$clazz = get_class(\$this);
		" . $this->buildObjectInstanceCreationCode('$copyObj', '$clazz') . "
		\$this->copyInto(\$copyObj, \$deepCopy);
		\$copyObj->setCopiedFrom(\$this);
		return \$copyObj;
	}
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @var     ".$this->getObjectClassname()." Clone of current object.
	 */
	protected \$copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      ".$this->getObjectClassname()." \$copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(".$this->getObjectClassname()." \$copiedFrom)
	{
		\$this->copiedFrom = \$copiedFrom;
	}
";
	} // addCopy()
	
	/**
	 * Specifies the methods that are added as part of the basic OM class.
	 * This can be overridden by subclasses that wish to add more methods.
	 * @see        ObjectBuilder::addClassBody()
	 */
	protected function addClassBody(&$script)
	{
		parent::addClassBody($script);
		
		$table = $this->getTable();
		$customDataColumn = $table->getColumn('custom_data');
		if($customDataColumn)
			$this->addCustomDataMethods($script);
	}
	
	
	/**
	 * Adds all custom data required methods
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addCustomDataMethods(&$script)
	{
		$table = $this->getTable();

		$script .= "
/* ---------------------- CustomData functions ------------------------- */
	private \$m_custom_data = null;
	
	public function putInCustomData ( \$name , \$value , \$namespace = null )
	{
		\$customData = \$this->getCustomDataObj( );
		\$customData->put ( \$name , \$value , \$namespace );
	}

	public function getFromCustomData ( \$name , \$namespace = null , \$defaultValue = null )
	{
		\$customData = \$this->getCustomDataObj( );
		\$res = \$customData->get ( \$name , \$namespace );
		if ( \$res === null ) return \$defaultValue;
		return \$res;
	}

	public function removeFromCustomData ( \$name , \$namespace = null)
	{

		\$customData = \$this->getCustomDataObj( );
		return \$customData->remove ( \$name , \$namespace );
	}

	public function incInCustomData ( \$name , \$delta = 1, \$namespace = null)
	{
		\$customData = \$this->getCustomDataObj( );
		return \$customData->inc ( \$name , \$delta , \$namespace  );
	}

	public function decInCustomData ( \$name , \$delta = 1, \$namespace = null)
	{
		\$customData = \$this->getCustomDataObj(  );
		return \$customData->dec ( \$name , \$delta , \$namespace );
	}

	public function getCustomDataObj( )
	{
		if ( ! \$this->m_custom_data )
		{
			\$this->m_custom_data = myCustomData::fromString ( \$this->getCustomData() );
		}
		return \$this->m_custom_data;
	}
	
	public function setCustomDataObj()
	{
		if ( \$this->m_custom_data != null )
		{
			\$this->setCustomData( \$this->m_custom_data->toString() );
		}
	}
/* ---------------------- CustomData functions ------------------------- */
	";
		
	} // addCustomDataMethods()
	
}
