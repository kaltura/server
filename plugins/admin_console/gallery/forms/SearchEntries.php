<?php
/**
 * @package plugins.adminConsoleGallery
 * @subpackage admin
 */ 
class Form_Batch_SearchEntries extends Form_Base
{
	private $enumFields = array();
	
    public static function getFieldId($id)
    {
    	return str_replace(array('-', '.'), array('_', '_'), $id);
    }
    
    public function getFilter(array $properties)
    {
    	if(!isset($properties['partnerId']) || !strlen($properties['partnerId']))
    		return null;
    		
    	$filter = $this->getObject('Kaltura_Client_Type_MediaEntryFilter', $properties);
    	
    	foreach($this->enumFields as $field => $enumClass)
    	{
			$parts = explode('_', strtolower($field));
			$prop = '';
			foreach ($parts as $part) 
				$prop .= ucfirst(trim($part));
			$prop[0] = strtolower($prop[0]);
			
    		$reflect = new ReflectionClass($enumClass);
    		$values = array();
    		foreach($reflect->getConstants() as $const)
				if($properties[$field] || $properties[Form_Batch_SearchEntries::getFieldId("{$field}_{$const}")])
    				$values[] = $const;
					
    		if(count($values))
    			$filter->$prop = implode(',', $values);
    		else
    			$filter->$prop = null;
    	}
    	return $filter;
    }
    
    /**
     * @param string $name name of the fields
     * @param string $enum class name
     * @param mixed $selected 'all' | array
     */
    protected function addEmumElemets($name, $enum, $selected = null)
    {
    	$this->enumFields[$name] = $enum;
    	
        $this->addElement('checkbox', $name, array(
            'required'   => false,
            'value' => ($selected == 'all'),
        	'onclick' => "toogleAll('$name')",
        ));
        
    	$reflect = new ReflectionClass($enum);
    	foreach($reflect->getConstants() as $const)
    	{
	        $this->addElement('checkbox', Form_Batch_SearchEntries::getFieldId("{$name}_{$const}"), array(
	            'required'   => false,
	            'value' => (is_array($selected) && in_array($const, $selected)),
	        ));
    	}   
    }
    
    public function init()
    {
    	$this->setAttrib('class', 'simple');
		$this->setTemplatePath('forms/search-entries-form.phtml');
		
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('id', 'frmSearch');

        $this->addElement('text', 'partnerId', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(),
        ));
        
        $this->addElement('text', 'id_in', array(
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(),
        ));
        
		$this->addEmumElemets('media_type_in', 'Kaltura_Client_Enum_MediaType', 'all');
		$this->addEmumElemets('status_in', 'Kaltura_Client_Enum_EntryStatus', array(Kaltura_Client_Enum_EntryStatus::READY));
		$this->addEmumElemets('moderation_status_in', 'Kaltura_Client_Enum_EntryModerationStatus', 'all');
	    
        // Add the search button
        $this->addElement('button', 'search', array(
            'type' => 'submit',
            'ignore'   => true,
            'label'    => 'gallery search button',
        ));
        
        $this->addViewParam('enumFields', $this->enumFields);
    }
}