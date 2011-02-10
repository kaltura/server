<?php
/**
 * @package api
 * @subpackage ps2
 */
class uploadAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "upload",
				"desc" => "post data" ,
				"in" => array (
					"mandatory" => array ( 
						"filename" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"result_ok" => array ("type" => "string", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
  		$filename = $this->getPM ('filename');
		// strip the filename from invalid characters
		$token = $this->getKsUniqueString();
		
		// should upload the file with the token as the prefix
		$res = myUploadUtils::uploadFileByToken ( $_FILES['Filedata'] , $token , $filename ,null , true );
		
		$this->addMsg( "result_ok" , $res );
	}
}
