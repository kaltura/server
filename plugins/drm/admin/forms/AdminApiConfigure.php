<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class Form_AdminApiConfigure extends Infra_Form
{
	protected $partnerId;
	protected $drmType;
	protected $readOnly;
	protected $actionApi;
	
	public function __construct($partnerId, $drmType, $actionApi)
	{
		$this->partnerId = $partnerId;
		$this->drmType = $drmType;
		$this->actionApi = $actionApi;
		$this->readOnly = !($actionApi == AdminApiActionType::ADD);

		parent::__construct();
	}


	public function init()
	{
		$this->setAttrib('id', 'frmAdminApiConfigure');
		$this->setMethod('post');

		if ($this->actionApi == AdminApiActionType::REMOVE)
			$this->addTitle('ARE YOU SURE YOU WANT TO REMOVE THIS DOC? if you do, click Execute');

		$this->addTextElement('DrmPartnerId', $this->partnerId);
		$this->addTextElement('DrmType', $this->drmType);
		
		$this->addDocFields();
	}

	private function addSection($name, $fields)
	{
		$this->addLine("{$name}_line");
		$this->addTitle($name);
		foreach($fields as $field)
		{
			$this->addTextElement($field, '', $this->readOnly);
		}
	}
	
	private function addDocFields()
	{
		$partnerDocFields = array('provider_sign_key', 'key', 'iv', 'provider', 'seed', 'cas_username', 'cas_password', 'default_entitlement');
		$fpsDocFields = array('ask', 'key_pem', 'fps_default_persistence_duration');
		$cencDocFields = array('wv_override_device_revocation');
		
		foreach($partnerDocFields as $field)
			$this->addTextElement($field, '', $this->readOnly);
		
		$this->addTextElement('Other');
		
		if ($this->drmType === 'cenc')
		{
			$this->addSection('CENC', $cencDocFields);
		}
		
		if ($this->drmType == 'fps')
		{
			$this->addSection('FPS', $fpsDocFields);
			$this->addComment('KeyPemNote', 'The KeyPem is private key base64 encoded for FPS documents. Note - It has to be un-encrypted data');
		}

	}
	
	public function populateJson($res)
	{
		$values = json_decode($res, true);
		$other = array();
		if (json_last_error() == JSON_ERROR_NONE)
		{
			foreach($values as $key => $val)
			{
				if($key === 'default_entitlement')
				{
					$val = json_encode($val);
				}
				if ($elem = $this->getElement($key))
				{
					$elem->setValue($val);
				}
				else
				{
					$other[$key] = $val;
				}
			}
			if ($other && $elem = $this->getElement('Other'))
			{
				$elem->setValue(json_encode($other));
			}
		}
		else
		{
			$this->addLine("server_results_line");
			$this->addTextElement('ServerRawResults', $res);
		}
	}


	private function addTextElement($id, $value = '', $readOnly = true)
	{
		$option = array('label'	=> "$id:", 'filters'=> array('StringTrim'),'value'=> $value);
		if ($readOnly)
			$option['readonly'] = true;
		$this->addElement('text', $id, $option);
	}

	private function addComment($name, $msg, $addDecorator = array(array('HtmlTag',  array('tag' => 'dd', 'class' => 'comment')))) {
		$element = new Zend_Form_Element_Hidden($name);
		$element->setLabel($msg);
		$decorator = array('ViewHelper', array('Label', array('placement' => 'append')));
		$element->setDecorators(array_merge($decorator, $addDecorator));
		$this->addElements(array($element));
	}

	private function addTitle($name)
	{
		$this->addComment(str_replace(' ', '', $name), $name, array(array('HtmlTag',  array('tag' => 'b'))));
	}

	private function addLine($name)
	{
		$tag = str_replace(' ', '', $name);
		$this->addElement('hidden', "crossLine_$tag", array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
	}



}