<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class rawAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id
	 */
	public function execute()
	{
		requestUtils::handleConditionalGet();
		
		$entry_id = $this->getRequestParameter( "entry_id" );
		$type = $this->getRequestParameter( "type" );
		$ks = $this->getRequestParameter( "ks" );
		$file_sync = null;
		$ret_file_name = "name";
		$referrer = $this->getRequestParameter("referrer");
		$referrer = base64_decode($referrer);
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = "";
		
		$request_file_name = $this->getRequestParameter( "file_name" );
		if($request_file_name)
			$ret_file_name = $request_file_name;
		
		$direct_serve = $this->getRequestParameter( "direct_serve" );
	
		$entry = null;
		
		if($ks)
		{
			try {
				kCurrentContext::initKsPartnerUser($ks);
			}
			catch (Exception $ex)
			{
				KExternalErrors::dieError(KExternalErrors::INVALID_KS);
			}
		}
		else
		{
			$entry = kCurrentContext::initPartnerByEntryId($entry_id);
			if(!$entry)
				KExternalErrors::dieGracefully();
		}
		
		kEntitlementUtils::initEntitlementEnforcement();
		
		if ( ! $entry )
		{
			$entry = entryPeer::retrieveByPK( $entry_id );
			
			if(!$entry)
				KExternalErrors::dieGracefully();
		}
		else
		{
			if(!kEntitlementUtils::isEntryEntitled($entry))
				KExternalErrors::dieGracefully();
		}

		myPartnerUtils::blockInactivePartner($entry->getPartnerId());
		
		$securyEntryHelper = new KSecureEntryHelper($entry, $ks, $referrer, ContextType::DOWNLOAD);
		$securyEntryHelper->validateForDownload();

		// relocate = did we use the redirect and added the extension to the name
		$relocate = $this->getRequestParameter ( "relocate" );
		
		if ($ret_file_name == "name")
			$ret_file_name = $entry->getName();
			
		if ($ret_file_name)
		{
			//rawurlencode to content-disposition filename to handle spaces and other characters across different browsers
			//$name = rawurlencode($ret_file_name);
			// 19.04.2009 (Roman) - url encode is not needed when the filename in Content-Disposition header is in quotes
			// IE6/FF3/Chrome - Will show the filename correctly
			// IE7 - Will show the filename with underscores instead of spaces (this is better than showing %20)
			$name = $ret_file_name;
			if ($name)
			{
				if( $relocate )
				{
					// if we have a good file extension (from the first time) - use it in the content-disposition
					// in some browsers it will be stronger than the URL's extension
					$file_ext = pathinfo ( $relocate , PATHINFO_EXTENSION );
					$name .= ".$file_ext";
				}
				$name = kString::removeNewLine($name);
				if(!$direct_serve)
				{
					$entry_data = $entry->getData();
					if(strpos($name , ".") === false && !is_null($entry_data))
					{
						$file_ext = pathinfo($entry_data, PATHINFO_EXTENSION);
						$image_extensions = kConf::get('image_file_ext');
						if ($file_ext && in_array($file_ext, $image_extensions))
							$name .= '.' . $file_ext;
					}
					header("Content-Disposition: attachment; filename=\"$name\"");
				}
			}
		}
		else
		{
			$ret_file_name = $entry_id;
			$name = $ret_file_name;
		}
		$name = str_replace(array("\t", "\r", "\n"), array(' ', '', ' '), $name);
		$format = $this->getRequestParameter( "format" );
		
		if ( $type == "download" && $format && $entry->getType() != entryType::DOCUMENT) // mediaType is not relevant when requesting download with format
		{
			// this is a video for a specifc extension - use the proper flavorAsset
			$flavor_asset = $this->getAllowedFlavorAssets( $securyEntryHelper, $entry_id , $format );
			if($flavor_asset && $flavor_asset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
			{
				$file_sync = $this->redirectIfRemote ( $flavor_asset ,  flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET , null , true , $name);
			}
			else
			{
				header('KalturaRaw: no flavor asset for extension');
				header("HTTP/1.0 404 Not Found");
				KExternalErrors::dieGracefully();
			}

			$archive_file = $file_sync->getFullPath();
			$mime_type = kFile::mimeType( $archive_file );
						
			kFileUtils::dumpFile($archive_file, $mime_type, null, 0, $file_sync->getEncryptionKey(), $file_sync->getIv(), $file_sync->getFileSize());
		}
		
		// TODO - move to a different action - document should be plugin
		if ($entry->getType() == entryType::DOCUMENT)
		{
			// use the fileSync from the entry
			if($type == "download" && $format)
			{
				$flavor_asset = $this->getAllowedFlavorAssets( $securyEntryHelper, $entry_id , $format );
			}
			else
			{
				$flavor_asset = $this->getAllowedFlavorAssets( $securyEntryHelper, $entry_id , null, true );
			}
			
			if($flavor_asset && $flavor_asset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
			{
				$file_sync = $this->redirectIfRemote ( $flavor_asset ,  flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET , null , true, $name);
			}
			else
			{
				header('KalturaRaw: no flavor asset for extension');
				header("HTTP/1.0 404 Not Found");
				KExternalErrors::dieGracefully();
			}
			// Gonen 2010-08-05 workaround to make sure file name includes correct extension
			// make sure a file extension is added to the downloaded file so browser will identify and
			// allow opening with default program
			// for direct serve we do not want to send content-disposition header
			if(!$direct_serve)
			{
				$ext = pathinfo($file_sync->getFullPath(), PATHINFO_EXTENSION);
				if($relocate)
				{
					// remove relocate file extension
					$reloc_ext = pathinfo ( $relocate , PATHINFO_EXTENSION );
					$name = str_replace(".$reloc_ext", '', $name);
				}
				$name = kString::removeNewLine($name. '.' .$ext);
				header("Content-Disposition: attachment; filename=\"$name\"");
			}
			kFileUtils::dumpFile($file_sync->getFullPath(), null, null, 0, $file_sync->getEncryptionKey(), $file_sync->getIv(), $file_sync->getFileSize());
		}
		elseif ($entry->getType() == entryType::DATA)
		{
			$version = $this->getRequestParameter("version");
			$syncKey = $entry->getSyncKey(kEntryFileSyncSubType::DATA, $version);
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
			
			$path = null;
			if($local)
			{
				$path = $fileSync->getFullPath();
			}
			elseif($fileSync)
			{
				$path = kDataCenterMgr::getRedirectExternalUrl($fileSync);
				header("Location: $path");
				KExternalErrors::dieGracefully();
			}
			
			if (!$path)
			{
				header('KalturaRaw: no data was found available for download');
				header("HTTP/1.0 404 Not Found");
			}
			else
			{
				kFileUtils::dumpFile($path, null, null, 0, $fileSync->getEncryptionKey(), $fileSync->getIv(), $fileSync->getFileSize());
			}
		}
		
		//$archive_file = $entry->getArchiveFile();
		$media_type =  $entry->getMediaType();
		if ( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			// image - use data for entry
			$file_sync = $this->redirectIfRemote ( $entry ,  kEntryFileSyncSubType::DATA , null, true, $name);
			$key = $entry->getSyncKey(kEntryFileSyncSubType::DATA);
			kFileUtils::dumpFile(kFileSyncUtils::getLocalFilePathForKey($key, true),  null, null, 0, $file_sync->getEncryptionKey(), $file_sync->getIv(), $file_sync->getFileSize());
		}
		elseif ( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO || $media_type == entry::ENTRY_MEDIA_TYPE_AUDIO  )
		{
			$format = $this->getRequestParameter( "format" );
			if ( $type == "download" && $format )
			{
				// this is a video for a specifc extension - use the proper flavorAsset
				$flavor_asset = $this->getAllowedFlavorAssets( $securyEntryHelper, $entry_id , $format );
				if($flavor_asset && $flavor_asset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				{
					$file_sync = $this->redirectIfRemote ( $flavor_asset ,  flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET , null , true , $name);
				}
				else
				{
					header('KalturaRaw: no flavor asset for extension');
					KExternalErrors::dieGracefully();
				}
				
				$archive_file = $file_sync->getFullPath();
			}
			else
			{
				// flavorAsset of the original
				$flavor_asset = $this->getAllowedFlavorAssets( $securyEntryHelper, $entry_id , null, true );
				if($flavor_asset && $flavor_asset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				{
					$file_sync = $this->redirectIfRemote ( $flavor_asset ,  flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET , null , false , $name); // NOT strict - if there is no archive, get the data version
					if ( $file_sync )
					{
						$archive_file = $file_sync->getFullPath();
					}
				}
				
				if(!$flavor_asset || !$file_sync || $flavor_asset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
				{
					// either no archive asset or no fileSync for archive asset
					// use fallback flavorAsset
					$flavor_asset = $this->getAllowedFlavorAssets( $securyEntryHelper, $entry_id , null, false, true );
					if(!$flavor_asset)
					{
						header('KalturaRaw: no original flavor asset for entry, no best play asset for entry');
						KExternalErrors::dieGracefully();
					}
					$file_sync = $this->redirectIfRemote ( $flavor_asset ,  flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET , null , false , $name); // NOT strict - if there is no archive, get the data version
					if(!$file_sync)
					{
						header('KalturaRaw: no file sync found for flavor ['.$flavor_asset->getId().']');
						KExternalErrors::dieGracefully();
					}
					$archive_file = $file_sync->getFullPath();
				}
			}
		}
		elseif ( $media_type == entry::ENTRY_MEDIA_TYPE_SHOW )
		{
			// in this case "raw" is a bad name
			// TODO - add the ability to fetch the actual XML by flagging "xml" or something
			$version = $this->getRequestParameter ( "version" );
			
			// hotfix - links sent after flattening is done look like:
			// http://cdn.kaltura.com/p/387/sp/38700/raw/entry_id/0_ix99151g/version/100001
			// while waiting for flavor-adaptation in flattening, we want to find at least one file to return.
			$try_formats = array('mp4', 'mov', 'avi', 'flv');
			if($format)
			{
				$key = array_search($format, $try_formats);
				if($key !== FALSE)
					unset($try_formats[$key]);
				
				$file_sync = $this->redirectIfRemote( $entry , kEntryFileSyncSubType::DOWNLOAD, $format, false, $name);
			}
			
			if(!isset($file_sync) || !$file_sync || !file_exists($file_sync->getFullPath()))
			{
				foreach($try_formats as $ext)
				{
					KalturaLog::log( "raw for mix - trying to find filesync for extension: [$ext] on entry [{$entry->getId()}]");
					$file_sync = $this->redirectIfRemote( $entry , kEntryFileSyncSubType::DOWNLOAD, $ext, false, $name);
					if($file_sync && file_exists($file_sync->getFullPath()))
					{
						KalturaLog::log( "raw for mix - found flattened video of extension: [$ext] continuing with this file {$file_sync->getFullPath()}");
						break;
					}
				}
				if(!$file_sync || !file_exists($file_sync->getFullPath()))
				{
					$file_sync = $this->redirectIfRemote( $entry , kEntryFileSyncSubType::DOWNLOAD, $ext, true, $name);
				}
			}

			// if got to here - fileSync was found for one of the extensions - continue with that file
			$archive_file = $file_sync->getFullPath();
		}
		else
		{
			// no archive for this file
			header("HTTP/1.0 404 Not Found");
			KExternalErrors::dieGracefully();
		}

//		echo "[$archive_file][" . file_exists ( $archive_file ) . "]";
		$mime_type = kFile::mimeType( $archive_file);
//		echo "[[$mime_type]]";


		$shouldProxy = $this->getRequestParameter("forceproxy", false);
		if($shouldProxy || !empty($relocate))
		{
			// dump the file
			if(isset($file_sync) && $file_sync && in_array($file_sync->getDc(), kDataCenterMgr::getSharedStorageProfileIds()))
			{
				$archive_file = $file_sync->getRemotePath();
			}
			kFileUtils::dumpFile($archive_file , $mime_type );
			KExternalErrors::dieGracefully();
		}
		
		// use new Location to add the best extension we can find for the file
		$file_ext = pathinfo ( $archive_file , PATHINFO_EXTENSION );
		if ( $file_ext != "flv" )
		{
			// if the file does not end with "flv" - it is the real extension
			$ext = $file_ext;
		}
		else
		{
			// for now - if "flv" return "flv" - // TODO - find the real extension from the file itself
			$ext = "flv";
		}
		
		// rebuild the URL and redirect to it with extraa parameters
		$url = $_SERVER["REQUEST_URI"];
		$format = $this->getRequestParameter( "format" );
		if ( ! $format )
		{
			$url = str_replace( "format" , "" , $url );
		}
		
		if ( !$ret_file_name)
		{
			// don't leave the name empty - if it is empty - use the entry id
			$ret_file_name = $entry_id;
		}
		
		$ret_file_name_safe = str_replace(' ', '-', $ret_file_name); // spaces replace with "-"
		$ret_file_name_safe = preg_replace('/[^a-zA-Z0-9-_]/', '', $ret_file_name_safe); // only "a-z", "A-Z", "0-9", "-" & "_" are left

		if ( strpos ($url , "?" ) > 0 ) // replace BEFORE the query string
		{
			$url = str_replace( "?" , "/$ret_file_name_safe.{$ext}?" ,  $url );
			$url .= "&relocate=f.{$ext}"; // add the ufname as a query parameter
		}
		else
		{
			$url .= "/$ret_file_name_safe.{$ext}?relocate=f.{$ext}";   // add the ufname as a query parameter
		}
		
		// or redirect if no proxy
		header ( "Location: {$url}" );
		KExternalErrors::dieGracefully();
	}

	/**
	 * @param $obj
	 * @param $sub_type
	 * @param $version
	 * @param bool $strict
	 * @param null $fileName
	 * @return mixed|null
	 * @throws PropelException
	 * @throws sfStopException
	 */
	private function redirectIfRemote ( $obj , $sub_type , $version , $strict = true , $fileName = null)
	{
		$dataKey = $obj->getSyncKey( $sub_type , $version );
		list ( $file_sync , $local ) = kFileSyncUtils::getReadyFileSyncForKey( $dataKey ,true , false );
		
		if ( ! $file_sync )
		{
			if ( $strict )
			{
				// file does not exist on any DC - die

				KalturaLog::log( "Error - no FileSync for object [{$obj->getId()}]");
				header("HTTP/1.0 404 Not Found");
				KExternalErrors::dieGracefully();
			}
			else
				return null;
		}
		
		return $this->redirectFileSyncIfRemote($file_sync, $local, $obj, $fileName);
	}

	/**
	 * @param $file_sync
	 * @param $local
	 * @param $object
	 * @param null $fileName
	 * @return mixed
	 * @throws sfStopException
	 */
	private function redirectFileSyncIfRemote($file_sync, $local, $object, $fileName = null)
	{
		if(kFile::isSharedPath($file_sync->getFullPath()) || in_array($file_sync->getDc(), kStorageExporter::getPeriodicStorageIds()))
		{
			if($object instanceof asset)
			{
				$downloadDeliveryProfile = myPartnerUtils::getDownloadDeliveryProfile($file_sync->getDc(), $object->getEntryId());
				if($downloadDeliveryProfile && $object)
				{
					$isDir = kFile::isDir($file_sync->getFullPath());
					$url = $this->getDownloadRedirectUrl($downloadDeliveryProfile, $object, $fileName, $isDir, $file_sync);
					$this->redirect($url);
				}
			}
		}

		if ( !$local )
		{
			$shouldProxy = $this->getRequestParameter("forceproxy", false);
			$remote_url = kDataCenterMgr::getRedirectExternalUrl ( $file_sync , $_SERVER['REQUEST_URI'] );
			KalturaLog::log ( __METHOD__ . ": redirecting to [$remote_url]" );
			if($shouldProxy)
			{
				kFileUtils::dumpUrl($remote_url);
			}
			else
			{
				// or redirect if no proxy
				$this->redirect($remote_url);
			}
		}
		
		return $file_sync;
	}

	protected function getDownloadRedirectUrl($downloadDeliveryProfile, $flavorAsset, $fileName, $isDir, $file_sync)
	{
		if($fileName)
		{
			$ext = pathinfo($file_sync->getFullPath(), PATHINFO_EXTENSION);
			$fileName = kString::removeNewLine($fileName. '.' .$ext);
			$fileName = kString::stripInvalidUrlChars($fileName);
			$fileName = rawurlencode($fileName);
		}
		$url = $flavorAsset->getServeFlavorUrl(null, $fileName, $downloadDeliveryProfile, $isDir);
		KalturaLog::log ("URL to redirect to [$url]" );
		return $url;
	}
	
	private function getAllowedFlavorAssets(KSecureEntryHelper $secureEntryHelper, $entryId, $format = null, $isOriginal = false, $isBestPlay = false)
	{
		$flavorAsset = null;
		
		if($isBestPlay)
		{
			$flavorAssets = assetPeer::retrieveReadyWebByEntryId($entryId);
		}
		else
		{
			$c = new Criteria();
			$c->add(assetPeer::ENTRY_ID, $entryId);
			if($format)
				$c->add(assetPeer::FILE_EXT, $format);
			if($isOriginal)
				$c->add(assetPeer::IS_ORIGINAL, true);
			
			$flavorAssets =  assetPeer::doSelect($c);
		}

		foreach ($flavorAssets as $currentFlavorAsset)
		{
			if($secureEntryHelper->isAssetAllowed($currentFlavorAsset))
			{
				$flavorAsset = $currentFlavorAsset;
				break;
			}
		}
		return $flavorAsset;
	}
}
