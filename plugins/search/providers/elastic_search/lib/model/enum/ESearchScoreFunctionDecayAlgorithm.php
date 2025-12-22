<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchScoreFunctionDecayAlgorithm extends BaseEnum
{
	const EXP = 'exp';
	const GAUSS = 'gauss';
	const LINEAR = 'linear';
}
