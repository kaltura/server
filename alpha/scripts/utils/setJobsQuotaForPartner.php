<?php
/**
 * This script is used to limit the number of jobs of a specific type
 * the given partner can execute. 
 * Please don't execute this script without consulting with someone from IT / Operations.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

/**
 * Parameters 
 * -------------- 
 */
/* Partner Id - The partner we want to restrict */
$partnerId = 100;

/* Restriction array.
 * All restrictions are defined as 
 * 		<key> = '<job_type>-<job_sub_type>'
 * 		<value> = limit
 * 
 * Some examples:
 * '*' - A restriction for all job types.
 * '4-*' - A restriction for all jobs of type 4.
 * '5-2' - A restriction for all jobs of type 5 and sub-type 2 
 * */
$quota =  array ('*' => 6, '4-*' =>2 , '5-2' => 3); 

// don't add to database if one of the parameters is missing or is an empty string
if (!($partnerId) || !($quota) )
{
	die ('Missing parameter');
}

$partner = PartnerPeer::retrieveByPK($partnerId);

if(!$partner)
{
    die("No such partner with id [$partnerId].".PHP_EOL);
}

//setting custom data fields of the partner
$partner->setJobTypeQuota($quota);
$partner->save();	

echo "Done.";

