<?php
/**
 * @package infra
 * @subpackage Conversion
 */

require_once '../kOperator.php';
require_once '../kOperatorSets.php';

$json = '
[
	[
		{
			"id":1,
			"extra":"A extra params 1 ",
			"command":"A command line data 1"
		},
		{
			"id":2,
			"extra":"A extra params 2",
			"command":"A command line data 2"
		}
	],
	[
		{
			"id":1,
			"extra":"B extra params 1 ",
			"command":"B command line data 1"
		},
		{
			"id":2,
			"extra":"B extra params 2",
			"command":"B command line data 2"
		}
	]
]';


$obj = new kOperatorSets();
$obj->setSerialized($json);

var_dump($obj);
