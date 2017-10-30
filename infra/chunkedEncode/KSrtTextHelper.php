<?php
/*****************************
 * Includes & Globals
 */
ini_set("memory_limit","512M");

	/********************
	 * KSrtText
	 */
	class KSrtText {
		protected $num = 0;
		protected $times = array();
		
		protected $lines = array();
		
		/********************
		 * SplitSrtFile
		 */
		public static function SplitSrtFile($fHd, $fileName, $startTime, $duration, &$txArr)
		{
			$tx = end($txArr);
			if($tx===false || $tx->getTime(0)<$startTime) {
				$rv = self::loadSubsFromFile($fHd, $startTime+$duration, $txArr);
			}
			self::toFile($fileName, $txArr, $startTime, $duration);
		}
		
		/********************
		 * getTime
		 */
		public function getTime($num)
		{
			return $this->times[$num];
		}
		
		/********************
		 * parseTimeFromLine
		 */
		protected function parseTimeFromLine($line)
		{
			$h0 = $m0 = $s0 = $u0 = $h1 = $m1 = $s1 = $u1 = $str = 0;
			$rv=sscanf(trim($line),'%d:%d:%d,%d %s %d:%d:%d,%d', $h0, $m0, $s0, $u0, $str, $h1, $m1, $s1, $u1);
			if($rv!=9)
				return false;
			$this->times[0] = 3600*$h0+60*$m0+$s0+$u0/1000;
			$this->times[1] = 3600*$h1+60*$m1+$s1+$u1/1000;

			return true;
		}
		
		/********************
		 * timeToString
		 */
		protected static function timeToString($tm)
		{
			$h = (int)($tm/3600);
			$rem = $tm - $h*3600;
			$m = (int)($rem/60);
			$rem -= $m*60;
			$s = (int)$rem;
			$u = round(($rem-$s)*1000);
			$str = sprintf('%02d:%02d:%02d,%03d',$h,$m,$s,$u);
			KalturaLog::log("$tm:$str");
			return $str;
		}
		
		/********************
		 * toFile
		 */
		protected static function toFile($fileName, &$txArr, $chunkStart, $chunkDur)
		{
			if(count($txArr)>0) {
				$tmpIdx=1;
				foreach($txArr as $idx=>$tx) {
					if($tx->getTime(0)<$chunkStart+$chunkDur){
						$str = $tx->toString($tmpIdx,$chunkStart);
						file_put_contents($fileName, "$str", FILE_APPEND);
						if($tx->getTime(1)<$chunkStart+$chunkDur)
							unset($txArr[$idx]);
						$tmpIdx++;
					}
				}
			}
			else {
				$str = KSrtText::emptyToString($fileName);
				file_put_contents($fileName, "$str", FILE_APPEND);
			}
		}
		
		/********************
		 * toString
		 */
		protected function toString($num=null, $offset=0)
		{
			KalturaLog::log("$num, $offset");
			if(!isset($num))
				$num = $this->num;
			$tm0 = $this->times[0]-$offset;
			$tm1 = $this->times[1]-$offset;
			if($tm0<0){
				$tm0 = 0;
				if($tm1<0){
					return self::emptyToString($num);
				}
			}
			$tmStr0 = self::timeToString($tm0);
			$tmStr1 = self::timeToString($tm1);
			$str = $num."\n";
			$str.= "$tmStr0 --> $tmStr1\n";
			$str.= implode("",$this->lines)."\n";
			return $str;
		}
		
		/********************
		 * emptyToString
		 */
		protected static function emptyToString($num=1)
		{
			return ("$num\n");
		}
		
		/********************
		 * loadSubsFromFile
		 */
		protected static function loadSubsFromFile($fHd, $finishTime, &$txArr)
		{
			$cnt = 0;
			while(1) {
				$tx = self::loadSubsLine($fHd);
				if($tx===false)
					break;
				$lineStartTime = $tx->getTime(0);
				$lineFinishTime = $tx->getTime(1);
				$txArr[] = $tx;
				$cnt++;
				if($finishTime<$lineFinishTime)
					break;
			}
			return $cnt;
		}
		
		/********************
		 * loadSubsLine
		 * 	The file struct is like this - 
		 * 		line num
		 *		from --> to
		 *		text line 1
		 *		...
		 *		blank line
		 */
		protected static function loadSubsLine($fHd)
		{
			/*
			 * Skip first empty lines, if any
			 */
			while(1) {
				if(($line=fgets($fHd))===false){
					return false;
				}
				if(strlen(trim($line))>0)
					break;
			}
			
			/*
			 * Attempt to load a single Subs line (with all relevant params)
			 * see above
			 */
			while(!isset($tx)){
				/*
				 * Read line number
				 */
				if(!isset($line)){
					if(($line=fgets($fHd))===false){
						return false;
					}
				}
				$num = $str = null;
				$rv=sscanf($line,'%d%s',$num,$str);
				if($rv!=1)
					continue;
				
				$tx = new KSrtText();
				$tx->num = $num;

				/*
				 * Read and parse fram/to times
				 */
				if(($line=fgets($fHd))===false)
					return false;
				if($tx->parseTimeFromLine(trim($line))==false){
					$tx = null;
					continue;
				}
				/*
				 * Read text lines
				 */
				while(1) {
					if(($line=fgets($fHd))===false){
						break;
					}
					if(strlen(trim($line))>0){
						$tx->lines[] = $line;
					}
					else {
						break;
					}
				}
			}
			return $tx;
		}
	}
	
