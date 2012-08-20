<?php

require_once(dirname(__FILE__) . '/../../bootstrap/bootstrapApi.php');

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
	public function testKMCAction($kmcUiConfId, $permissionsUiConf2Id, $dashboardUiConf3Id, $userId, $results)
	{
		//Starting multirequest - ui conf
		$this->client->startMultiRequest();
		$kmcUiConf = $this->client->uiConf->get($kmcUiConfId);
		$permissionsUiConf2 = $this->client->uiConf->get($permissionsUiConf2Id);
		$user = $this->client->user->get($userId);
		$userRole = $this->client->userRole->get($user->roleId);
		$filter = new KalturaPermissionFilter();
		$filter->statusEqual = 1;
		$filter->typeIn = "2, 3";
		$permissions = $this->client->permission->listAction($filter);
		$results = $this->client->doMultiRequest();

		//Dashboard ui Conf
		$uiConf3 = $this->client->uiConf->get($dashboardUiConf3Id);

		//Dashboard tab
		$this->client->startMultiRequest();
		$usage = $this->client->partner->getUsage();
		$info = $this->client->partner->getInfo();
		$results2 = $this->client->doMultiRequest();

		//Atar Test
		$mediaListResults = $this->client->media->listAction();
	}
}