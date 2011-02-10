<?php
/**
 * @package api
 * @subpackage ps2
 */
class listpartnerpackagesAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "listPartnerPackages",
				"desc" => "Return list of available packages." ,
				"in" => array (
					"mandatory" => array (
						),
					"optional" => array (
						)
					),
				"out" => array (
					"packages" => array ("type" => "Package", "desc" => ""),
					),
				"errors" => array (
				)
			);
	}

		
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{

		$packages = new PartnerPackages();
				
		$this->addMsg ( "packages", $packages->listPackages());
	}
	
}
?>