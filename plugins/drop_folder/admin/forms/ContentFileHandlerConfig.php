<?php 
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_ContentFileHandlerConfig extends Infra_Form
{
	public function init()
	{
		$fileDeletePolicies = new Kaltura_Form_Element_EnumSelect('contentMatchPolicy', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderContentFileHandlerMatchPolicy'));
		$fileDeletePolicies->setLabel('Content Match Policy:');
		$fileDeletePolicies->setRequired(true);
		$this->addElement($fileDeletePolicies);
		
		$this->addElement('text', 'slugRegex', array(
			'label' 		=> 'Slug Regex:',
		    'value' 		=> '/(?P<referenceId>.+)[.]\w{2,}/',
			'filters'		=> array('StringTrim'),
		));
				
		$this->setDecorators(array(
	        'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => 'frmContentFileHandlerConfig')),
        ));
	}
}