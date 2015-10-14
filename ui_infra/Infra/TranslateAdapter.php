<?php

/**
 * @package UI-infra
 * @subpackage Translate
 */
class Infra_TranslateAdapter extends Zend_Translate_Adapter_Array
{
    public function __construct($data, $locale = null, array $options = array())
    {
        parent::__construct($data, $locale, $options);
        
        Infra_AuthHelper::registerNamespaceChangedCallback(array($this, 'reload'), 'Infra_TranslateAdapter::reload');
    }
	
	public function reload()
    {
    	$locale = $this->getLocale();
    	if($locale instanceof Zend_Locale)
    		$locale = "$locale";
    		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationTranslations');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaApplicationTranslations */
			$translations =  $pluginInstance->getTranslations($locale);
			if(isset($translations[$locale]) && is_array($translations[$locale]))
			{
				foreach($translations[$locale] as $key => $value)
					$this->_translate[$locale][$key] = $value;
			}
		}
    }
	
	protected function _loadTranslationData($data, $locale, array $options = array())
    {
        $translate = parent::_loadTranslationData($data, $locale, $options);
        
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationTranslations');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaApplicationTranslations */
			$translations =  $pluginInstance->getTranslations($locale);
			$translate = array_merge_recursive($translate, $translations);
		}
		
		return $translate;
    }
}
