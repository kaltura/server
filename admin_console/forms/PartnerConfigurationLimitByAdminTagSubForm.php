<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PartnerConfigurationLimitByAdminTagSubForm extends Form_PartnerConfigurationLimitSubForm
{
	protected $adminTag;

	public function __construct($limitType, $label, $adminTag)
	{
		$this->adminTag = $adminTag;
		parent::__construct($limitType, $label);
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject('Kaltura_Client_SystemPartner_Type_SystemPartnerLiveAdminTagLimit', $properties, $add_underscore, $include_empty_fields);
		$object->adminTag = $this->adminTag;
		$object->type = Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::LIVE_CONCURRENT_BY_ADMIN_TAG; // Overriding the type from properties.
		return $object;
	}

}
