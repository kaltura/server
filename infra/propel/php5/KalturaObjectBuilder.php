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
	const KALTURA_COLUMN_CREATED_AT = 'created_at';
	const KALTURA_COLUMN_UPDATED_AT = 'updated_at';
	const KALTURA_COLUMN_CUSTOM_DATA = 'custom_data';
	
	protected static $systemColumns = array(
		self::KALTURA_COLUMN_CREATED_AT,
		self::KALTURA_COLUMN_UPDATED_AT,
		self::KALTURA_COLUMN_CUSTOM_DATA,
	);
	
	/**
	 * Adds class attributes.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addAttributes(&$script)
	{
		parent::addAttributes($script);
		
		$this->addTraceAttributes($script);
	}

	/**
	 * Adds the $alreadyInValidation attribute, which prevents attempting to re-validate the same object.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addTraceAttributes(&$script)
	{
		$script .= "
	/**
	 * Store columns old values before the changes
	 * @var        array
	 */
	protected \$oldColumnsValues = array();
	
	/**
	 * @return array
	 */
	public function getColumnsOldValues()
	{
		return \$this->oldColumnsValues;
	}
";
	}
	
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
		$createdAtColumn = $table->getColumn(self::KALTURA_COLUMN_CREATED_AT);
		$updatedAtColumn = $table->getColumn(self::KALTURA_COLUMN_UPDATED_AT);
		$customDataColumn = $table->getColumn(self::KALTURA_COLUMN_CUSTOM_DATA);
		
		$script .= "
	/**
	 * Code to be run before persisting the object
	 * @param PropelPDO \$con
	 * @return bloolean
	 */
	public function preSave(PropelPDO \$con = null)
	{";
		
		if($customDataColumn)
		$script .= "
		\$this->setCustomDataObj();
    	";
		
		$script .= "
		return parent::preSave(\$con);
	}

	/**
	 * Code to be run after persisting the object
	 * @param PropelPDO \$con
	 */
	public function postSave(PropelPDO \$con = null) 
	{
		\$this->oldColumnsValues = array();";
		
		if($customDataColumn)
		$script .= "
		\$this->oldCustomDataValues = array();
    	";
		
		$script .= " 
	}
	
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
		$customDataColumn = $table->getColumn(self::KALTURA_COLUMN_CUSTOM_DATA);
		if($customDataColumn)
			$this->addCustomDataMethods($script);
	}

	/**
	 * Adds the mutator open body part
	 * @param      string &$script The script will be modified in this method.
	 * @param      Column $col The current column.
	 * @see        addMutatorOpen()
	 **/
	protected function addMutatorOpenBody(&$script, Column $col) 
	{
		parent::addMutatorOpenBody($script, $col);
		
		$clo = strtolower($col->getName());
		if(in_array($clo, self::$systemColumns))
			return;
			
		$fullColumnName = $this->getColumnConstant($col);
		$cfc = $col->getPhpName();
		$script .= "
		if(!isset(\$this->oldColumnsValues[$fullColumnName]))
			\$this->oldColumnsValues[$fullColumnName] = \$this->get$cfc();
";
	}
	

	/**
	 * Adds a setter method for date/time/timestamp columns.
	 * @param      string &$script The script will be modified in this method.
	 * @param      Column $col The current column.
	 * @see        parent::addColumnMutators()
	 */
	protected function addTemporalMutator(&$script, Column $col)
	{
		$cfc = $col->getPhpName();
		$clo = strtolower($col->getName());
		$visibility = $col->getMutatorVisibility();

		$dateTimeClass = $this->getBuildProperty('dateTimeClass');
		if (!$dateTimeClass) {
			$dateTimeClass = 'DateTime';
		}

		$script .= "
	/**
	 * Sets the value of [$clo] column to a normalized version of the date/time value specified.
	 * ".$col->getDescription()."
	 * @param      mixed \$v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     ".$this->getObjectClassname()." The current object (for fluent API support)
	 */
	".$visibility." function set$cfc(\$v)
	{";
		
		$this->addMutatorOpenBody($script, $col);

		$fmt = var_export($this->getTemporalFormatter($col), true);

		$script .= "
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if (\$v === null || \$v === '') {
			\$dt = null;
		} elseif (\$v instanceof DateTime) {
			\$dt = \$v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric(\$v)) { // if it's a unix timestamp
					\$dt = new $dateTimeClass('@'.\$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					\$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					\$dt = new $dateTimeClass(\$v);
				}
			} catch (Exception \$x) {
				throw new PropelException('Error parsing date/time value: ' . var_export(\$v, true), \$x);
			}
		}

		if ( \$this->$clo !== null || \$dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			\$currNorm = (\$this->$clo !== null && \$tmpDt = new $dateTimeClass(\$this->$clo)) ? \$tmpDt->format($fmt) : null;
			\$newNorm = (\$dt !== null) ? \$dt->format($fmt) : null;

			if ( (\$currNorm !== \$newNorm) // normalized values don't match ";

		if (($def = $col->getDefaultValue()) !== null && !$def->isExpression()) {
			$defaultValue = $this->getDefaultValueString($col);
			$script .= "
					|| (\$dt->format($fmt) === $defaultValue) // or the entered value matches the default";
		}

		$script .= "
					)
			{
				\$this->$clo = (\$dt ? \$dt->format($fmt) : null);
				\$this->modifiedColumns[] = ".$this->getColumnConstant($col).";
			}
		} // if either are not null
";
		$this->addMutatorClose($script, $col);
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

	/**
	 * @var myCustomData
	 */
	protected \$m_custom_data = null;

	/**
	 * Store custom data old values before the changes
	 * @var        array
	 */
	protected \$oldCustomDataValues = array();
	
	/**
	 * @return array
	 */
	public function getCustomDataOldValues()
	{
		return \$this->oldCustomDataValues;
	}
	
	/**
	 * @param string \$name
	 * @param string \$value
	 * @param string \$namespace
	 * @return string
	 */
	public function putInCustomData ( \$name , \$value , \$namespace = null )
	{
		\$customData = \$this->getCustomDataObj( );
		
		\$currentNamespace = '';
		if(\$namespace)
			\$currentNamespace = \$namespace;
			
		if(!isset(\$this->oldCustomDataValues[\$currentNamespace]))
			\$this->oldCustomDataValues[\$currentNamespace] = array();
		if(!isset(\$this->oldCustomDataValues[\$currentNamespace][\$name]))
			\$this->oldCustomDataValues[\$currentNamespace][\$name] = \$customData->get(\$name, \$namespace);
		
		\$customData->put ( \$name , \$value , \$namespace );
	}

	/**
	 * @param string \$name
	 * @param string \$namespace
	 * @param string \$defaultValue
	 * @return string
	 */
	public function getFromCustomData ( \$name , \$namespace = null , \$defaultValue = null )
	{
		\$customData = \$this->getCustomDataObj( );
		\$res = \$customData->get ( \$name , \$namespace );
		if ( \$res === null ) return \$defaultValue;
		return \$res;
	}

	/**
	 * @param string \$name
	 * @param string \$namespace
	 */
	public function removeFromCustomData ( \$name , \$namespace = null)
	{

		\$customData = \$this->getCustomDataObj( );
		return \$customData->remove ( \$name , \$namespace );
	}

	/**
	 * @param string \$name
	 * @param int \$delta
	 * @param string \$namespace
	 * @return string
	 */
	public function incInCustomData ( \$name , \$delta = 1, \$namespace = null)
	{
		\$customData = \$this->getCustomDataObj( );
		return \$customData->inc ( \$name , \$delta , \$namespace  );
	}

	/**
	 * @param string \$name
	 * @param int \$delta
	 * @param string \$namespace
	 * @return string
	 */
	public function decInCustomData ( \$name , \$delta = 1, \$namespace = null)
	{
		\$customData = \$this->getCustomDataObj(  );
		return \$customData->dec ( \$name , \$delta , \$namespace );
	}

	/**
	 * @return myCustomData
	 */
	public function getCustomDataObj( )
	{
		if ( ! \$this->m_custom_data )
		{
			\$this->m_custom_data = myCustomData::fromString ( \$this->getCustomData() );
		}
		return \$this->m_custom_data;
	}
	
	/**
	 * Must be called before saving the object
	 */
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
