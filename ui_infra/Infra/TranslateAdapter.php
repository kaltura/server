<?php

/**
 * @package UI-infra
 * @subpackage Translate
 */
class Infra_TranslateAdapter extends Zend_Translate_Adapter_Array
{
    public function __construct($data, $locale = null, array $options = array())
    {
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationTranslations');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaApplicationTranslations */
			KalturaLog::debug("Loading plugin[" . $pluginInstance->getPluginName() . "]");
			$translations =  $pluginInstance->getTranslations($locale);
			$data = array_merge($data, $translations);
		}
		
        parent::__construct($data, $locale, $options);
    }
}
