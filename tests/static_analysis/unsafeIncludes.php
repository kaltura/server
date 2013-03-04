<?php

require_once 'FileExplorer.php';

class unsafeIncludesVisitor extends Visitor {
	
	const FILE_NAME_PATTERN = ".php";
	const UNSAFE_REQUIRE_PATTERN = "/^(\s)*require(_once){0,1}(\s)*\((\"){0,1}[a-zA-Z_\s]*\\$/i";
	const UNSAFE_INCLUDE_PATTERN = "/^(\s)*include(_once){0,1}(\s)+(\")[a-zA-Z_\s]*\\$/i";
	
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
		$this->checkPattern(self::UNSAFE_INCLUDE_PATTERN, $fileName, $excludes);
		$this->checkPattern(self::UNSAFE_REQUIRE_PATTERN, $fileName, $excludes);
	}
}

exploreDir(new unsafeIncludesVisitor(), "c:/opt/kaltura/app", 
		array("c:/opt/kaltura/app/cache", "c:/opt/kaltura/app/gemini_2013_01_07", "c:/opt/kaltura/app/gemini_2013_01_28_SaaS",
				"c:/opt/kaltura/app/gemini_2013_01_07_SaaS", "c:/opt/kaltura/app/symfony","c:/opt/kaltura/app/vendor"));

