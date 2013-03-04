<?php

abstract class Visitor {
	
	abstract public function shouldVisit($fileName);

	abstract public function visit($fileName);
}

?>