<?php 
class Form_Batch_SearchEntries extends Form_Base
{
    public function getFilter(array $properties)
    {
    	$filter = $this->getObject('Kaltura_Client_Type_MediaEntryFilter', $properties);
    	return $filter;
    }
    
    /**
     * @param string $name name of the fields
     * @param string $enum class name
     * @param mixed $selected 'all' | array
     */
    protected function addEmumElemets($name, $enum, $selected = null)
    {
        $this->addElement('checkbox', $name, array(
            'required'   => false,
            'value' => ($selected == 'all'),
        ));
        
    	$reflect = new ReflectionClass($enum);
    	foreach($reflect->getConstants() as $const)
    	{
	        $this->addElement('checkbox', "{$name}_{$const}", array(
	            'required'   => false,
	            'value' => (is_array($selected) && in_array($const, $selected)),
	        ));
    	}   
    }
    
    public function init()
    {
    	$this->setAttrib('class', 'simple');
		$this->setTemplatePath('forms/gallery.phtml');
		
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('id', 'frmSearch');

        $this->addElement('text', 'partnerIdEqual', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(),
        ));
        
        $this->addElement('text', 'idIn', array(
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(),
        ));
        
		$this->addEmumElemets('mediaTypeIn', 'Kaltura_Client_Enum_MediaType', 'all');
		$this->addEmumElemets('statusIn', 'Kaltura_Client_Enum_EntryStatus', array(Kaltura_Client_Enum_EntryStatus::READY));
		$this->addEmumElemets('moderationStatusIn', 'Kaltura_Client_Enum_EntryModerationStatus', 'all');
	    
        // Add the search button
        $this->addElement('button', 'search', array(
            'type' => 'submit',
            'ignore'   => true,
            'label'    => 'gallery search button',
        ));
    }
}