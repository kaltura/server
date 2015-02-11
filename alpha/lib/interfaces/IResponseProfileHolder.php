<?php

interface IResponseProfileHolder extends IResponseProfileBase
{
	public function getId();
	public function setId($v);

	public function getSystemName();
	public function setSystemName($systemName);
}

