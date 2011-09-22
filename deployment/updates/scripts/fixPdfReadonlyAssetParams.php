<?php
/**
 * enable feature for each partner
 * set to all partners with partner->partnerPackage > 1 to 1  
 * @package Deployment
 * @subpackage updates
 */

$dryRun = true; //TODO: change for real run
if (in_array ( 'realrun', $argv ))
	$dryRun = false;
	

	
$countLimitEachLoop = 100;
$offset = $countLimitEachLoop;
$assetParamsChanged = 0;
//------------------------------------------------------


require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
KalturaStatement::setDryRun ( $dryRun );

$c = new Criteria();
$c->add(assetParamsPeer::TAGS, 'pdf-readonly', Criteria::LIKE);
$c->setLimit ( $countLimitEachLoop );

$assetParams = assetParamsPeer::doSelect ( $c, $con );
while ( count ( $assetParams ) ) 
{
	foreach ( $assetParams as $assetParam ) 
	{
		if(!($assetParam instanceof PdfFlavorParams))
			continue;
		
		if (!$assetParam->getReadonly())
		{
			$assetParam->setReadonly(true);
			$assetParam->save();
			$assetParamsChanged++;
			echo $assetParamsChanged . ': Set readonly to asset param id: ' . $assetParam->getId() . PHP_EOL;
		}
	}
	
	$c->setOffset($offset);
	assetParamsPeer::clearInstancePool();
	$assetParams = assetParamsPeer::doSelect ( $c, $con );
	$offset += $countLimitEachLoop;
	sleep ( 1 );
}

echo "done. updated $assetParamsChanged asset params" . PHP_EOL;
