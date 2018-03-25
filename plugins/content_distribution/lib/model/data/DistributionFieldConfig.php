<?php

/**
 * @package plugins.contentDistribution
 * @subpackage model.data
 */
class DistributionFieldConfig
{
    /**
     * A value taken from a connector field enum which associates the current configuration to that connector field
     * @var string
     */
    private $fieldName;
    
    /**
     * A string that will be shown to the user as the field's name in error messages related to the current field
     * @var string
     */
    private $userFriendlyFieldName;
    
    /**
     * 
     * An XSLT string that extracts the right value from the Kaltura entry MRSS XML.
     * The value of the current connector field will be the one that is returned from transforming the Kaltura entry MRSS XML using this XSLT string.
     * @var string
     */
    private $entryMrssXslt;
    
    /**
     * Is the field required to have a value for submission ?
     * @var DistributionFieldRequiredStatus
     */
    private $isRequired;

	/**
	 * @var string
	 */
	private $type;
    
    /**
     * Trigger distribution update when this field changes or not ?
     * @var bool
     */
    private $updateOnChange;
    
    /**
     * Entry column or metadata xpath that should trigger an update
     * TODO: find a better solution for this
     * @var array of string
     */
    private $updateParams;
    
    /**
     * Is this field config is the default for the distribution provider?
     * @var bool
     */
    private $isDefault;
    
    /**
     * Flag indicating whether an error on this field should cause the distributed data to be deleted. 
     * @var bool
     */
    private $triggerDeleteOnError;
    
    
	/**
     * @return the $fieldName
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

	/**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

	/**
     * @return the $userFriendlyFieldName
     */
    public function getUserFriendlyFieldName()
    {
        return $this->userFriendlyFieldName;
    }

	/**
     * @param string $userFriendlyFieldName
     */
    public function setUserFriendlyFieldName($userFriendlyFieldName)
    {
        $this->userFriendlyFieldName = $userFriendlyFieldName;
    }

	/**
     * @return the $entryMrssXslt
     */
    public function getEntryMrssXslt()
    {
        return $this->entryMrssXslt;
    }

	/**
     * @param string $entryMrssXslt
     */
    public function setEntryMrssXslt($entryMrssXslt)
    {
        $this->entryMrssXslt = $entryMrssXslt;
    }
    
	/**
	 * @return the $isRequired
	 */
	public function getIsRequired() {
		return $this->isRequired;
	}

	/**
	 * @return string $type
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param DistributionFieldRequiredStatus $isRequired
	 */
	public function setIsRequired($isRequired) {
		$this->isRequired = $isRequired;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
     * @return the $updateOnChange
     */
    public function getUpdateOnChange ()
    {
        return $this->updateOnChange;
    }

	/**
     * @param bool $updateOnChange
     */
    public function setUpdateOnChange ($updateOnChange)
    {
        $this->updateOnChange = $updateOnChange;
    }

	/**
     * @return the $updateParams
     */
    public function getUpdateParams ()
    {
        return $this->updateParams;
    }

	/**
     * @param string $updateParams
     */
    public function setUpdateParams ($updateParams)
    {
    	if (!is_array($updateParams)) {
    		$updateParams = array($updateParams);
    	}
        $this->updateParams = $updateParams;
    }
    
	/**
	 * @return the $isDefault
	 */
	public function getIsDefault() 
	{
		return $this->isDefault;
	}

	/**
	 * @param bool $isDefault
	 */
	public function setIsDefault($isDefault) 
	{
		$this->isDefault = $isDefault;
	}
	
	/**
	 * @return the $triggerDeleteOnError
	 */
	public function getTriggerDeleteOnError() {
		return $this->triggerDeleteOnError;
	}

	/**
	 * @param bool $triggerDeleteOnError
	 */
	public function setTriggerDeleteOnError($triggerDeleteOnError) {
		$this->triggerDeleteOnError = $triggerDeleteOnError;
	}

	public function __sleep()
	{
		$vars = get_class_vars(get_class($this));
		unset($vars['isDefault']); // isDefault should not be serialized
		return array_keys($vars);
	}
}