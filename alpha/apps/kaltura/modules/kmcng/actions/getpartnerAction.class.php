<?php
/**
 * @package    Core
 * @subpackage KMCNG
 */
class getpartnerAction extends kalturaAction
{
	public function execute ( )
	{
		$product_dev = array('2227652','1645161','2052371','2306001','1088012','601652','2035982','1376231','33962');
		$support = array('1820271','1900001');
		$customers = array('1329972','505811','811482','599132','2031111','391241','2075141','1723551','1459511','1921661','1786071','1068292','2093031','2267341','1125742','1953381');
		$utest = array('2393651','2393671','2393681','2393691','2393701','2393711','2393721','2393751','2393761','2393781');
		$qa = array('2264851','2269901','2290151','2296611','2348741','2243041','2091671','929011','2341681','1989481');
		$pids = array_merge($product_dev, $support, $customers, $utest, $qa);

		$pid = $_GET['pid'];
		echo json_encode(array('isPartnerPartOfBeta' =>  in_array($pid, $pids, true)));
	}

}
