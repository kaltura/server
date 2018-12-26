<?php
/**
 * @package plugins.drm
 */
class BaseDrmPlugin extends KalturaPlugin
{
	const BASE_PLUGIN_NAME = 'drm';
	/**
	 * @return string the name of the plugin
	 */
	public static function getPluginName(){}

	/**
	 * @param array<kRuleAction> $actions
	 * @return bool
	 */
    public static function shouldContributeToPlaybackContext(array $actions)
    {
	    foreach ($actions as $action)
	    {
		    /*** @var kRuleAction $action */
		    if ($action->getType() == DrmAccessControlActionType::DRM_POLICY)
			    return true;
	    }

	    return false;
    }
}


