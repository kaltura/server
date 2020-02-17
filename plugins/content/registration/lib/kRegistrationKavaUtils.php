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
		$registrationUserEntryType = RegistrationPlugin::getRegistrationUserEntryTypeCoreValue(RegistrationUserEntryType::REGISTRATION);
		if (!isset($context['custom_criterion']))
		{
			$context['custom_criterion'] = array();
		}
		$customCtiteriaType['column'] = 'user_entry.TYPE';
		$customCtiteriaType['comparison'] = '=';
		$customCtiteriaType['value'] = $registrationUserEntryType;
		$context['custom_criterion'][] = $customCtiteriaType;

		$enrichedResult = self::genericQueryEnrich($ids, $partner_id, $context);

		return $enrichedResult;
	}

}
