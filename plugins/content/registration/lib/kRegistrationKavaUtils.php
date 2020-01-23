<?php
/**
 * @package plugins.registration
 * @subpackage lib
 */

class kRegistrationKavaUtils extends kKavaReportsMgr
{
	protected static function getRegistrationUserEntry($ids, $partner_id, $context)
	{
		$context['peer'] = 'UserEntryPeer';
		if (!isset($context['columns']))
		{
			$context['columns'] = array();
		}
		$value = RegistrationPlugin::getRegistrationUserEntryTypeCoreValue(RegistrationUserEntryType::REGISTRATION);
		$registrationUserEntryType = kPluginableEnumsManager::apiToCore('UserEntryType', $value);
		if (!isset($context['custom_criterion']))
		{
			$context['custom_criterion'] = array();
		}
		$customCtiteriaType['column'] = 'TYPE';
		$customCtiteriaType['comparison'] = '=';
		$customCtiteriaType['value'] = $registrationUserEntryType;
		$context['custom_criterion'][] = $customCtiteriaType;

		$enrichedResult = self::genericQueryEnrich($ids, $partner_id, $context);

		return $enrichedResult;
	}

}