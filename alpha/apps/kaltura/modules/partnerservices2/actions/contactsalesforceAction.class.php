<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class contactsalesforceAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "contactSalesforce",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						),
					"optional" => array (
						"name" => array ("type" => "string", "desc" => ""),
						"phone" => array ("type" => "string", "desc" => ""),
						"comments" => array ("type" => "string", "desc" => ""),
						"services" => array ("type" => "string", "desc" => "comma-separated list of services"),
						)
					),
				"out" => array (
					
					),
				"errors" => array (
					)
			);
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$partner = new Partner();
		$partner = PartnerPeer::retrieveByPk($partner_id);
		
		$name = $this->getP("name");
		if (!$name) $name = ($puser_kuser) ? $puser_kuser->getPuserName() : "";
		
		$phone = $this->getP("phone");
		if (!$phone) $phone = $partner->getPhone();

		$comments = $this->getP("comments");
		$services = $this->getP("services");
		
		$array_to_lead['partner_id'] = $partner_id;
		$array_to_lead['partner_type'] = $partner->getType();
		$array_to_lead['admin_email'] = $partner->getAdminEmail();
		$array_to_lead['partner_description'] = $partner->getDescription();
		$array_to_lead['website'] = $partner->getUrl1();

		if ($puser_kuser) 
		{
			$kuser = $puser_kuser->getKuser();
			$array_to_lead['user_email'] = $kuser->getEmail();
		}
		else
		{
			$array_to_lead['user_email'] = $partner->getAdminEmail();
		}
		
		$array_to_lead['name'] = $name;
		$array_to_lead['company'] = $partner->getName();
		$array_to_lead['phone'] = $phone;		
		$array_to_lead['comments'] = $comments;
		$array_to_lead['services'] = $services;
		$this->sendLeadToMarketo($array_to_lead);

		$this->addMsg ( "lead" , $array_to_lead ) ;
		$this->addDebug ( "curl_response", "");
	}
	
	private function sendLeadToMarketo($lead_array)
	{
			$partner_types_texts = array(
				1 => 'KMC_SIGNUP',
				101 => 'Wordpress',
				102 => 'Drupal Module',
				103 => 'DekiWiki',
				104 => 'Moodle',
				105 => 'KalturaCE',
			);
			if(!substr_count($lead_array['partner_description'], 'KMC_SIGNUP'))
			{
				$partner_types_texts[1] = '';
			}
			
			$accessKey = kConf::get('marketo_access_key');
			$secretKey = kConf::get('marketo_secret_key');
			$soapEndPoint = 'https://na-g.marketo.com/soap/mktows/1_3';
			$marketo = new MarketoApiService($soapEndPoint.'?WSDL', array('location' => $soapEndPoint));
			$marketo->setCredentials($accessKey, $secretKey);
			
			$leadRecord = new LeadRecord();
			$leadRecord->Email = $lead_array['admin_email'];
			$leadRecord->leadAttributeList = new stdClass();
			$attributes = array();
			$attributes[] = $marketo->createAttribute('Download_Center__c', 'KMC Contact us');
			$attributes[] = $marketo->createAttribute('LastName', $lead_array['name']);
			$attributes[] = $marketo->createAttribute('Kaltura_Partner_ID__c', $lead_array['partner_id']);
			$attributes[] = $marketo->createAttribute('Company', $lead_array['company']);
			$attributes[] = $marketo->createAttribute('Kaltura_Partner_Activation_Type__c', $partner_types_texts[$lead_array['partner_type']]);
			$attributes[] = $marketo->createAttribute('Phone', $lead_array['phone']);
			$attributes[] = $marketo->createAttribute('Website', $lead_array['website']);
			$attributes[] = $marketo->createAttribute('Kaltura_User_Email__c', $lead_array['user_email']);
			$attributes[] = $marketo->createAttribute('Kaltura_Comments__c', $lead_array['comments']);
			$attributes[] = $marketo->createAttribute('Form_KMC_Upgrade_Request__c', 'True ' . date('r'));
			
			$leadRecord->leadAttributeList->attribute = $attributes;
			$params = new ParamsSyncLead();
			$params->leadRecord = $leadRecord;
			$marketo->syncLead($params);
	}
}
?>