<?php 
class Form_Batch_SearchEntry extends Form_Base
{
    public function init()
    {
    	$this->setAttrib('class', 'simple');
		$this->setTemplatePath('forms/investigate.phtml');
		
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('id', 'frmSearch');

        $this->addElement('select', 'searchType', array(
			'required' 		=> true,
			'multiOptions' 	=> array( 
				'by-entry-id' => 'By Entry ID',
				'by-flavor-asset-id' => 'By Flavor Asset ID',
			)
		));
		
		// Add an entryId element
        $this->addElement('text', 'entryId', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(),
        ));
        
        if(Kaltura_Support::isAdminEnabled())
	        $this->addElement('file', 'entryFile');
        
        // Add the search button
        $this->addElement('button', 'search', array(
            'type' => 'submit',
            'ignore'   => true,
            'label'    => 'entry-history search button',
        ));
        
        $this->addElement('hidden', 'submitAction');
        $this->addElement('hidden', 'partnerId');
        $this->addElement('hidden', 'actionFlavorAssetId');
        $this->addElement('hidden', 'actionJobId');
        $this->addElement('hidden', 'actionJobType');
    }
}