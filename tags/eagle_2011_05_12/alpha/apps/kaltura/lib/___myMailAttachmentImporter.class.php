<?php

/*
 * This class implements a simple IMAP mail client that polls a provided IMAP server
 * downloads the messages and files, and formats them in an array for later use 
 * by insertMobileEntry
 * 
 */
class myMailAttachmentImporter
{
	/*
	 * This functions makes a connection to IMAP server, and downloads attachments and 
	 * gives out information about the messages on the server. it then delete the messages from the server
	 * 
	 * params:
	 * $host: mail server host i.e. mail.kaltura.com
	 * $long: username i.e. mobile@kaltura.com
	 * $password: user's pwd on the mailserver
	 * $path: to which attachments should be save
	 * 
	 * output: array with message data, and set of files saved in provided dir path
	 * 
	 */
	
	function getdata($host,$login,$password,$savedirpath)
	{

		$this->savedDirPath = $savedirpath;
		$this->attachmenttype = array( "text", "multipart","message","application","audio","image","video","other");

		// create empty array to store message data
		$this->importedMessageDataArray = array();

		// open the mailbox
		$mailbox="{".$host.":143/imap/notls}INBOX";

		$this->mbox = imap_open ($mailbox,  $login, $password);

		if ( $this->mbox == FALSE) return null;

		$status = imap_status($this->mbox, $mailbox, SA_ALL);
		
			echo "Messages: ", $status->messages, "<BR>\n";
			echo "Recent: ", $status->recent, "<BR>\n";
			echo "Unseen: ", $status->unseen, "<BR>\n";
			echo "UIDnext: ", $status->uidnext, "<BR>\n";
			echo "UIDvalidity: ", $status->uidvalidity, "<BR>\n";
			echo "Flags: ", $status->flags, "<BR>\n";
		
		// now itterate through messages
		for ($mid = imap_num_msg($this->mbox); $mid >= 1; $mid--)
		{
			$header = imap_header($this->mbox, $mid);

			$this->importedMessageDataArray[$mid]["subject"] =  property_exists( $header, 'subject') ? $header->subject: "";
			$this->importedMessageDataArray[$mid]["fromaddress"] = property_exists($header, 'fromaddress' ) ? $header->fromaddress : "";
			$this->importedMessageDataArray[$mid]["date"] = property_exists($header, 'date' ) ? $header->date : "";
			$this->importedMessageDataArray[$mid]["body"] = "";

			$this->structureObject = imap_fetchstructure($this->mbox, $mid );
			$this->saveAttachments( $mid );
			$this->getBody( $mid );

			imap_delete($this->mbox,$mid); //imap_delete tags a message for deletion

		} // for multiple messages
			
		imap_expunge($this->mbox); // imap_expunge deletes all tagged messages
		imap_close($this->mbox);

		// now send the data to the server
		$this->exportEntries();
		
		return $this->importedMessageDataArray;
	}// function getdata

