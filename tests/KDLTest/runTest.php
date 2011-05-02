<?

Include_once 'runTestAux.php';
require_once 'K:\opt\kaltura\app\infra\cdl\kOperator.php';
require_once 'K:\opt\kaltura\app\infra\cdl\kOperatorSets.php';

include_once("K:\opt\kaltura\app\infra\cdl\kdl\KDLMediaInfoLoader.php");
include_once('K:\opt\kaltura\app\infra\cdl\kdl\KDLProcessor.php');
include_once 'K:\opt\kaltura\app\infra\cdl\kdl\KDLUtils.php';
include_once 'K:\opt\kaltura\app\infra\cdl\kdl\KDLWrap.php';
include_once 'K:\opt\kaltura\app\infra\cdl\kdl\KDLTest.php';
Include_once 'K:\opt\kaltura\app\plugins/quick_time_tools/lib/KDLTranscoderQTPTools.php';
Include_once 'K:\opt\kaltura\app\plugins/fast_start/lib/KDLOperatorQTFastStart.php';
Include_once 'K:\opt\kaltura\app\plugins/expression_encoder/lib/KDLOperatorExpressionEncoder.php';
Include_once 'K:\opt\kaltura\app\plugins/inlet_armada/lib/KDLOperatorInletArmada.php';
include_once('K:\opt\kaltura\app\alpha\lib\BaseEnum.php');
include_once('K:\opt\kaltura\app\alpha\lib\enums\conversionEngineType.php');
//include_once('K:\opt\kaltura\app\admin_console\lib\Kaltura\KalturaClient.php');
//include_once('K:\opt\kaltura\app\admin_console\lib\Kaltura\KalturaClientBase.php');


define("MediaInfoProgram", "mediainfo");
define("ContentDir", "C:\\Anatol\\Work\\Kaltura\\Media");

	/* =========================================================================
	 * MAIN
	 */
/* 
	 9spkxiz8m4_100007.mp4 
	 e5u7e2hzia 116487.flv 
	 avi+mpeg4+++angel_decomb.avi M*HD-2Blade.Runner.1982.Final.Cut.720p.mkv.avi
	 big_buck_bunny_720p_surround.avi
	 Sample556.m2ts
	 Kuzi\\ff4_480.mov
	 NotSupported\DVC.wzjujipkb1.mov
	 00_ondveoeu7o_6331_1
	 00_vr8gv4colh_752_1.flv
	 ocyuvkbbx4.mov
	 53038943001_54779358001_VS1-E1-Sc4-YouTube-HQ---1-5.flv
	 Mammoth*.mov
	 VP6.mh3dcp57bw.flv - no Audio
	 o1xosfknow.mp3.aiff.mp3
	 FLYING_P.MP3
	 C:\Anatol\Work\Kaltura\Media\NotSupported\ALAC.mov++alac++ALAC_6ch.mov
	 TestVideo.flv
	 webcam.slova3mhmy.flv
	 avi+h263+++h263p_artifacts1.avi
	 avi+indeo3+++1-dog.avi
	 Serena_Williams_threatens_a_line_judge(HQ).HQ.mp4
	 20090317180556.m2ts
	 lkjk9j6wco.avi
	 Avatar-1080p.mov(Source).mov
	 0_8veiqmna.flv
	 0_gz7q5qj6.mov
	 0_pmje1q9j.avi
	 0_l9355ult.mp4 - zero
	 0_o6q4j49k.3gp - zeroed frame size
	 Rotated.0_22ta0dw2.mov
	 0_ybws36sj.flv - 
	 NotSupported\MultiVideo.0_0a03bfsu.mov
	 NoAudio.0_iywkt4tj.mp4
	 0_4zt33loq.flv - vp6
	 0_myw8j6ev.wma
	 Rotated.0_22ta0dw2.mov
	 Canon.Rotated.0_qaqsufbl.avi
	 MULTI_STREAMS.e6y9ss9374.flv.mov
	 MULTI_STREAM.0_p5ds1pr8.wmv
	 MSSpeech.0_r6qkhpy6.wmv
	 NotSupported\ICOD.3203qlvb1w.mov
	 6ch.mp4
	 FLV_NELLIMOSER.0_v4lcudxr.flv
	 Real test file.rm
	 stuff.wav.wav
	 How_to_Access_my_Contacts.mp4 - low br, presentation
	 0_2leylrz6.rm
	 0_apdhpdiz_0_tu8lodt3_1.mp3 - invalid mediainfo
	 VP6.mh3dcp57bw.flv
*/

	//testOperatorJSON();
	//return
	
