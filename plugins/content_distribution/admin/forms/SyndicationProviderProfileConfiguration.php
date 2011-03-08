<?php 
class Form_SyndicationProviderProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		$upload = new Zend_File_Transfer_Adapter_Http();
		$files = $upload->getFileInfo();
		if(count($files) && isset($files["xslfile"]) && $files["xslfile"]['size'])
			$object->xsl = file_get_contents($files["xslfile"]['tmp_name']);
			
		return $object;
	}
	
	public function resetUnUpdatebleAttributes(KalturaDistributionProfile $distributionProfile)
	{
		parent::resetUnUpdatebleAttributes($distributionProfile);
		
		// reset readonly attributes
		$distributionProfile->feedId = null;
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Syndication Provider Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'feed_id', array(
			'label'	  =>  'Feed ID',
			'readonly'		=> true,
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt')))
		));
		
		$this->addElement('hidden', 'xsl', array(
			'class' => 'xsl-data file-data',
		));
		
		$this->addElement('file', 'xsl-file', array(
			'label'	  =>  'MRSS Transformer (XSL)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend', 'class' => 'xsl-file-label')), array('HtmlTag',  array('tag' => 'dt')))
		));
	}
}
