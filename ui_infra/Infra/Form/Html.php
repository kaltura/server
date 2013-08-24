<?php
/**
 * @package UI-infra
 * @subpackage forms
 */
class Infra_Form_Html extends Zend_Form_Element_Xhtml
{
    public function render(Zend_View_Interface $view = null)
    {
    	return $this->getAttrib('content');
    }
}