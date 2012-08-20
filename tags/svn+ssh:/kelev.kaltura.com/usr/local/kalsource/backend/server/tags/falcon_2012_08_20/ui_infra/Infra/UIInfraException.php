<?php
/**
 * @package UI-infra
 * @subpackage Errors
 */
class Infra_UIInfraException extends Exception
{
    protected $code;
    
    public function Infra_UIInfraException($codeString)
    {
        $this->code = $codeString;
    }
}