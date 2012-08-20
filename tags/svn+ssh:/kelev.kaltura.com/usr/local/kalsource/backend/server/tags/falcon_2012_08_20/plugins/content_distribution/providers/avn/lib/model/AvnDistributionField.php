<?php
/**
 * @package plugins.avnDistribution
 * @subpackage model.enum
 */ 
interface AvnDistributionField extends BaseEnum
{
	const GUID								= 'GUID';
	const PUB_DATE							= 'PUB_DATE';
	const TITLE								= 'TITLE';
	const DESCRIPTION 						= 'DESCRIPTION';
	const LINK								= 'LINK';
	const CATEGORY							= 'CATEGORY';
	const IS_ON_MAIN						= 'IS_ON_MAIN';
	const ORDER_MAIN						= 'ORDER_MAIN';
	const ORDER_SUB							= 'ORDER_SUB';
	const HEADER							= 'HEADER';
	const SUB_HEADER						= 'SUB_HEADER';
}