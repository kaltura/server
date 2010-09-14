<?php
require_once ( "defPartnerservices2Action.class.php");

class webcamdummyAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
			); 
	}
	
	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}
	
	/* 
	simulates the webcam by uploading an flv file
	*/  
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
  		$filename = $this->getP('filename');
		// strip the filename from invalid characters
		$token = $this->getKsUniqueString();
		
//		$res = myUploadUtils::uploadFileByToken ( $_FILES['Filedata'] , $token , $filename );
		
		$origFilename = $_FILES['Filedata']['name'];
		$parts = pathinfo($origFilename);
		$extension = "flv" ;// always flv ! //strtolower($parts['extension']);

		$extra_id = null;
		
		$file_alias = $token .'_'. $filename;
		// add the file extension after the "." character
		$fullPath = myContentStorage::getFSContentRootPath(). "content/webcam/my_recorded_stream_" . $file_alias . ( $extra_id ? "_" . $extra_id : "" ) .".".$extension;
		
		myContentStorage::fullMkdir($fullPath);
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $fullPath);
		chmod ( $fullPath , 0777 );
		
		$res =  array ( "token" => $token , "filename" => $filename , "origFilename" => $origFilename );
		
		// should upload the file with the token as the prefix
		$this->addMsg( "result_ok" , $res );
	}
}
?>