	function exportEntries()
	{
		foreach(  $this->importedMessageDataArray as $importedMessageData  )
		{	
			if( $importedMessageData["attachment"] )
			{
				$kshowinsertid = $importedMessageData["subject"];
				if ( $kshowinsertid == "" ) $kshowinsertid = 1;
				$insertype = $importedMessageData["attachment"]["type"];
				if ($insertype == "video")
					{ $mediatypecode = 1; }
					else if ( $insertype = "image" )
					{ $mediatypecode = 2; }
					else $mediatypecode = 0; // unknown type
				
				$fileforupload = $importedMessageData["attachment"]["filename"];
				$filethumbnail = $importedMessageData["attachment"]["thumbnail"];
				
				//$pieces = explode( '@', $importedMessageData["fromaddress"] );
				//$kusermobileid = $pieces[0];
				
				$mobileid = $importedMessageData["fromaddress"];
				
				// create a new curl resource
				$ch = curl_init();
				
				// set URL and other appropriate options
				if ( SF_ENVIRONMENT == 'prod' )
				{
					$prefix = 'index.php';
				}
				else $prefix = 'kaltura_dev.php';
				
				$serveraddr = $_SERVER && $_SERVER["SERVER_ADDR"] ? $_SERVER["SERVER_ADDR"] : "localhost";

				$urlstring = $serveraddr.'/'.$prefix.'/contribute/insertMobileEntry?kshow_id='.$kshowinsertid.'&entry_name=MobileEntry&entry_description=Entry_from_mobile_phone&entry_media_type='.$mediatypecode.'&entry_thumbnail='.$filethumbnail.'&entry_data='.$fileforupload.'&mobile_id='.$mobileid;
				
				//echo $urlstring;
				
				curl_setopt($ch, CURLOPT_URL, $urlstring);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
				curl_setopt($ch, CURLOPT_POST , TRUE); // make sure we're doing this using post
				
				// grab URL and pass it to the browser
				$response = curl_exec($ch);
				
				if (curl_errno($ch)) 
				{
					echo "Error while trying to connect to:". $urlstring."error=".curl_error($ch)."\n"; 
				}
				else 
				{
					echo "server response:".$response."\n";
					curl_close($ch);
				}
			} else echo 'Error - no attachment\n';
		}
	}//function exportEntries
	
	
	function saveAttachments( $mid )
	{
		$attachments = $this->locateAttachments();
		if ( !$attachments )
		{
			$this->importedMessageDataArray[$mid]["attachment"] = null;
			return;
		}

		$part = $this->getPartByPartNum( "1");

		// we will save only the first attachment, which is always in position 2
		$mege = imap_fetchbody($this->mbox, $mid, "2" );

		$ext = strtolower( $part->subtype );
		if ($ext == 'jpeg') $ext = 'jpg';

		$data=$this->getdecodevalue($mege,$part->encoding);

		$filename="$this->savedDirPath".$mid."_attach.".$ext;
		$fp=fopen($filename,"w");
		fputs($fp,$data);
		fclose($fp);

		// now create thumbnail images
		if ( $part->type == 5 ) // image
		{
			myFileConverter::createImageThumbnail( $filename, "$this->savedDirPath".$mid."_thumbnail1.jpg", "image2"  );
			$this->importedMessageDataArray[$mid]["attachment"]["thumbnail"] = $mid."_thumbnail1.jpg";
		}
		else if ( $part->type == 6 )// video
		{
			$thumbPrefix = myContentStorage::getFSUploadsPath().'_thumbnail';
			myFileConverter::captureFrame($filename, "$this->savedDirPath".$mid."_thumbnail%d.jpg", 1, "image2" );
			$this->importedMessageDataArray[$mid]["attachment"]["thumbnail"] = $mid."_thumbnail1.jpg";
		}
		else
		{
			$this->importedMessageDataArray[$mid]["attachment"]["thumbnail"] = "";
		}
		/*
		* TODO: handle the case for audio upload, or other cases where there is no image thumbnail
		*/

		$this->importedMessageDataArray[$mid]["attachment"]["type"] = $this->attachmenttype[$part->type];
		$this->importedMessageDataArray[$mid]["attachment"]["subtype"] = strtolower($part->subtype);
		$this->importedMessageDataArray[$mid]["attachment"]["filename"] = $mid."_attach.".$ext;
	}

	function getBody( $mid )
	{
		$plain = $this->locatePlain();

		if( !$plain )
		{
			return;
		}

		$part = $this->getPartByPartNum( $plain[0]-1 );

		if( $part )
		{
			$text = imap_fetchbody($this->mbox, $mid, $plain[0] );
			$this->importedMessageDataArray[$mid]["body"] = $this->getdecodevalue( $text, $part->encoding );
			if( $this->importedMessageDataArray[$mid]["subject"] == null ) $this->importedMessageDataArray[$mid]["subject"] = $this->importedMessageDataArray[$mid]["body"];
		}
	} // function getBody


	function printarray($array)
	{

		if ( is_array($array ) )
		{
			while(list($key,$value) = each($array))
			{
				if(is_array($value))
				{
			  echo $key."(array):<blockquote>";
			  $this->printarray($value);//recursief!!
			  echo "</blockquote>";
			 } elseif(is_object($value))
			 {
			  echo $key."(object):<blockquote>";
			  $this->printobject($value);
			  echo "</blockquote>";
			 }else
			 {
			 	echo $key."==>".$value."<br />";
			 }
			}
		}
		elseif( is_object($array) ) $this->printobject($array);
	}

