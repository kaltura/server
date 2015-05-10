<?php

class kMetadataProfileManager
{
    public static function validateXsdData($xsdData, &$errorMessage)
    {
        // validates the xsd
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        
        $xml = new KDOMDocument();
        if(!$xml->loadXML($xsdData))
        {
            $errorMessage = kXml::getLibXmlErrorDescription($xsdData);
            return false;
        }
        
        libxml_clear_errors();
        libxml_use_internal_errors(false);
        
        return true;
    }
}