<?php
include_once("StringTokenizer.php");
include_once("KDLMediaDataSet.php");
include_once 'KDLUtils.php';

	/* ---------------------------
	 * KDLMediaInfoLoader
	 */
	class KDLMediaInfoLoader extends StringTokenizer {
		public function __construct(/*string*/ $str) {
			parent::__construct($str, "\t\n");
		}
		
		public function __destruct() {
			unset($this);
		}

		/* .........................
		 * Load
		 */ 
		function Load(&$dataSet) {
			$fieldCnt=0;
			$streamsCnt = 0;
			$streamsColStr = null;
			$section = "general";
			$sectionID = 0;
			while ($this->hasMoreTokens()) {
				$tok = strtolower(trim($this->nextToken()));
				if(strrpos($tok, ":") == false){
					$sectionID = strchr($tok,"#");
					if($sectionID) {
						$sectionID = trim($sectionID,"#"); 
					}
					else
						$sectionID = 0;

					if(strstr($tok,"general")==true)
						$section = "general";
					else if(strstr($tok,KDLConstants::VideoIndex)==true)
						$section = KDLConstants::VideoIndex;
					else if(strstr($tok,KDLConstants::AudioIndex)==true)
						$section = KDLConstants::AudioIndex;
					else if(strstr($tok,KDLConstants::ImageIndex)==true)
						$section = KDLConstants::ImageIndex;
					else	
						$section = $tok;
					$streamsCnt++;
					if($streamsColStr===null)
						$streamsColStr = $tok;
					else
						$streamsColStr = $streamsColStr.",".$tok;
				}
				else if($sectionID<=1) {
					$key =  trim(substr($tok, 0, strpos($tok, ":")) );
					$val =  trim(substr(strstr($tok, ":"),1));
					switch($section) {
					case "general":
						$this->loadContainerSet($dataSet->_container, $key, $val);
						break;
					case KDLConstants::VideoIndex:
						$this->loadVideoSet($dataSet->_video, $key, $val);
						break;
					case KDLConstants::ImageIndex:
						$this->loadVideoSet($dataSet->_image, $key, $val);
						break;
					case KDLConstants::AudioIndex:
						$this->loadAudioSet($dataSet->_audio, $key, $val);
						break;
					}
					$fieldCnt++;
				}
			}
			if($dataSet->_container!=null){
				$streamsColStr = "1+".$streamsCnt.":".$streamsColStr;
			}
			else
				$streamsColStr = "0+".$streamsCnt.":".$streamsColStr;
			$dataSet->_streamsCollectionStr = $streamsColStr;
			kLog::log("StreamsColStr- ".$dataSet->_streamsCollectionStr);
		}

		/* ------------------------------
		 * loadAudioSet
		 */
		private function loadAudioSet(&$audioData, $key, $val) {
			if($audioData=="")
				$audioData = new KDLAudioData();
			switch($key) {
			case "channel(s)":
				$audioData->_channels = KDLUtils::trima($val);
				settype($audioData->_channels, "integer");
				break;
			case "sampling rate":
				$audioData->_sampleRate = KDLUtils::trima($val);
				settype($audioData->_sampleRate, "float");
				if($audioData->_sampleRate<1000)
					$audioData->_sampleRate *= 1000;
				break;
			case "resolution":
				$audioData->_resolution = KDLUtils::trima($val);
				settype($audioData->_resolution, "integer");
				break;
			default:
				$this->loadBaseSet($audioData, $key, $val);
				break;
			}
		}

		/* .........................
		 * loadVideoSet
		 */
		private function loadVideoSet(&$videoData, $key, $val) {
			if($videoData=="")
				$videoData = new KDLVideoData();
			switch($key) {
			case "width":
				$videoData->_width = KDLUtils::trima($val);
				settype($videoData->_width, "integer");
				break;
			case "height":
				$videoData->_height = KDLUtils::trima($val);
				settype($videoData->_height, "integer");
				break;
			case "frame rate":
				$videoData->_frameRate = KDLUtils::trima($val);
				settype($videoData->_frameRate, "float");
				break;
			case "display aspect ratio":
				$val = KDLUtils::trima($val);
				if(strstr($val, ":")==true){
					$darW = trim(substr($val, 0, strpos($val, ":")) );
					$darH = trim(substr(strstr($val, ":"),1));
					if($darH>0)
						$videoData->_dar = $darW/$darH;
					else
						$videoData->_dar = null;
					
				}
				else if(strstr($val, "/")==true){
					$darW = trim(substr($val, 0, strpos($val, "/")));
					$darH = trim(substr(strstr($val, "/"),1));
					if($darW>0)
						$videoData->_dar = $darW/$darH;
					else
						$videoData->_dar = null;
				}
				else if($val) {
					$videoData->_dar = (float)$val;
				}
/*
				$val = $this->trima($val);
				if(strstr($val, ":")==true){
					$darW = trim(substr($val, 0, strpos($val, ":")));
					$darH = trim(substr(strstr($val, ":"),1));
					if($darW>0)
						$mediaInfo->videoDar = $darW / $darH;
					else
						$mediaInfo->videoDar = null;
				}
				else if(strstr($val, "/")==true){
					$darW = trim(substr($val, 0, strpos($val, "/")));
					$darH = trim(substr(strstr($val, "/"),1));
					if($darW>0)
						$mediaInfo->videoDar = $darW / $darH;
					else
						$mediaInfo->videoDar = null;
				}
				else if($val) {
					$mediaInfo->videoDar = (float)$val;
				}
				break;

 */
				break;
			case "rotation":
				$videoData->_rotation = KDLUtils::trima($val);
				settype($videoData->_rotation, "integer");
				break;
			case "scan type":
				$scanType = KDLUtils::trima($val);
				if($scanType!="progressive") {
					$videoData->_scanType=1;
				}
				else {
					$videoData->_scanType=0;
				}
//				settype($videoData->_rotation, "integer");
				break;
			default:
				$this->loadBaseSet($videoData, $key, $val);
				break;
			}
		}

		/* .........................
		 * loadContainerSet
		 */
		private function loadContainerSet(&$containerData, $key, $val) {
			if($containerData=="")
				$containerData = new KDLContainerData();
			switch($key) {
			case "file size":
				$containerData->_fileSize = KDLUtils::convertValue2kbits(KDLUtils::trima($val));
				break;
			case "complete name":
				$containerData->_fileName = $val;
				break;
			default:
				$this->loadBaseSet($containerData, $key, $val);
				break;
			}
		}
		
		// .........................
		// loadBaseSet
		//
		private function loadBaseSet(&$baseData, $key, $val) 
		{
			switch($key) {
			case "codec id":
				$baseData->_id = $val;
				break;
			case "format":
				$baseData->_format = $val;
				break;
			case "duration":
				$baseData->_duration = KDLUtils::convertDuration2msec($val);
				break;
			case "bit rate":
				$baseData->_bitRate = KDLUtils::convertValue2kbits(KDLUtils::trima($val));
				break;
			default:
	//echo "<br>". "key=". $key . " val=" . $val . "<br>";
				$baseData->_params[$key] = $val;
				break;
			}
		}
	
	}

?>