	function printobject($obj)
	{
		$array = get_object_vars($obj);
		$this->printarray($array);
	}


	public function locatePlain() {
		if ( $this->structureObject == null) return;
		if (isset($this->structureObject->parts) && count($this->structureObject->parts) > 0 ) {
			return $this->findParts($this->structureObject->parts,null,'type',0,'subtype','PLAIN');
		} else {
			return array(1);
		}
	}

	public function locateHTML() {
		if ( $this->structureObject == null) return;
		if (count($this->structureObject->parts) > 0) {
			return $this->findParts($this->structureObject->parts,null,'type',0,'subtype','HTML');
		} else {
			return false;
		}
	}

	public function locateAttachments() {
		if ( $this->structureObject == null || ! isset($this->structureObject->parts)) return;
		if (count($this->structureObject->parts) > 0) {
			return $this->findParts($this->structureObject->parts,null,'disposition','attachment');
		} else {
			return false;
		}
	}

	private function checkParam($part, $type, $value) {
		switch ($type) {
			case 'type':
				return ($part->type == $value);
				break;
			case 'subtype':
				if ($part->ifsubtype) {
					return ($part->subtype == $value);
				} else {
					return false;
				}
				break;
			case 'description':
				if ($part->ifdescription) {
					return ($part->description == $value);
				} else {
					return false;
				}
				break;
			case 'disposition':
				if ($part->ifdisposition) {
					return  ($part->disposition == $value);
				} else {
					return false;
				}
				break;
			default:
				return false;
				break;
		}
	}

	private function findParts($partsArray, $prefix, $param1Type = 'type', $param1Value = 0, $param2Type = null, $param2Value = null, $param3Type = null, $param3Value = null) {
		if ( !$partsArray ) return null;
		$found = array();
		$i = 1;
		
		foreach ($partsArray as $key => $part) {
			if ($this->checkParam($part, $param1Type, $param1Value)) { //1 == true, go true
				if (isset($param2Type)) {
					if ($this->checkParam($part, $param2Type, $param2Value)) {  //2 == true, go true
						if (isset($param3Type)) {
							if ($this->checkParam($part, $param3Type, $param3Value)) { //3 == true, go true
								if (!is_null($prefix)) { $found[] = $prefix.".".$i; } else {$found[] = $i;}
							}
						} else { // 3 == null, go true
							if (!is_null($prefix)) { $found[] = $prefix.".".$i; } else {$found[] = $i;}
						}
					} // 2 == null, go true
				} else {
					if (!is_null($prefix)) { $found[] = $prefix.".".$i; } else {$found[] = $i;}
				}
			}
			if (isset($part->parts)) $found = array_merge($found , $this->findParts($part->parts,$prefix.".".$key+1,$param1Type,$param1Value,$param2Type,$param2Value,$param3Type,$param3Value));
			$i++;
		}
		return $found;
	}

	private function getPartByPartNum( $partNum )
	{
		if (isset($this->structureObject->parts) && count($this->structureObject->parts) > 0 )
		{
			$subNumbers = explode( '.', $partNum );
			if ( count ($subNumbers ) == 1 )
			{
				return $this->structureObject->parts[$subNumbers[0]];
			} else if ( count ($subNumbers ) == 2 )
			{
				$this->structureObject->parts[$subNumbers[0]][$subNumbers[1]];
			} else if ( count ($subNumbers ) == 3 )
			{
				$this->structureObject->parts[$subNumbers[0]][$subNumbers[1]][$subNumbers[2]];
			}
			return null;
		}
	}
	
	function getdecodevalue($message,$coding)
	{
		if ($coding == 0)
		{
			$message = imap_8bit($message);
		}
		elseif ($coding == 1)
		{
			$message = imap_8bit($message);
		}
		elseif ($coding == 2)
		{
			$message = imap_binary($message);
		}
		elseif ($coding == 3)
		{
			$message=imap_base64($message);
		}
		elseif ($coding == 4)
		{
			$message = imap_qprint($message);
		}
		elseif ($coding == 5)
		{
			$message = imap_base64($message);
		}
		return $message;
	}
	
}// class


