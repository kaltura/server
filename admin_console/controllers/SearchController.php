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

		$this->view->possibleValues = array_keys(array_diff_key($options, $selected));
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
			'English' => 'english',
			'Arabic' => 'arabic',
			'Basque' => 'basque',
			'Brazilian' => 'brazilian',
			'Bulgarian' => 'bulgarian',
			'Catalan' => 'catalan',
			'Chinese' => 'cjk',
			'Korean' => 'cjk',
			'Japanese' => 'cjk',
			'Czech' => 'czech',
			'Danish' => 'danish',
			'Dutch' => 'dutch',
			'Finnish' => 'finnish',
			'French' => 'french',
			'Galician' => 'galician',
			'German' => 'german',
			'Greek' => 'greek',
			'Hindi' => 'hindi',
			'Hungarian' => 'hungarian',
			'Indonesian' => 'indonesian',
			'Irish' => 'irish',
			'Italian' => 'italian',
			'Latvian' => 'latvian',
			'Lithuanian' => 'lithuanian',
			'Norwegian' => 'norwegian',
			'Persian' => 'persian',
			'Prtuguese' => 'portuguese',
			'Romanian' => 'romanian',
			'Russian' => 'russian',
			'Sorani' => 'sorani',
			'Spanish' => 'spanish',
			'Swedish' => 'swedish',
			'Turkish' => 'turkish',
			'Thai' => 'thai',
		);

		return $langMap;
	}
}
