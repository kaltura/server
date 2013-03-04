<?php

require_once 'FileExplorer.php';

class unsafeCommandVisitor extends Visitor {
	
	const FILE_NAME_PATTERN = ".php";
	const EXECUTE_CMD = "/^(\s)*exec(\s)*\([^$]*\\$/i";
	const SYSTEM_CMD = "/^(\s)*system(\s)*\([^$]*\\$/i";
	const PASSTHRU_CMD = "/^(\s)*passthru(\s)*\([^$]*\\$/i";
	
	private static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
	
		return (substr($haystack, -$length) === $needle);
	}
	
	public function shouldVisit($fileName) {
		return self::endsWith($fileName, self::FILE_NAME_PATTERN);
	}
	
	private function checkPattern($pattern, $fileName, $excludes) {
		$matches = preg_grep($pattern, file($fileName));
		if(!empty($matches))
			foreach($matches as $line=>$match) {
				$shouldExclude = false;
				foreach($excludes as $exclude)
					if(strpos($match, $exclude) !== false) {
						$shouldExclude = true;
						break;
					}
				if(!$shouldExclude)
					print "$fileName\t[" . ($line + 1) . "]\t" . trim($match) . "\n";
			}
	}
	
	public function visit($fileName) {
		$excludes = array("sf_symfony_lib_dir");
		$this->checkPattern(self::EXECUTE_CMD, $fileName, $excludes);
		$this->checkPattern(self::SYSTEM_CMD, $fileName, $excludes);
		$this->checkPattern(self::PASSTHRU_CMD, $fileName, $excludes);
	}
}

exploreDir(new unsafeCommandVisitor(), "c:/opt/kaltura/app", 
		array("c:/opt/kaltura/app/cache", "c:/opt/kaltura/app/gemini_2013_01_07", "c:/opt/kaltura/app/gemini_2013_01_28_SaaS",
				"c:/opt/kaltura/app/gemini_2013_01_07_SaaS", "c:/opt/kaltura/app/symfony","c:/opt/kaltura/app/vendor",
				"c:/opt/kaltura/app/tests", "c:/opt/kaltura/app/deployment"));