//	runXMLtest("K:\Media\join\\mixTest.xml");
//	print_r(FramesizeToAspectRatio(1282, 850));
	runWrapperTest(ContentDir, "Serena_Williams_threatens_a_line_judge(HQ).HQ.mp4");
//	runStandaloneTest("K:\Media", "TestVideo.flv");
//	parsePlayList("k:/web/content/r31v1/entry/data/4/268/0_kjzze1dp_0_zewz6krv_2.appl/playlist.m3u8", "zzz");
	
	return;
	/*
	 * MAIN
	 *==========================================================/

	/* ------------------------------
	 * function mediaTestStub
	 */
	function mediaTestStub($fileName, $flavorList) 
	{
		$dlPrc = new KDLProcessor();
		KDLTest::runFileTest($fileName, $dlPrc, $flavorList);
		print_r($flavorList->_flavors[0]);
		$cdlFlavor=KDLWrap::ConvertFlavorKdl2Cdl($dlPrc->_targets[0]);

		echo "<br>\n";
	}

	/* ------------------------------
	 * function mediaTestStub
	 */
	function mediaTestStub1($fileName, $flavorList) 
	{
		kLog::log($fileName);
		$str = shell_exec(MediaInfoProgram." ". realpath($fileName));
		$mdLoader = new KDLMediaInfoLoader($str);
		$mediaInfoObj1 = new KDLMediaDataSet();
		$mdLoader->Load($mediaInfoObj1);
		kLog::log($mediaInfoObj1->ToString());

		$str = shell_exec(MediaInfoProgram." ". realpath($fileName));
		$mdLoader = new KDLMediaInfoLoader($str);
		$mediaInfoObj2 = new KDLMediaDataSet();
		$mdLoader->Load($mediaInfoObj2);
		$mediaInfoObj2->_video->_id="aaa";
		kLog::log($mediaInfoObj2->ToString());
		
		if($mediaInfoObj1==$mediaInfoObj2)
			kLog::log("Equals");
		else
			kLog::log("NOT Equals");
		
		echo "<br>\n";
	}
