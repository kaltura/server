<?php
require_once ( "defPartnerservices2Action.class.php");

class getthumbnailAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "getThumbnail",
				"desc" => "" ,
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
	
	public function needKuserFromPuser ( )	{		return self::KUSER_DATA_NO_KUSER;	}
	
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
?>