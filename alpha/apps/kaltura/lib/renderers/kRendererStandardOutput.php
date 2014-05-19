<?php

require_once(dirname(__file__) . '/kRendererString.php');

/**
 * @package server-infra
 * @subpackage renderers
 */
class kRendererStandardOutput extends kRendererString
{
	public function __construct($contentType = null, $maxAge = 8640000)
	{
		$content = ob_get_clean();
		parent::__construct($content, $contentType, $maxAge);
	}
}