/*
cdl
KDLFlavor.php (9 matches)
52: $this->_warnings[KDLConstants::VideoIndex][]="Redundant bitrate";  
110: $product->_warnings[KDLConstants::VideoIndex][] = "Product duration too short - ".($prdVid->_duration/1000)."sec, required - ".($srcVid->_duration/1000)."sec.";  
113: $product->_warnings[KDLConstants::VideoIndex][] = "Product bitrate too low - ".$prdVid->_bitRate."kbps, required - ".$trgVid->_bitRate."kbps.";  
128: $product->_warnings[KDLConstants::AudioIndex][] = "Product duration too short - ".($prdAud->_duration/1000)."sec, required - ".($srcAud->_duration/1000)."sec.";  
131: $product->_warnings[KDLConstants::AudioIndex][] = "Product bitrate too low - ".$prdAud->_bitRate."kbps, required - ".$trgAud->_bitRate."kbps.";  
201: $this->_warnings[KDLConstants::VideoIndex][] = "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";  
250: $this->_warnings[KDLConstants::VideoIndex][] =   
403: $this->_warnings[$keyPart][] = "The transcoder (".$key.") can not process the (".$sourcePart->_id."/".$sourcePart->_format. ").";  
416: $this->_warnings[KDLConstants::VideoIndex][] = "The transcoder (".$key.") does not handle properly DAR<>PAR.";  
KDLMediaDataSet.php
51: $warnings[KDLConstants::VideoIndex][] = "Invalid bitrate value. Set to defualt ".$this->_video->_bitRate;  
KDLMediaObjectData.php (4 matches)
85: $warnings[$section][] = "Invalid duration (" . $this->_duration . "msec)";  
89: $warnings[$section][] = "Invalid bitrate (" . $this->_bitRate . "kbps)";  
243: $warnings[KDLConstants::VideoIndex][] = "Invalid frame rate (" . $this->_frameRate . "fps)";  
247: $warnings[KDLConstants::VideoIndex][] = "Invalid DAR (" . $this->_dar. ")";  */

	/* ---------------------------
	 * testOperatorJSON
	 */
	function testOperatorJSON()
	{
		if(1){
require_once '../kOperator.php';
require_once '../kOperatorSets.php';
//	$transObjArr=KDLUtils::parseTranscoderList("(1),(2#99#5),98,4,1","(--FE_SOMETING 0)|(-b 30#-a 23 -c ee# vcodec;c:7)|--FE_AAA 1234||101");
//print_r($transObjArr);
//print_r($transObjArr);
$json2 = '[[{"id":2}]]';
$json3 = '[[{\"id\":2,\"extra\":null,\"command\":\"-i __inFileName__ -vcodec flv -b 251k -s 320x240 -r 14.96 -g 60 -acodec libmp3lame -ab 64k -ar 44100 -ac 2 -f flv -y __outFileName__\"},{\"id\":98,\"extra\":null,\"command\":\"-i __inFileName__ -vcodec flv -b 251k -s 320x240 -r 14.96 -g 60 -acodec libmp3lame -ab 64k -ar 44100 -ac 2 -f flv -y __outFileName__\"}]]';
$json4 = '[[{"id":2,"extra":null,"command":"-i __inFileName__ -vcodec flv -b 251k -s 320x240 -r 14.96 -g 60 -acodec libmp3lame -ab 64k -ar 44100 -ac 2 -f flv -y __outFileName__"},{"id":98,"extra":null,"command":"-i __inFileName__ -vcodec flv -b 251k -s 320x240 -r 14.96 -g 60 -acodec libmp3lame -ab 64k -ar 44100 -ac 2 -f flv -y __outFileName__"}]]';
$json = '
[
	[
		{
			"id":6,
			"extra":"A extra params 1 ",
			"command":"A command line data 1",
			"azaz":12345
		}
	],
	[
		{
			"id":1,
			"extra":"A extra params 1 ",
			"command":"A command line data 1"
		},
		{
			"id":2,
			"extra":"A extra params 2",
			"command":"A command line data 2"
		}
	],
	[
		{
			"id":1,
			"extra":"B extra params 1 ",
			"command":"B command line data 1"
		},
		{
			"id":2,
			"extra":"B extra params 2",
			"command":"B command line data 2"
		}
	]
]';
$oprSets = new kOperatorSets();
$oprSets->setSerialized(stripslashes($json));
			print_r($oprSets);
			echo "<br>\n\n";
			echo "333333333333333333";
			return;
$transObjArr = KDLWrap::convertOperatorsCdl2Kdl($json);
			KDLUtils::RecursiveScan($transObjArr, "transcoderSetFuncTest", KDLConstants::$TranscodersCdl2Kdl, "");
echo "<br>\n\n";
print_r($transObjArr);
$cdlOprSets = new kOperatorSets;
//		$cdlOprSets = KDLWrap::convertOperatorsKdl2Cdl($transObjArr);
//		print_r($cdlOprSets);
//return;
			foreach($transObjArr as $transObj) {
				$auxArr = array();
				if(is_array($transObj)) {
					foreach($transObj as $tr) {
						$opr = new kOperator();
						$key=array_search($tr->_id,KDLWrap::$TranscodersCdl2Kdl);
						if($key===false){
							$opr->id = $tr->_id;
						}
						else{
							$opr->id = $key;
						}
						$opr->extra = $tr->_extra;
						$opr->command = $tr->_cmd;
						$auxArr[] = $opr;
					}
				}
				else {
					$opr = new kOperator();
					$key=array_search($transObj->_id,KDLWrap::$TranscodersCdl2Kdl);
					if($key===false){
						$opr->id = $transObj->_id;
					}
					else{
						$opr->id = $key;
					}
					$opr->extra = $transObj->_extra;
					$opr->command = $transObj->_cmd;
					$auxArr[] = $opr;
				}
				$cdlOprSets->addSet($auxArr);
			}
			print_r($cdlOprSets);
		
			return;
		}
	}

	/* ---------------------------
	 * getMediasetFromFile($filename)
	 */
	function getMediasetFromFile($filename)
	{
		$mediaInfoStr = shell_exec(MediaInfoProgram." ". realpath($filename));
		$mdLoader = new KDLMediaInfoLoader($mediaInfoStr);
		$mediaSet = new KDLMediaDataSet();
		$mdLoader->Load($mediaSet);
		return $mediaSet;
	}
	
	/* ---------------------------
	 * runStandaloneTest
	 */
	function runStandaloneTest($contentDir, $patern)
	{
////////////////////////////////////////////
// Nokia suited flavor
//$profile->_flavors[] = KDLTest::simulateFlavor(KDLVideoTarget::MP4, KDLVideoTarget::MPEG4, 640, 360, 600);


		$profile = new KDLProfile();
		$profile->_flavors[] = KDLTest::simulateFlavor(
		KDLContainerTarget::MP4, KDLVideoTarget::H264, 
			0,  352, 2500,KDLAudioTarget::AAC,0,22050, 100,0, "2");
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLContainerTarget::ISMV, KDLVideoTarget::WVC1A, 0,  352, 4000,"aac");
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLContainerTarget::MP4, KDLVideoTarget::H264B, 0,  280, 600);
	//	$profile->_flavors[0]->_audio=null;
	
	//	$profile->_flavors[] = KDLTest::simulateFlavor("mp3", KDLAudioTarget::MP3, 0,  0, 0);
