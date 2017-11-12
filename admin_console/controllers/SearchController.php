<?php

/**
 * @package Admin
 * @subpackage Serach
 */
class SearchController extends Zend_Controller_Action
{
	public function assignSearchLanguageAction()
	{
		$this->_helper->layout->disableLayout();
		$partnerId = $this->_getParam('partnerId');

		$client = Infra_ClientHelper::getClient();
		$options = $this->getSupportedESearchLanguages();
		$selected = $this->getESearchLanguages($client, $partnerId);

		$this->view->possibleValues = array_values(array_diff($options, $selected));
		$this->view->selectedValues = $selected;

	}
	
	protected function getESearchLanguages($client, $partnerId, $currentLanguages = null) {

		$partnerService = new Kaltura_Client_SystemPartner_SystemPartnerService($client);

		Infra_ClientHelper::impersonate($partnerId);
		$partnerConfiguration = $partnerService->getConfiguration($partnerId);
		$options = array();

		if(!$partnerConfiguration->eSearchLanguages)
			return $options;

		$options = json_decode($partnerConfiguration->eSearchLanguages,true);
		return $options;
	}

	protected function getSupportedESearchLanguages()
	{
		$langMap = array(
			'English',
			'Arabic',
			'Basque',
			'Brazilian',
			'Bulgarian',
			'Catalan',
			'Chinese',
			'Korean',
			'Japanese',
			'Czech',
			'Danish',
			'Dutch',
			'Finnish',
			'French',
			'Galician',
			'German',
			'Greek',
			'Hindi',
			'Hungarian',
			'Indonesian',
			'Irish',
			'Italian',
			'Latvian',
			'Lithuanian',
			'Norwegian',
			'Persian',
			'Prtuguese',
			'Romanian',
			'Russian',
			'Sorani',
			'Spanish',
			'Swedish',
			'Turkish',
			'Thai'
		);

		return $langMap;
	}
}
