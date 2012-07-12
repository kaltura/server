<?php
/**
 * @package Var
 * @subpackage Partners
 */
class Form_PartnerUsagePaginator extends Infra_Paginator
{
    public function getTotal ()
    {
        return $this->_adapter->getTotal();
    }
}