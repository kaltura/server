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

		$this->addTextElement('pIdFrm', 'Partner ID:', $this->partnerId);
		$this->addTextElement('drmTypeFrm', 'DRM Type:', $this->drmType);
		
		$this->addDocFields();
	}

	private function addDocFields()
	{
//		$partnerDocFields = array('providerSignKey', 'key', 'iv', 'provider', 'seed', 'cas_username', 'cas_password');
		$partnerDocFields = array('provider_sign_key', 'key', 'iv', 'provider', 'seed', 'cas_username', 'cas_password');
		$fpsDocFields = array('ask', 'keyPem');

		foreach($partnerDocFields as $field)
			$this->addTextElement($field, "$field:", "", $this->readOnly);

		if ($this->drmType == 'fps') {
			$this->addLine("fps_line");
			$this->addTitle("FPS");
			$this->addComment('KeyPemNote', 'The KeyPem is private key base64 encoded for FPS documents. Note - It has to be un-encrypted data');
			foreach($fpsDocFields as $field)
				$this->addTextElement($field, "$field:", "", $this->readOnly);
		}

	}
	
	public function populate($res)
	{
		$values = json_decode($res, true);
		if (json_last_error() == JSON_ERROR_NONE)
		{
			foreach($values as $key => $val)
				if ($elem = $this->getElement($key))
					$elem->setValue($val);
		} else {
			$this->addLine("server_results_line");
			$this->addTextElement('serverResults', 'Raw Results', $res);
		}
	}


	private function addTextElement($id, $label, $value = '', $readOnly = true) {
		$option = array('label'	=> $label, 'filters'=> array('StringTrim'),'value'=> $value);
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