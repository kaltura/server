<?php
class Form_Base extends Infra_Form
{
	protected $templatePath = null;
	protected $viewParams = array();
	
	protected function setTemplatePath($v)
	{
		$this->templatePath = $v;
	}
	
	protected function addViewParam($name, $value)
	{
		$this->viewParams[$name] = $value;
	}
	
	/**
	 * Render form with template
	 *
	 * @param  Zend_View_Interface $view
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null)
	{
		if(is_null($this->templatePath))
			return parent::render($view);
		
		if(null !== $view)
		{
			$this->setView($view);
		}
		
		$partialView = $this->getView();
		$translator = $this->getTranslator();
		$noDecorations = array(array('ViewHelper'), array('Errors')); // Keep the error
		$fileDecorations = array(array('File'), array('Errors')); // Keep the error
		

		// Loop through all the form elements in this Form
		foreach($this as $item)
		{
			$item->setView($view)->setTranslator($translator);
			// Remove Label, all setting
			if($item->getType() == 'Zend_Form_Element_File')
			{
				$item->setDecorators($fileDecorations);
			}
			else
			{
				$item->setDecorators($noDecorations);
			}
			$strVarName = $item->getName();
			$partialView->$strVarName = $item->render();
		}
		
		foreach($this->viewParams as $name => $value)
			$partialView->$name = $value;
		
		// pass the <form> attributes
		$strReturn = "<form ";
		// form attributes
		foreach($this->getAttribs() as $strKey => $strValue)
		{
			// Safari Bug, action must be having something (not empty)
			if($strKey == 'action' && $strValue == "")
				$strReturn .= "action='" . $this->getView()->url() . "'";
			else
				$strReturn .= $strKey . '="' . $strValue . '" ';
		}
		$strReturn .= "/>";
		
		$strReturn .= $this->getView()->partial($this->templatePath, $partialView);
		$strReturn .= "</form>";
		return $strReturn;
	}
}