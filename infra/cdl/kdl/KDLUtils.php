<?php

class KDLUtils
{
	/* ------------------------------
	 * trima
	 */
	public static function trima($str)
	{
		$str = str_replace(array("\n", "\r", "\t", " ", '\o', "\xOB"), '', $str);
		return $str;
	}

	/* ------------------------------
	 * convertDuration2msec
	 */
	public static function convertDuration2msec($str)
	{
	//echo $str;
		preg_match_all("/(([0-9]*)h ?)?(([0-9]*)mn ?)?(([0-9]*)s ?)?(([0-9]*)ms ?)?/",
			$str, $res);

		$hour = @$res[2][0] ? @$res[2][0] : 0;
		$min  = @$res[4][0] ? @$res[4][0] : 0;
		$sec  = @$res[6][0] ? @$res[6][0] : 0;
		$msec = @$res[8][0] ? @$res[8][0] : 0;
	//echo "<br>Time ". $hour . "hr:".$min . "mn:" . $sec . "sec:". $msec ."msec<br>";
		$rv = ($hour*3600 + $min*60 + $sec)*1000 + $msec;
		settype($rv, "integer");
		return $rv;
	}
	
	public static function getNumericValu($value)
	{
		
	}

	/* ------------------------------
	 * convertValue2kbits
	 */
	public static function convertValue2kbits($str)
	{
		preg_match_all("/(([0-9.]*)b ?)?(([0-9.]*)k ?)?(([0-9.]*)m ?)?(([0-9.]*)g ?)?/",
			$str, $res);

		if(@$res[2][0]!=="")
			$kbps=@$res[2][0]/1024;
		else if(@$res[4][0]!=="")
			$kbps=@$res[4][0];
		else if(@$res[6][0]!=="")
			$kbps=@$res[6][0]*1024;
		else if(@$res[8][0]!=="")
			$kbps=@$res[8][0]*1048576;
		settype($kbps, "float");
		return $kbps;
	}

	/* ------------------------------
	 * function arrayToString
	 */
	public static function arrayToString($arr) {
		$str = null;
		$prevKey = null;
		foreach ($arr as $key => $item){
			if($str){
				$str = $str.",";
			}
			if(is_array($item)) {
				if($prevKey && $key==$prevKey) {
					$str = $str.",".KDLUtils::arrayToString($item);
				}
				else{
					$str = $str.$key."=>".KDLUtils::arrayToString($item);
				}
			}
			else {
				if(!is_object($item))// && )
					$item2str = $item;
				else if(method_exists($item,"ToString"))
					$item2str=$item->ToString();
				else
					$item2str = "item";
				if($prevKey && $key==$prevKey) {
					$str = $str.",".$item2str;
				}
				else {
					$str = $str.$key."=>".$item2str;
				}
			}
			$prevkey = $key;
		}
		return $str;
	}
	
	/* ------------------------------
	 * function arrayToString
	 */
	public static function AddXMLElement(SimpleXMLElement $dest, SimpleXMLElement $source)
    {
        $new_dest = $dest->addChild($source->getName(), $source[0]);

		foreach($source->attributes() as $name => $val) {
			$new_dest->addAttribute($name, $val);
		} 
        foreach ($source->children() as $child) {
            self::AddXMLElement($new_dest, $child);
        }
    }

	/* ------------------------------
	 * function 
	 */
    static function parseTranscodingDataList($dataStr, $delim)
	{
		$parsed = array();
		if(is_null($dataStr))
		{
			return $parsed;
		}
		
		preg_match_all("/([^".$delim."]*)".$delim."?/", $dataStr, $trGrpPrs);
		foreach ($trGrpPrs[1] as $trGrp)
		{
			$trGrp = trim($trGrp,"()");
			if($trGrp == null)
			{
				$parsed[] = "";
				continue;
			}
			$trPrs = array();
			preg_match_all('/([^\#]*)\#?/', $trGrp, $trPrs);
			
			if(count($trPrs[1])>2)
			{
				$parsed[] = $trPrs[1];
			}
			else
			{
				$parsed[] = $trGrp;
			}
		}
		return $parsed;
	}

	/* ------------------------------
	 * function 
	 */
	static function mergeTranscoderObjArr(array $trPrmArr, $transParse, $extraParse=null, array $transDictionary=null)
	{
		foreach ($transParse as $key=>$trId){
			if($trId==null){
				continue;
			}
			
			if($extraParse && array_key_exists($key, $extraParse)){
				$trEx = $extraParse[$key];
			}
			else{
				$trEx = null;
			}
			
			if(is_array($trId)){
				$auxArr = array();
				$trPrmArr[$key] = KDLUtils::mergeTranscoderObjArr($auxArr, $trId, $trEx, $transDictionary);
			}
			else {
				$trId = KDLUtils::trima($trId);
				if(!is_null($transDictionary) && array_key_exists($trId, $transDictionary)){
					$trId = $transDictionary[$trId];
				}
				$trPrm = new KDLOperationParams();
				$trPrm->Set($trId, $trEx);
				$trPrmArr[$key] = $trPrm;
			}
		}
		return $trPrmArr;
	}
	
	/* ------------------------------
	 * function 
	 */
	static function parseTranscoderList($transStr, $extraStr, array $transDictionary=null)
	{
		$transParse = KDLUtils::parseTranscodingDataList($transStr, '\,');
		$extraParse = KDLUtils::parseTranscodingDataList($extraStr, '\|');
		$trPrmArr = array();
		$trPrmArr = KDLUtils::mergeTranscoderObjArr($trPrmArr, $transParse, $extraParse, $transDictionary);

		return $trPrmArr;
		
	}
	
		/* ---------------------------
		 * RecursiveScan
		 */
	public static function RecursiveScan(array $transObjArr, $func, $param1, $param2){
		foreach ($transObjArr as $key=>$trPrm) {
			if(is_array($trPrm)){
				KDLUtils::RecursiveScan($trPrm, $func, $param1, $param2);
			}
			else {
				$func($trPrm, $param1, $param2);
			}
		}
	}

	/* ---------------------------
	 * parseParamStr2Map
	 */
	public static function parseParamStr2Map($paramsArrStr)
	{
	$paramsMap = array();
		if(isset($paramsArrStr)) {
			$paramsInArr=explode(",",$paramsArrStr);
			foreach ($paramsInArr as $paramStr) {
				$pair=explode("=",$paramStr);
				$paramsMap[$pair[0]]=$pair[1];
			}
		}
		if(count($paramsMap)==0) {
			return null;
		}
		else {
			return $paramsMap;
		}
	}
	
	/* ---------------------------
	 * CalcH264Level
	 */
	public static function CalcH264Level($w, $h, $fr)
	{
$levels = array(
	0=>		null,
	1485=>	'1', 	
	1485=>	'1b', 	
	3000=>	'1.1', 	
	6000=>	'1.2', 	
	11880=>	'1.3', 	
	11880=>	'2', 	
	19800=>	'2.1', 	
	20250=>	'2.2', 	
	40500=>	'3', 	
	108000=>'3.1', 	
	216000=>'3.2', 	
	245760=>'4', 	
	245760=>'4.1', 	
	522240=>'4.2', 	
	589824=>'5', 	
	983040=>'5.1',
	2073600=>'5.2');
	
		$result = null;
		$macroblocks = ceil($w/16) * ceil($h/16) * $fr;
		foreach ($levels as $lvlMcbs=>$lvl){
			if ($lvlMcbs > $macroblocks) {
				$result=$levels[$lvlMcbs];
				break;
			}
		}
		return $result;
	}
	
}



?>