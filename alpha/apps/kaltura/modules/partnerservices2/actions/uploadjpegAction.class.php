<?php
require_once ( "defPartnerservices2Action.class.php");

class uploadjpegAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "uploadJpeg",
				"desc" => "post data" ,
				"in" => array (
					"mandatory" => array ( 
						"filename" => array ("type" => "string", "desc" => ""),
						"hash" => array ("type" => "string", "desc" => "")
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
  		$filename = $this->getPM('filename');
		$hash = $this->getP('hash');
  		
  		// strip the filename from invalid characters
		$token = $this->getKsUniqueString();

		if(isset($HTTP_RAW_POST_DATA))
			$data = $HTTP_RAW_POST_DATA;
		else
			$data = file_get_contents("php://input");

		// should upload the file with the token as the prefix
		// call method and expect the extended data
		$res = myUploadUtils::uploadJpeg ( $data , $token , $filename ,null , null , true );

		$this->addMsg( "result_ok" , $res );
	}
}
?>