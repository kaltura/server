<?php
/**
* @package Core
* @subpackage model.filters
*/

class systemPartnerFilter extends partnerFilter
{
    public function init ()
    {
        parent::init();
        $this->fields["_eq_partner_parent_id"] = null;
        $this->fields["_in_partner_parent_id"] = null;
        $this->fields["_eq_admin_email"] = null;
    }

    public function describe()
    {
        return
            array (
                "display_name" => "SystemPartnerFilter",
                "desc" => ""
            );
    }
}

