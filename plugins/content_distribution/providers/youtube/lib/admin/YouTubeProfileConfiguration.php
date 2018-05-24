<?php 
/**
 * @package plugins.youTubeDistribution
 * @subpackage admin
 */
class Form_YouTubeProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	/**
	 * This element id is used to separate the default profile elements and youtube profile elements, so later we could
	 * insert the dynamic elements at the correct position
	 */
	const FORM_PLACEHOLDER_ELEMENT_ID = 'youtube_placeholder';

	public function init()
	{
		parent::init();
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'youtube-distribution.phtml',
			'placement' => 'APPEND'
		));
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		if($object instanceof Kaltura_Client_YouTubeDistribution_Type_YouTubeDistributionProfile)
		{
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();
         
			if(isset($files['sftp_public_key']))
			{
				$file = $files['sftp_public_key'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->sftpPublicKey = $content;
				}
			}
			
			if(isset($files['sftp_private_key']))
			{
				$file = $files['sftp_private_key'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->sftpPrivateKey = $content;
				}
			}
		}
		return $object;
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		$this->_sort();

		$order = $this->_order[self::FORM_PLACEHOLDER_ELEMENT_ID];
		$this->resetOrderOfLastElements();
		/** @var $object Kaltura_Client_YouTubeDistribution_Type_YouTubeDistributionProfile */
		switch($object->feedSpecVersion)
		{
			case Kaltura_Client_YouTubeDistribution_Enum_YouTubeDistributionFeedSpecVersion::VERSION_1:
			{
				$this->setV1Mode($order++);
				break;
			}
			case Kaltura_Client_YouTubeDistribution_Enum_YouTubeDistributionFeedSpecVersion::VERSION_2:
			{
				$this->setV2Mode($order++);
				break;
			}
			case Kaltura_Client_YouTubeDistribution_Enum_YouTubeDistributionFeedSpecVersion::VERSION_3:
			{
				$this->setV3Mode($order++);
				break;
			}
			default:
				$this->setV1Mode($order++);
		}

		parent::populateFromObject($object, $add_underscore);
	}

	protected function addProviderElements()
	{
	    $this->setDescription(null);
	    
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('YouTube Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		

		$this->addElement('select', 'feed_spec_version', array(
			'label'			=> 'Feed Specification Version:',
			'multioptions' => array(
				'1' => 'Version 1 (Legacy Feed)',
				'2' => 'Version 2 (YouTube Rights Feeds)',
				'3' => 'Version 3 (YouTube CSV)',
			),
			'description' => 'Save to see specific spec configurations',
		));

		$this->addElement('text', 'api_authorize_url', array(
			'label'			=> 'Authorize API Access:',
			'decorators' => array(array('ViewScript', array(
				'viewScript' => 'youtube-distribution-api-authorize-field.phtml',

			)))
		));

		$this->addDisplayGroup(
			array('feed_spec_version'),
			'feed_spec_version_group',
			array('legend' => '', 'decorators' => array('FormElements', 'Fieldset'))
		);

		$this->addElement('hidden', self::FORM_PLACEHOLDER_ELEMENT_ID);
	}

	public function resetOrderOfLastElements()
	{
		$found = false;
		foreach ($this->_order as $key => &$order)
		{
			if ($found)
				$order = null;

			if ($key == self::FORM_PLACEHOLDER_ELEMENT_ID)
				$found = true;
		}
	}

	protected function addGeneralElements($order)
	{
		$this->getElement('feed_spec_version')->setDescription('When changing this field, the form must be saved and re-opened');

		// General
		$this->addElement('text', 'username', array(
			'label'			=> 'YouTube Account:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'notification_email', array(
			'label'			=> 'Notification Email:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'owner_name', array(
			'label' => 'Owner Name:',
		));

		$this->addElement('select', 'target', array(
			'label' => 'Target:',
			'multioptions' => array(
				'upload,claim,fingerprint' => 'upload,claim,fingerprint',
				'upload,claim' => 'upload,claim',
				'claim,fingerprint' => 'claim,fingerprint',
			)
		));

		// SFTP Configuration
		$this->addElement('text', 'sftp_host', array(
			'label'			=> 'SFTP Host:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'sftp_port', array(
			'label'			=> 'SFTP Port:',
			'default'		=> '22',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'sftp_login', array(
			'label'			=> 'SFTP Login:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('file', 'sftp_public_key', array(
			'label' => 'SFTP Public Key:'
		));

		$this->addElement('file', 'sftp_private_key', array(
			'label' => 'SFTP Private Key:'
		));

		$this->addElement('text', 'sftp_base_dir', array(
			'label'			=> 'SFTP Base Directory:',
			'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup(
			array('sftp_host', 'sftp_port', 'sftp_login', 'sftp_public_key', 'sftp_private_key', 'sftp_base_dir'),
			'sftp',
			array('legend' => 'SFTP Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);

		//  Metadata
		$this->addElement('text', 'default_category', array(
			'label' => 'Default Category:',
		));

		// Advertising
		$this->addElement('checkbox', 'enable_ad_server', array(
			'label' => 'Enable AD server:',
		));

		$this->addElement('text', 'ad_server_partner_id', array(
			'label' => 'Ad Server Partner ID:',
		));

		$this->addElement('checkbox', 'allow_pre_roll_ads', array(
			'label' => 'Allow Pre-Roll Ads:',
		));

		$this->addElement('checkbox', 'allow_mid_roll_ads', array(
			'label' => 'Allow Mid-Roll Ads:',
		));

		$this->addElement('checkbox', 'allow_post_roll_ads', array(
			'label' => 'Allow Post-Roll Ads:',
		));

		// Community
		$this->addElement('select', 'allow_comments', array(
			'label' => 'Allow Comments:',
			'multioptions' => array(
				'' => 'Default',
				'Always' => 'Always',
				'Approve' => 'Approve',
				'Never' => 'Never',
			)
		));

		$this->addElement('select', 'allow_embedding', array(
			'label' => 'Allow Embedding:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'allow_ratings', array(
			'label' => 'Allow Ratings:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'allow_responses', array(
			'label' => 'Allow Responses:',
			'multioptions' => array(
				'' => 'Default',
				'Always' => 'Always',
				'Approve' => 'Approve',
				'Never' => 'Never',
			)
		));

		$this->addElement('text', 'commercial_policy', array(
			'label' => 'Commercial Policy:'
		));

		$this->addElement('text', 'ugc_policy', array(
			'label' => 'UGC Policy:'
		));

		// V2 elements
		$this->addElement('select', 'strict', array(
			'label' => 'Strict:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'override_manual_edits', array(
			'label' => 'Override Manual Edits:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'urgent_reference', array(
			'label' => 'Urgent Reference:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'allow_syndication', array(
			'label' => 'Allow Syndication:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'hide_view_count', array(
			'label' => 'Hide View Count:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));


		$this->addElement('select', 'allow_adsense_for_video', array(
			'label' => 'Allow AdSense Ads:',
			'multioptions' => array(
				'' => 'Default',
				'Allow' => 'Allow',
				'Deny' => 'Deny',
			)
		));
		$this->addElement('select', 'allow_invideo', array(
			'label' => 'Allow InVideo Ads:',
			'multioptions' => array(
				'' => 'Default',
				'Allow' => 'Allow',
				'Deny' => 'Deny',
			)
		));

		$this->addElement('checkbox', 'allow_mid_roll_ads', array(
			'label' => 'Allow Mid-Roll Ads:',
		));

		$this->addElement('select', 'instream_standard', array(
			'label' => 'Instream Standard:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
				'long' => 'long',
				'short' => 'short',
				'disallow' => 'disallow',
			)
		));

		$this->addElement('select', 'instream_trueview', array(
			'label' => 'Instream TrueView Ads:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'claim_type', array(
			'label' => 'Claim Type:',
			'multioptions' => array(
				'audiovisual' => 'Audio Visual',
				'audio' => 'Audio',
				'visual' => 'Visual',
			)
		));

		$this->addElement('select', 'block_outside_ownership', array(
			'label' => 'Block Outside Ownership:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('select', 'caption_autosync', array(
			'label' => 'Caption Autosync:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False',
			)
		));

		$this->addElement('checkbox', 'delete_reference', array(
			'label' => 'Delete reference when removing distribution:',
		));

		$this->addElement('checkbox', 'release_claims', array(
			'label' => 'Release claims when deleting reference:',
		));
	}

	protected function setV1Mode($order)
	{
		$this->addGeneralElements($order);

		// General
		$this->addDisplayGroup(
			array('username', 'notification_email', 'owner_name', 'target'),
			'general',
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// SFTP Configuration
		$this->addDisplayGroup(
			array('sftp_host', 'sftp_port', 'sftp_login', 'sftp_public_key', 'sftp_private_key', 'sftp_base_dir'),
			'sftp',
			array('legend' => 'SFTP Configuration', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// Community
		$this->addDisplayGroup(
			array('allow_comments', 'allow_embedding', 'allow_ratings', 'allow_responses'),
			'community',
			array('legend' => 'Community', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// Advertising
		$this->addDisplayGroup(
			array('enable_ad_server', 'ad_server_partner_id', 'allow_pre_roll_ads', 'allow_post_roll_ads'),
			'advertising',
			array('legend' => 'Advertising', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		//  Metadata
		$this->addDisplayGroup(
			array('default_category'),
			'metadata',
			array('legend' => 'Metadata', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// Policies
		$this->addDisplayGroup(
			array('commercial_policy', 'ugc_policy'),
			'policies',
			array('legend' => 'Saved Policies', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		$this->removeElement('strict');
		$this->removeElement('override_manual_edits');
		$this->removeElement('urgent_reference');
		$this->removeElement('allow_syndication');
		$this->removeElement('hide_view_count');
		$this->removeElement('allow_adsense_for_video');
		$this->removeElement('allow_invideo');
		$this->removeElement('allow_mid_roll_ads');
		$this->removeElement('instream_standard');
		$this->removeElement('instream_trueview');
		$this->removeElement('claim_type');
		$this->removeElement('block_outside_ownership');
		$this->removeElement('caption_autosync');
		$this->removeElement('delete_reference');
		$this->removeElement('release_claims');
	}

	protected function setV2Mode($order)
	{
		$this->addGeneralElements($order);

		// modify the names of the elements to better fit the new spec
		$this->getElement('username')->setLabel('Channel:');
		$this->getElement('owner_name')->setLabel('Content Owner:');
		$this->getElement('default_category')->setLabel('Default Genre:');

		// SFTP Configuration
		$this->addDisplayGroup(
			array('sftp_host', 'sftp_port', 'sftp_login', 'sftp_public_key', 'sftp_private_key', 'sftp_base_dir'),
			'sftp',
			array('legend' => 'SFTP Configuration', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <feed>
		$this->addDisplayGroup(
			array('username', 'owner_name', 'notification_email', 'strict'),
			'feed_group',
			array('legend' => 'Feed', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <ownership>
		$this->addDisplayGroup(
			array('override_manual_edits'),
			'ownership_group',
			array('legend' => 'Ownership', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <video>
		$this->addDisplayGroup(
			array('allow_comment_rating', 'allow_comments', 'allow_embedding', 'allow_ratings', 'allow_responses', 'allow_syndication', 'hide_view_count', 'default_category'),
			'video_group',
			array('legend' => 'Video', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <ad_policy>
		$this->addDisplayGroup(
			array('allow_adsense_for_video', 'allow_invideo', 'instream_standard', 'instream_trueview', 'enable_ad_server', 'ad_server_partner_id', 'allow_pre_roll_ads', 'allow_mid_roll_ads', 'allow_post_roll_ads'),
			'ad_policy_group',
			array('legend' => 'Ad Policy', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <file>
		$this->addDisplayGroup(
			array('urgent_reference'),
			'file_group',
			array('legend' => 'File', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <claim>
		$this->addDisplayGroup(
			array('claim_type', 'block_outside_ownership'),
			'claim_group',
			array('legend' => 'Claim', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <reference>
		$this->addDisplayGroup(
			array('delete_reference', 'release_claims'),
			'reference_group',
			array('legend' => 'Reference', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <caption>
		$this->addDisplayGroup(
			array('caption_autosync'),
			'caption_group',
			array('legend' => 'Caption', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// Policies
		$this->addDisplayGroup(
			array('commercial_policy', 'ugc_policy'),
			'policies',
			array('legend' => 'Saved Policies', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);
		$this->getElement('commercial_policy')->setLabel('Commercial Policy (Usage):');
		$this->getElement('ugc_policy')->setLabel('UGC Policy (Match):');

		$this->removeElement('target');
	}

	protected function setV3Mode($order)
	{
		$this->addGeneralElements($order);
		$this->addV3Elements($order);

		// modify the names of the elements to better fit the new spec
		$this->getElement('username')->setLabel('Channel:');
		$this->getElement('owner_name')->setLabel('Content Owner:');
		$this->getElement('ugc_policy')->setLabel('Match Policy:');
		$this->getElement('commercial_policy')->setLabel('Usage Policy:');
		$this->getElement('instream_trueview')->setLabel('Skippable video ads:');

		$this->getElement('instream_standard')->setLabel('Non-skippable video ads:');
		$this->getElement('instream_standard')->setOptions( array(
			'multioptions' => array(
				'' => 'Default',
				'True' => 'True',
				'False' => 'False',
			)));

		$this->getElement('allow_invideo')->setLabel('Overlay ads:');
		$this->getElement('allow_invideo')->setOptions( array(
		'multioptions' => array(
		'' => 'Default',
		'True' => 'True',
		'False' => 'False',
		)));

		$this->getElement('allow_adsense_for_video')->setLabel('Display ads:');
		$this->getElement('allow_adsense_for_video')->setOptions( array(
			'multioptions' => array(
				'' => 'Default',
				'True' => 'True',
				'False' => 'False',
			)));

		$this->getElement('third_party_ads')->setLabel('Third Party Ads:');

		// SFTP Configuration
		$this->addDisplayGroup(
			array('sftp_host', 'sftp_port', 'sftp_login', 'sftp_public_key', 'sftp_private_key', 'sftp_base_dir'),
			'sftp',
			array('legend' => 'SFTP Configuration', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// General
		$this->addDisplayGroup(
			array('username', 'notification_email', 'owner_name','privacy_status','domain_whitelist'),
			'general',
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <video>
		$this->addDisplayGroup(
			array('notify_subscribers','enable_content_id', 'default_category'),
			'video_group',
			array('legend' => 'Video', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <ad_policy>
		$this->addDisplayGroup(
			array( 'instream_trueview','instream_standard','allow_invideo', 'product_listing_ads','allow_adsense_for_video','third_party_ads'),
			'ad_policy_group',
			array('legend' => 'Ad Policy', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// Policies
		$this->addDisplayGroup(
			array('commercial_policy','ugc_policy'),
			'policies',
			array('legend' => 'Saved Policies', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);

		// <claim>
		$this->addDisplayGroup(
			array('block_outside_ownership'),
			'claim_group',
			array('legend' => 'Claim', 'decorators' => array('FormElements', 'Fieldset'), 'order' => $order++)
		);


		$this->removeElement('allow_pre_roll_ads');
		$this->removeElement('allow_post_roll_ads');
		$this->removeElement('strict');
		$this->removeElement('allow_embedding');
		$this->removeElement('override_manual_edits');
		$this->removeElement('urgent_reference');
		$this->removeElement('allow_mid_roll_ads');
		$this->removeElement('claim_type');
		$this->removeElement('caption_autosync');
		$this->removeElement('delete_reference');
		$this->removeElement('release_claims');
		$this->removeElement('allow_comments');
		$this->removeElement('allow_ratings');
		$this->removeElement('allow_responses');
		$this->removeElement('allow_syndication');
		$this->removeElement('hide_view_count');
		$this->removeElement('target');
		$this->removeElement('enable_ad_server');
		$this->removeElement('ad_server_partner_id');
	}

	protected function addV3Elements($order)
	{
		$this->addElement('text', 'privacy_status', array(
			'label'			=> 'Privacy Status ( public, private, unlisted ):',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('select', 'enable_content_id', array(
			'label' => 'Enable Content Id:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False'
			)
		));

		$this->addElement('select', 'notify_subscribers', array(
			'label' => 'Notify Subscribers:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False'
			)
		));

		$this->addElement('text', 'domain_whitelist', array(
			'label'			=> 'Domain WhiteList:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('select', 'product_listing_ads', array(
			'label' => 'Allow Product Listing ads:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False'
			)
		));

		$this->addElement('select', 'third_party_ads', array(
			'label' => 'Allow Third Party Ads:',
			'multioptions' => array(
				'' => 'Default',
				'true' => 'True',
				'false' => 'False'
			)
		));

	}
}