//	$profile->_flavors[1]->_video=null;
	
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLContainerTarget::ISMV, null, 0, 1080, 4000);
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLContainerTarget::ISMV, null, 0,  720, 2500);
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLVideoTarget::FLV, null, 0,  720, 1350);
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLVideoTarget::FLV, null, 0,  288, 750);
//	$profile->_flavors[] = KDLTest::simulateFlavor(KDLVideoTarget::FLV, null, 0,  288, 400);
	
//$profile->_flavors[0]->_video=null;
//	$profile->_flavors[] = KDLTest::simulateFlavor("mp4", null, 0, 720, 1350);
//	$profile->_flavors[] = KDLTest::simulateFlavor("mp4", null, 0/*512*/,  288,  750);
//	$profile->_flavors[] = KDLTest::simulateFlavor("mp4", null, 0/*512*/,  288,  400);
echo "<br>\n-----------<br>\nProfile<br>\n----------<br>\n";
		echo $profile->ToString()."<br>\n";

//	C:\\xampp\htdocs\mvdr6t454m.mov 
/*
		$exeStr = MediaInfoProgram." C:\Anatol\Work\Kaltura\Media\\0_myw8j6ev.wma";
		$mediaInfoStr = shell_exec($exeStr);// $mediaInfoSample6; //
echo $mediaInfoStr."\n";
		$mdLoader = new KDLMediaInfoLoader($mediaInfoStr);
		$mediaInfoObj = new KDLMediaDataSet();
		$mdLoader->Load($mediaInfoObj);
		print_r($mediaInfoObj);
		if($mediaInfoObj->IsDataSet()) {
			echo "IsDataSet - true\n";
		}
		else {
			echo "IsDataSet - false\n";
		}
//		$tagList[] = "itunes";
		$tagList[] = "web";
		$tagList[] = "mbr";
		$tagList[] = "flv";
		$tagList[] = "slweb";
		echo "<br>\nIn tags-->";
		print_r($tagList);
$profile->_flavors[0]->_tags = $tagList;		
//		$tagsOut = KDLFlavor2Tags::ToTags($mediaInfoObj, $profile->_flavors[0]);
		$tagsOut = $mediaInfoObj->ToTags($tagList);
		if(count($tagsOut)==1)
			echo "Found";
		else
			echo "Not found";
		
		echo "<br>\nOut tags-->";
		print_r($tagsOut);
return;
*/
		
		/*
		 * 
		 */

		echo "<br>\n";
		$dlPrc = new KDLProcessor();
		KDLTest::runFileTest("$contentDir\\$patern", $dlPrc, $profile);
		print_r($profile->_flavors[0]);

		echo "<br>\n";
		$kdlMedSet = getMediasetFromFile("$contentDir\\$patern");
		
		return;
			
	}

	/* ---------------------------
	 * 
	 */
	function runWrapperTest($contentDir, $patern)
	{
		echo "<br>\n";
		$kdlSrcMedSet = getMediasetFromFile("$contentDir\\$patern");
		
		$cdlSrcMedInf = new mediaInfo();
		$cdlSrcMedInf->LoadFromMediaset($kdlSrcMedSet);
		$cdlFlavors []=new flavorParams();
		$cdlFlavors[0]->simulate(KDLContainerTarget::MP4, KDLVideoTarget::H264B, 
						0,  352, 2500,KDLAudioTarget::AAC,96,22050,"2,3");
$cdlTargets;
		$cdlTargets = KDLWrap::CDLGenerateTargetFlavors($cdlSrcMedInf, $cdlFlavors);
$cmdLine=KDLWrap::CDLProceessFlavorsForCollection($cdlTargets->_targetList);

		if($cdlTargets->_targetList[0]->engine_version==0){
			$cmdLine = $cdlTargets->_targetList[0]->command_lines;
		}
		else{
			$cmdLine = $cdlTargets->_targetList[0]->operators;
$oprSets = new kOperatorSets();
//		$operators = stripslashes($operators);
//kLog::log(__METHOD__."\ncdlOperators(stripslsh)==>\n".print_r($operators,true));
			$oprSets->setSerialized($cdlTargets->_targetList[0]->operators);
			$oprArr = $oprSets->getSets();
			$cmdLine=$oprArr[0][0]->command;
		}
		
$outFile = "aaa111.mpg";
		$exec_cmd =  
			str_replace ( 
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutFileName, KDLCmdlinePlaceholders::ConfigFileName), 
				array("$contentDir\\$patern", $outFile, "mko"),
				$cmdLine);
