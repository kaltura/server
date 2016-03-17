<?php
require_once KALTURA_ROOT_PATH.'/vendor/facebook-sdk-php-v5-customized/autoload.php';

/**
 * @package plugins.facebookDistribution
 * @subpackage admin
 */
class Form_FacebookProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
	}

	protected function addProviderElements()
	{
		$this->setDescription(null);

		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Facebook Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));

		$this->addElement('text', 'api_authorize_url', array(
			'label'			=> 'Authorize API Access:',
			'decorators' => array(
				array('ViewScript',
					array(
						'viewScript' => 'facebook-distribution-api-authorize-field.phtml',
			)))
		));

		// General
		$this->addElement('text', 'page_id', array(
			'label'			=> 'Facebook Page ID:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));


		$this->addDisplayGroup(
			array('page_id'),
			'general',
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);

	}

}