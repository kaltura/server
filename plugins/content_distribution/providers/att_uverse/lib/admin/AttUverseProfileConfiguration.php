<?php 
/**
 * @package plugins.attUverseDistribution
 * @subpackage admin
 */
class Form_AttUverseProfileConfiguration extends Form_ConfigurableProfileConfiguration
{	
	
	public function init()
	{
		parent::init();
		$this->setDescription('AttUverse Distribution Profile');
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'attuverse-distribution.phtml',
			'placement' => 'APPEND'
		));
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = true)
	{
		// $include_empty_fields is defaulted to true instead of false, so we can empty fields
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	
	protected function addProviderElements()
	{		
		$this->setDescription('');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));	
		
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('At&t Uverse Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'channel_title', array(
			'label'			=> 'Channel Title:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'ftp_host', array(
			'label'			=> 'FTP Host:',
			'filters'		=> array('StringTrim'),
		));
				
		$this->addElement('text', 'ftp_username', array(
			'label'			=> 'FTP user name:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'ftp_password', array(
			'label'			=> 'FTP password:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'ftp_path', array(
			'label'			=> 'FTP Path:',
			'filters'		=> array('StringTrim'),
		));
	
		// Feed URL
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('feed_url'), 
			'feed_url_group', 
			array('legend' => '', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addMetadataForm();
	}
	
	protected function addMetadataForm() 
	{
		// flavor asset file names
		$this->addElement('checkbox', 'enable_flavor_asset_filename', array(
			'label'			=> 'Custom Flavor Asset Filename:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_flavor_asset_filename')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'flavor_asset_filename_xslt', array(
			'label'			=> 'Flavor Asset Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('flavor_asset_filename_xslt')->removeDecorator('Label');
		
		// thumbnail asset file names
		$this->addElement('checkbox', 'enable_thumbnail_asset_filename', array(
			'label'			=> 'Custom Thumbnail Asset Filename',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_thumbnail_asset_filename')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'thumbnail_asset_filename_xslt', array(
			'label'			=> 'Thumbnail Asset Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('thumbnail_asset_filename_xslt')->removeDecorator('Label');

		// asset file names
		$this->addElement('checkbox', 'enable_asset_filename', array(
			'label'			=> 'Custom Asset Filename',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_asset_filename')->getDecorator('Label')->setOption('placement', 'APPEND');

		$this->addElement('textarea', 'asset_filename_xslt', array(
			'label'			=> 'Asset Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('asset_filename_xslt')->removeDecorator('Label');
		
		$this->addDisplayGroup(
			array( 'enable_flavor_asset_filename', 'flavor_asset_filename_xslt', 'enable_thumbnail_asset_filename', 'thumbnail_asset_filename_xslt', 'enable_asset_filename', 'asset_filename_xslt'),
			'file_names', 
			array('legend' => 'File Names', 'decorators' => array('FormElements', 'Fieldset'))
		);
	
	}
	
	public function renderFeedUrl($content)
	{
		$url = $this->getValue('feed_url');
		if (!$url)
			return 'Feed URL will be generated once the feed is saved';
		else
			return '<a href="'.$url.'" target="_blank">Feed URL</a>';
	}
}