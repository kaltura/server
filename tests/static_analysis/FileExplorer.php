<?php

require_once 'visitor.php';

function exploreDir(Visitor $visitor, $dirPath, $excludeDirList = array()) {
	foreach (glob("$dirPath/*") as $filename) {
		if(is_dir($filename) && (!in_array($filename, $excludeDirList)))
			exploreDir($visitor, $filename);

		if(is_file($filename) && ($visitor->shouldVisit($filename)))
			$visitor->visit($filename);
	}
}