/*
$xmlFileName = "k:\\pre.xml";
		$fHd = fopen($xmlFileName, "w");
		fwrite($fHd,$exec_cmd);
		fclose($fHd);
		//c:\Windows\SysWOW64\WindowsPowerShell\v1.0\powershell.exe -file C:\opt\kaltura\app\batch\batches\Convert\scripts\runee3.ps1 C:\expressionencoder\ExpressionEncoder.dll W:\/content/entry/data/29/399/0_0bybp8ez_0_t75dk08i_1.mov C:\opt\kaltura\tmp\convert\convert_0_0bybp8ez_4c24801bbb92f.xml >> "C:\opt\kaltura\tmp\convert\0_0bybp8ez_0_2.log" 2>&1
		$fHd = fopen("k:\\run.bat", "w");
		fwrite($fHd,"c:\\Windows\\SysWOW64\\WindowsPowerShell\\v1.0\\powershell.exe -file K:\\opt\\kaltura\\app\\batch\\batches\\Convert\\scripts\\runee3.ps1 C:\\expressionencoder\\ExpressionEncoder.dll $contentDir\\$patern $xmlFileName\npause");
		fclose($fHd);
*/		
		echo "1<br>\n";
		echo($exec_cmd);
//		exec("ffmpeg ".$exec_cmd);
		if(!file_exists($outFile) || filesize($outFile)==0) {
			kLog::log( "\nFailed");
		}
		else {
			kLog::log( "\nSucceeded, Filesize:".filesize($outFile));
		}
//		$cdlAsset = new flavorAsset;
//		KDLWrap::ConvertMediainfoCdl2FlavorAsset($cdlMedInf, $cdlAsset);
		$cdlMedInf = new mediaInfo();
		$kdlMedSet = getMediasetFromFile("$outFile");
		$cdlMedInf->LoadFromMediaset($kdlMedSet);

		kLog::log( "\npre CDLValidateProduct\n");
		
		KDLWrap::CDLValidateProduct($cdlSrclMedInf, $cdlTargets->_targetList[0], $cdlMedInf);
