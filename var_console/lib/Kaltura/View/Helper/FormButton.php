<?php
class Kaltura_View_Helper_FormButton extends Zend_View_Helper_FormButton
{
	 /**
     * Generates a 'button' element with a div after the content for better styling
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
	public function formButton($name, $value = null, $attribs = null)
	{
		$content = '';
		if (isset($attribs['content'])) 
			$content = $attribs['content'];
		else 
			$content = $value;
			
		$escape = (isset($attribs['content']) && $attribs['content']) ? true : false;
		
		// handle escaping here because we need to append an html inside the button
		$content = ($escape) ? $this->view->escape($content) : $content;
		$content = '<span>' . $content . '</span>';
		$attribs['content'] = $content;
		
		// parent should not escape
		$attribs['escape'] = false;
		
		return parent::formButton($name, $value, $attribs);
	}
}
