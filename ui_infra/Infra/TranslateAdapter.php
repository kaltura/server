<?php

/**
 * @package UI-infra
 * @subpackage Translate
 */
class Infra_TranslateAdapter extends Zend_Translate_Adapter_Array
{
    protected function _loadTranslationData($data, $locale, array $options = array())
    {
        $translate = parent::_loadTranslationData($data, $locale, $options);
        
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationTranslations');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaApplicationTranslations */
			KalturaLog::debug("Loading plugin[" . $pluginInstance->getPluginName() . "]");
			$translations =  $pluginInstance->getTranslations($locale);
			$translate = array_merge($translate, $translations);
		}
		
		return $translate;
    }
}
