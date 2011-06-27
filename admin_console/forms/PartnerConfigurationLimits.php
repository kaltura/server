<?php

class Form_PartnerConfigurationLimits_SubForm extends Zend_Form_SubForm
{
	public function init()
	{
	    $this->setDecorators(array(
			'FormElements',
		));
	    
		$this->addElement('text', Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS, array(
			'label'			=> 'Maximum login attemps:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS, array(
			'label'			=> 'Number of KMC admin users:',
			'filters'		=> array('StringTrim'),
		
		));
	}
	
	public function populateFromObject($object)
	{
		if (is_array($object))
		{
			foreach ($object as $limit)
			{
				if ($limit instanceof Kaltura_Client_SystemPartner_Type_SystemPartnerLimit)
				{
					$this->setDefault($limit->type, $limit->max);
				}
			}
		}
	}
	
	public function getObject(array $properties)
	{
		$limitArray = array();
		
		foreach ($properties as $prop)
		{
			var_dump($prop);
		}
	}
}