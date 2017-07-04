<?php

abstract class ESearchItemData extends BaseObject
{
	abstract public function getType();

	abstract public function loadFromElasticHits($objectResult);
}