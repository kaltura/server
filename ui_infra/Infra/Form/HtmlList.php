<?php
/**
 * @package UI-infra
 * @subpackage forms
 */
class Infra_Form_HtmlList extends Zend_Form_Element_Xhtml
{
    public function render(Zend_View_Interface $view = null)
    {
    	$legend = $this->getAttrib('legend');
    	$html = '';
    	if($legend)
    		$html .= "<legend>$legend</legend>";
    		
    	$html .= '<ol><li>' . implode('</li><li>', $this->getAttrib('list')) . '</li></ol>';
    	
    	return "<div><fieldset>$html</fieldset></div>";
    }
}