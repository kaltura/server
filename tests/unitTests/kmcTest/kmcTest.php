<?php

require_once(dirname(__FILE__) . '/../../bootstrap/bootstrapAPI.php');

/**
 * The KMC sanity test case
 * tests if decision layer makes a right decision about converting and validating files 
 * @author Roni
 *
 * TODO: change the file name to be caps
 */
class KMCTest extends KalturaApiTestCase
{
	/**
	 * 
	 * Creates a new KMC Test case
	 * @param string $name
	 * @param array<unknown_type> $data
	 * @param string $dataName
	 */
	public function __construct($name = "KMCTest", array $data = array(), $dataName ="Default data")
	{
		parent::__construct($name, $data, $dataName);
	}
	
	/**
	 * 
	 * Test the KMC Checks that the starting calls return okay
	 * @param array<unknown_type> $params
	 * @param array<unknown_type> $results
	 * @dataProvider provideData
	 */
	public function testKMCAction($uiConfId, $uiConf2Id, $uiConf3Id, $userId, $results)
	{
		print("\nin KMCTest\n");
		
		//Starting multirequest
		$this->client->startMultiRequest();
//		$userId = 'noga@mailinator.com';
//		$uiConfId = 3550492;
//		$uiConf2Id = 3550491;
		$uiConf = $this->client->uiConf->get($uiConfId);
		$uiConf2 = $this->client->uiConf->get($uiConf2Id);
		$user = $this->client->user->get($userId);
		$userRole = $this->client->userRole->get($user->roleId);
		$filter = new KalturaPermissionFilter();
		$filter->statusEqual = 1;
		$filter->typeIn = "2, 3";
		$permissions = $this->client->permission->listAction($filter);
		$results = $this->client->doMultiRequest();

		//Dashboard ui conf
//		$uiConf3Id = 3550477;		
		$uiConf3 = $this->client->uiConf->get($uiConf3Id);
		
		//Dashboard tab
		$this->client->startMultiRequest();
		$usage = $this->client->partner->getUsage();
		$info = $this->client->partner->getInfo();
		$results2 = $this->client->doMultiRequest();

		//Atar Test
		$mediaListResults = $this->client->media->listAction();
	}
}