<?php

class DistributionFieldConfig
{
    /**
     * A value taken from a connector field enum which associates the current configuration to that connector field
     * @var string
     */
    private $fieldName;
    
    /**
     * A string that will be shown to the user as the field’s name in error messages related to the current field
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
     * @var bool
     */
    private $isRequired;
    
    /**
     * Trigger distribution update when this field changes or not ?
     * @var bool
     */
    private $updateOnChange;
    
    /**
     * Entry column or metadata xpath that should trigger an update
     * TODO: find a better solution for this
     * @var string
     */
    private $updateParam;
    
    /**
     * Is this field config is the default for the distribution provider?
     * @var bool
     */
    private $isDefault;
    
    
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
	 * @param bool $isRequired
	 */
	public function setIsRequired($isRequired) {
		$this->isRequired = $isRequired;
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
     * @return the $updateParam
     */
    public function getUpdateParam ()
    {
        return $this->updateParam;
    }

	/**
     * @param string $updateParam
     */
    public function setUpdateParam ($updateParam)
    {
        $this->updateParam = $updateParam;
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
	
	public function __sleep()
	{
		$vars = get_class_vars(get_class($this));
		unset($vars['isDefault']); // isDefault should not be serialized
		return array_keys($vars);
	}
}