//		print_r($kdlFlavor);
 
		return;
	}
	
	function runXMLtest($xmlFileName)
	{

$config = new KalturaConfiguration("100");
$config->serviceUrl = 'trunk';
$client = new KalturaClient($config);
$client->setKs('YjQ2ZjFlY2FkNzJiMTRlYWZhZjhjZGVhY2ZkNjFjMmVlZDBhODZmY3wxMDA7MTAwOzEyODkyMjA2Mjc7MjsxMjg5MTM0MjI3LjQ3OzEwMDs7');
//	dofileSync($client, '0_t2jxptmg');

	doIt($client, $xmlFileName, "k:\media\join\avidemuxTest.scp");
	return;
		kLog::Log($xmlFileName);
		$xml = simplexml_load_file(realpath($xmlFileName));
		print_r($xml);
		
		foreach ($xml->VideoAssets->vidAsset as $vidAsset) {
			$attr = $vidAsset->attributes();
			$entryId = $attr[k_id];
			$attr = $vidAsset->StreamInfo->attributes();
			$fileName = $attr[file_name];
			echo "\nEntryId ($entryId), FileName($fileName)\n";

$results = $client->flavorAsset->getByEntryId('0_tyg28fov');	
print_r($results[0]->videoCodecId);
		}
		return;
	}
	/*
	 * 
	 */
	function doIt($clientObj, $mixFileName, $aviDemuxScp)
	{
		kLog::Log($mixFileName);
		$xml = simplexml_load_file(realpath($mixFileName));
//		print_r($xml);
		$jnDataArr = array();
		foreach ($xml->VideoAssets->vidAsset as $vidAsset) {
			$jnData = new JoinEntityData();
			$attr = $vidAsset->attributes();
			$jnData->_entryId = (string)$attr[k_id];
//			$entryId = $eId[0];
//			print_r($entryId);
//			$attr = $vidAsset->StreamInfo->attributes();
//			$jnData->_fileName = (string)$attr[file_name];

			$assets = $clientObj->flavorAsset->getByEntryId($jnData->_entryId);
			$jnData->_assetId = null;
			foreach ($assets as $asset) {
				if($asset->isOriginal==1) {
					$jnData->_assetId = $asset->id;
					break;
				}
			}
			$jnData->_fileName = dofileSync($clientObj, $jnData->_assetId);
			if(!isset($jnData->_fileName)) {
				return null;
			}
			$filter = new KalturaMediaInfoFilter();
$filter->flavorAssetIdEqual = $asset->id;
$mediaInfos = $clientObj->mediaInfo->listAction($filter, null);
			$jnData->_fps = $mediaInfos->objects[0]->videoFrameRate;
			$jnData->_dur = $mediaInfos->objects[0]->videoDuration;
			$jnDataArr[] = $jnData;
			print_r($jnData);
//			print_r($asset);
//			echo "\nEntryId ($entryId), FileName($fileName), Asset($assetId)\n";
			
//print_r($mediaInfos);
		}
		
		$fHd = fopen($aviDemuxScp, "w");
		fwrite($fHd,"var app = new Avidemux();\n");
		$totalDur=0;
		$str1=null;
		$str2=null;
		$idx=0;
		foreach ($jnDataArr as $jnData) {
			if($str1==null) {
				$str1='app.load("'.$jnData->_fileName.'");'."\n";
			}
			else{
				$str1.='app.append("'.$jnData->_fileName.'");'."\n";
			}
			$str2.="app.addSegment($idx,0,".(int)($jnData->_dur*$jnData->_fps/1000).");\n";//."\n";			"
//			$str2.='app.addSegment(0,0,'.$jnData->_dur.');';//."\n";			"
			$totalDur += $jnData->_dur;
			$idx++;
		}
		fwrite($fHd, $str1);
		fwrite($fHd, $str2);
		fwrite($fHd, "app.markerA=0;\n");
		$totalDur=(int)($totalDur*$jnData->_fps/1000)-1;
		fwrite($fHd, "app.markerB=$totalDur;\n");
		fwrite($fHd, "app.video.setPostProc(3,3,0);\n");
		fwrite($fHd, "app.video.fps1000 = ".$jnData->_fps*1000..";\n");
		fwrite($fHd, 'app.video.codec("Copy", "CQ=4", "0 ");'."\n");

//** Audio **
fwrite($fHd, 'app.audio.reset();
app.audio.codec("copy",0,0,"");
app.audio.normalizeMode=0
app.audio.normalizeValue=0;
app.audio.delay=0;
app.audio.mixer="NONE";
app.setContainer("MP4");
setSuccess(1);');
		
		fclose($fHd);
	}
	
	/*
	 * 
	 */
	function dofileSync($clientObj, $assetId)
	{
$filter = new KalturaFileSyncFilter();
//$filter->partnerIdEqual = $partnerid;
$filter->objectTypeEqual = KalturaFileSyncObjectType::FLAVOR_ASSET;
//$filter->objectIdEqual = '';
$filter->objectIdIn = $assetId;
$syncResults = $clientObj->fileSync->listAction($filter, null);
		foreach($syncResults->objects as $sync){
			if(isset($sync->isCurrentDc) && $sync->isCurrentDc==1) {
				$fPath = $sync->fileRoot.$sync->filePath;
				return ($fPath);
			}
		}
		return null;	
	}
	
	
class JoinEntityData {
	public $_entryId;
	public $_assetId;
	public $_fileName;
	public $_fps;
	public $_dur;
};

function parsePlayList($fileIn, $fileOut)
{
	$fdIn = fopen($fileIn, 'r');
	if($fdIn==false)
		return false;
	$fdOut = fopen($fileOut, 'w');
	if($fdOut==false)
		return false;
	$strIn=null;
	while ($strIn=fgets($fdIn)){
		if(strstr($strIn,"---")){
			$i=strrpos($strIn,"/");
			$strIn = substr($strIn,$i+1);
		}
		fputs($fdOut,$strIn);
		echo $strIn;
	}
	fclose($fdOut);
	fclose($fdIn);
	return true;
}