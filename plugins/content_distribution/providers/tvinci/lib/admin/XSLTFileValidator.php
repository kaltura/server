<?php

class XSLTFileValidator extends Zend_Validate_Abstract
{

    const ERROR = 'error';

    protected $_messageTemplates = array(
        self::ERROR      => "xml structure on loaded file is invalid",
    );

    function isValid( $value, $context = null ) {

        // empty file is valid
        if (!$value || empty($value)){
            return true;
        }
        // first try to read the tmp file
        $data = file_get_contents($value);
        if ($data === ""){
            // if you want to truncate the xslt
            return true;
        } else if ($data !== false) {
            // this means there is data there - try to parse as XML
            $dom = new DOMDocument;
            $result = $dom->loadXML($data);
            if ($result) {
                return true;
            }
        }
        $this->_error(self::ERROR);
        return false;

    }
}

?>