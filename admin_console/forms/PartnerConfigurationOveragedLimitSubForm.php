<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PartnerConfigurationOveragedLimitSubForm extends Form_PartnerConfigurationLimitSubForm
{

	public function addElementsToForm($form)
	{
		$element = parent::addElementsToForm($form);

		$element->addDecorators(array(
			'ViewHelper',
			array('Label'),
			array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'includeUsageFloatLeft')),
		));

		$form->addElement('text',  $this->limitType.'_overagePrice', array(
			'label'			=> 'Overage Fee:',
			'filters'		=> array('StringTrim'),
			//'decorators'	=> array('Label', 'ViewHelper', array('HtmlTag',array('tag'=>'div','closeOnly'=>true, 'class' =>'includeUsage'))),
		));
		$element = $form->getElement($this->limitType.'_overagePrice');
		$element->setBelongsTo($this->limitType);

		$element->addDecorators(array(
			'ViewHelper',
			array('Label'),
			array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'includeUsageFloatRight',)),
		));

		$form->addElement('text',  $this->limitType.'_overageUnit', array(
			'label'			=> 'Overage Unit:',
			'filters'		=> array('StringTrim'),
			//'decorators'	=> array('Label', 'ViewHelper', array('HtmlTag',array('tag'=>'div','closeOnly'=>true, 'class' =>'includeUsage'))),
		));
		$element = $form->getElement($this->limitType.'_overageUnit');
		$element->setBelongsTo($this->limitType);

		$element->addDecorators(array(
			'ViewHelper',
			array('Label'),
			array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'includeUsageFloatRight',)),
		));
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		return parent::getObject("Kaltura_Client_SystemPartner_Type_SystemPartnerOveragedLimit", $properties, $add_underscore, $include_empty_fields);
	}

}
