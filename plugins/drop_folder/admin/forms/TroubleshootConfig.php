<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_TroubleshootConfig extends Zend_Form_SubForm
{
	//		$this->view->tr

	public function init()
	{
		$this->addElement('hidden', 'crossLine5', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		 ));

		$titleElement = new Zend_Form_Element_Hidden('troubleshootTitle');
		$titleElement->setLabel('Troubleshoot');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

		$status = new Kaltura_Form_Element_EnumSelect('status', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderStatus'));
		$status->setLabel('Status:');
		$status->setAttrib('readonly', true);
		$status->setAttrib('disabled', 'disabled');
		$this->addElement($status);

		$this->addElement('text', 'lastAccessedAt', array(
			'label'			=> 'Last Drop Folder Access Time/Date:',
			'disabled'		=>true,
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('textarea', 'errorDescription', array(
			'label'			=> 'Error Description:',
			'disabled'		=>true,
			'rows'			=> 3,
			'filters'		=> array('StringTrim'),
		));

		$this->setDecorators(array(
	    	'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => 'frmTroubleshootConfig')),
        ));

	}

	public function populateFromObject($object, $add_underscore = true)
	{
		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);

		foreach($props as $prop => $value)
		{
			if($add_underscore)
			{
				$pattern = '/(.)([A-Z])/';
				$replacement = '\1_\2';
				$prop = strtolower(preg_replace($pattern, $replacement, $prop));
			}
			$this->setDefault($prop, $value);
		}

		$lastAccessedAtElm = $this->getElement('lastAccessedAt');
		if($lastAccessedAtElm->getValue())
			$formattedDate = date($this->getView()->translate('time format'), $lastAccessedAtElm->getValue());
		else
			$formattedDate = 'N/A';
		$lastAccessedAtElm->setValue($formattedDate);

		if($object->status !== Kaltura_Client_DropFolder_Enum_DropFolderStatus::ERROR)
		{
			$descElm = $this->getElement('errorDescription');
			$descElm->setAttrib('style', 'display:none');
			$descElm->setLabel('');
		}
	}

	/**
	 * @param string $objectType Kaltura client class name
	 * @param array $properties
	 * @param boolean $add_underscore
	 * @param boolean $include_empty_fields
	 * @return Kaltura_Client_ObjectBase
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = new $objectType;
		foreach($properties as $prop => $value)
		{
			if($add_underscore)
			{
				$parts = explode('_', strtolower($prop));
				$prop = '';
				foreach ($parts as $part)
					$prop .= ucfirst(trim($part));
				$prop[0] = strtolower($prop[0]);
			}

			if ($value !== '' || $include_empty_fields)
			{
				try{
					$object->$prop = $value;
				}catch(Exception $e){}
			}
		}

		return $object;
	}
 }