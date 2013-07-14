<?php
/**
 * @package Var
 * @subpackage Partners
 */
class Form_PartnerUsagePaginator extends Infra_Paginator
{
    /**
     * Flag showing whether the content presented by the paginator is filtered in any way.
     * @var bool
     */
    public $filtered;
    
    public function getTotal ()
    {
        return $this->_adapter->getTotal();
    }
}