<?php

require_once 'PHPUnit\Framework\TestCase.php';
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php');

/**
 * test case.
 */
class UserPs2Test extends PHPUnit_Framework_TestCase {
	
	const TEST_PARTNER_ID = 116;
	const TEST_ADMIN_SECRET = 'adminsecret116';
	
	private $ks = null;
	private $createdUserIds = null;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp ();
		$this->ks = $this->getAdminKs(self::TEST_PARTNER_ID, self::TEST_ADMIN_SECRET);
		$this->createdUserIds = array();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->ks = null;
		parent::tearDown ();
		foreach ($this->createdUserIds as $userId)
		{
			// delete all kusers created during the tests
			$c = new Criteria();
			$c->addAnd(kuserPeer::PUSER_ID, $userId, Criteria::EQUAL);
			$kusers = kuserPeer::doSelect($c);
			foreach ($kusers as $kuser) {
				@$kuser->delete();
			}
			
			// delete all kuser pusers created during the tests
			$c = new Criteria();
			$c->addAnd(PuserKuserPeer::PUSER_ID, $userId, Criteria::EQUAL);
			$puserKusers = PuserKuserPeer::doSelect($c);
			foreach ($puserKusers as $puserKuser) {
				@$puserKuser->delete();
			}
		}
	}
	
	public function testAddUser()
	{
		// add a user and check response
		$userId = uniqid();
		$result = $this->ps2AddUser($userId);
		$xml = simplexml_load_string($result);
		$user1 = $xml->result->user;
		$this->assertEquals($userId, (string)$user1->puserId);
		
		// try to get the added users
		$result = $this->ps2GetUser($userId);
		$xml = simplexml_load_string($result);
		$user2 = $xml->result->user;
		$this->assertEquals($userId, (string)$user2->puserId);
		
		// compare the two users - returned from add and from get
		$this->assertEquals($user1, $user2);
		
		// check failure to add the same id twice
		$result = $result = $this->ps2AddUser($userId);
		$xml = simplexml_load_string($result);
		$error = $xml->error;
		$this->assertEquals('DUPLICATE_USER_BY_ID', (string)$error->num_0->code);		
		
		// add another user with all parameters and check them all
		$user_id = uniqid().'_user_id';
		$user_screenName = 'screen_name';
		$user_first_name = 'first_name';
		$user_last_name = 'last_name';
		$user_fullName =  $user_first_name.' '.$user_last_name;
		$user_email = 'my.email@kaltura.com';
		$user_aboutMe = 'about me';
		$user_tags = 'tag1, tag2, tag3';
		$user_gender = rand(0, 1);
		$user_partnerData = uniqid();
		$result = $this->ps2AddUser( $user_id, $user_screenName, $user_fullName, $user_email, $user_aboutMe,
									 $user_tags, $user_gender, $user_partnerData);
		$xml = simplexml_load_string($result);
		$user3 = $xml->result->user;
		$this->assertEquals('', (string)$xml->error);
		$this->assertEquals($user_id, (string)$user3->puserId);
		$this->assertEquals($user_screenName, (string)$user3->kuser->screenName);
		$this->assertEquals($user_fullName, (string)$user3->kuser->fullName);
		$this->assertEquals($user_first_name, (string)$user3->kuser->firstName);
		$this->assertEquals($user_last_name, (string)$user3->kuser->lastName);
		$this->assertEquals($user_email, (string)$user3->kuser->email);
		$this->assertEquals($user_aboutMe, (string)$user3->kuser->aboutMe);
		$this->assertEquals($user_tags, (string)$user3->kuser->tags);
		$this->assertEquals($user_partnerData, (string)$user3->kuser->partnerData);
		
		// test getting the user		
		$result = $this->ps2GetUser($user_id);
		$xml = simplexml_load_string($result);
		$user4 = $xml->result->user;
		$this->assertEquals('', (string)$xml->error);
		$this->assertEquals($user_id, (string)$user4->puserId);
		$this->assertEquals($user_screenName, (string)$user4->kuser->screenName);
		$this->assertEquals($user_fullName, (string)$user4->kuser->fullName);
		$this->assertEquals($user_first_name, (string)$user4->kuser->firstName);
		$this->assertEquals($user_last_name, (string)$user4->kuser->lastName);
		$this->assertEquals($user_email, (string)$user4->kuser->email);
		$this->assertEquals($user_aboutMe, (string)$user4->kuser->aboutMe);
		$this->assertEquals($user_tags, (string)$user4->kuser->tags);
		$this->assertEquals($user_partnerData, (string)$user4->kuser->partnerData);
	}
	
	public function testGetUser()
	{
		
	}
	
	public function testDeleteUser()
	{
		
	}
	
	public function testListUsers()
	{
		
	}
	
	public function testUpdateUser()
	{
		
	}
	
	public function testUpdateUserId()
	{
		
	}
	
	// ----- PS2 "Client" for user related functions	
	
	private function getAdminKs ($partnerId, $secret)
	{
		$ks = '';
		$result = kSessionUtils::startKSession ( $partnerId , $secret , '' , $ks , 86400 , KalturaSessionType::ADMIN , '' , null );
		
		if ( $result >= 0 ) {
			return $ks;
		}
		else {
			$this->fail('Start session error');
		}
	}
	
	private function ps2AddUser( $user_id = null, $user_screenName = null, $user_fullName = null,	 
								 $user_email = null, $user_aboutMe = null, $user_tags = null,
	 							 $user_gender = null, $user_partnerData = null)
	{
		$params = array (
			'user_id' => $user_id, 
			'user_screenName' => $user_screenName,	 
			'user_fullName' => $user_fullName,	 
			'user_email' => $user_email,
			'user_aboutMe' => $user_aboutMe, 
			'user_tags' => $user_tags,
			'user_gender' => $user_gender,
			'user_partnerData' => $user_partnerData,
		);
		$this->createdUserIds[] = $user_id;
		return $this->doHttpRequest('adduser', $params);
	}
	
	private function ps2GetUser($userId)
	{
		$params = array ('user_id' => $userId);
		return $this->doHttpRequest('getuser', $params);
	}
	
	private function ps2DeleteUser($userId)
	{
		$params = array ('user_id' => $userId);
		return $this->doHttpRequest('deleteuser', $params);
	}
	
	private function ps2UpdateUser( $user_id = null, $user_screenName = null, $user_fullName = null,	 
									$user_email = null, $user_aboutMe = null, $user_tags = null,
									$user_gender = null, $user_partnerData = null )
	{
		$params = array (
			'user_id' => $user_id, 
			'user_screenName' => $user_screenName,	 
			'user_fullName' => $user_fullName,	 
			'user_email' => $user_email,
			'user_aboutMe' => $user_aboutMe, 
			'user_tags' => $user_tags,
			'user_gender' => $user_gender,
			'user_partnerData' => $user_partnerData,
		);	
		return $this->doHttpRequest('updateuser', $params);
	}
	
	private function ps2UpdateUserId($userId, $newUserId)
	{
		$params = array ('user_id' => $userId, 'new_user_id' => $newUserId);
		$this->createdUserIds[] = $newUserId;
		return $this->doHttpRequest('updateuserid');
	}
	
	private function ps2ListUsers()
	{
		return $this->doHttpRequest('listuser', array());
	}
	
	private function doHttpRequest($action, $params)
	{
		$url = kConf::get('apphome_url').'/index.php/partnerservices2/'.$action;
		$params['ks'] = $this->ks;
				
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		$opt = http_build_query($params, null, "&");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $opt);
		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		
		if ($curlError) {
			$this->fail('CurlError - '.$curlError);
		}
		
		return $result;
	}

}

