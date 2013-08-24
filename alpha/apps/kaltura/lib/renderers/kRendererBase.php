<?php

/*
 * @package server-infra
 * @subpackage renderers
 */
interface kRendererBase
{
	public function validate();
	
	public function output();
}
