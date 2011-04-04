<?php


require_once 'Dailymotion.php';

class DailyMotionImpl
{
	private $apiKey = "c53ca34fc66da3f98867";
	private $apiSecret = "aa8e888a2927dc1d54f2d1a0bd98ca51d1e65a98";
	private $api = null;
	private $user = "";
	private $pass = "";

	public function __construct($user, $pass)
	{
		$this->api = new DailyMotion();
		$this->user = $user;
		$this->pass = $pass;
		$this->_connect();
	}
	
	private function _connect()
	{
		$perms = array();
		$perms[] = 'read';
		$perms[] = 'write';
		$perms[] = 'delete';
        $this->api->setGrantType(Dailymotion::GRANT_TYPE_PASSWORD, $this->apiKey, $this->apiSecret, $perms, array('username' => $this->user, 'password' => $this->pass));
        $result = $this->api->call('auth.info');
	}

	public function upload($file)
	{
        $url = $this->api->uploadFile($file);
		$result = $this->api->call('video.create', array('url' => $url));
		$remoteId = $result['id'];
		return $remoteId;
	}	
	
	public function update($id, $propsArray)
	{
		$dailymotionArray = array('id' => $id);
		foreach($propsArray as $key => $value)
		{
			if (!empty($key) && !empty($value))
			{
				$dailymotionArray[$key]=$value;
			}
		}
		$this->api->call('video.edit', $dailymotionArray);
	
	}
	
	public function delete($id)
	{
		$this->api->call('video.delete', array('id' => $id));
	}
	
	public function getStatus($id)
	{
		$result = $this->api->call('video.status', array('id' => $id));
		return $result['status'];
	}
}


/*$testUser = "kalturasb";
$testPassword = "kalturasb";
$testVideoFile = 'snake.aaa';

$test = new DailyMotionImpl($testUser, $testPassword);
//$remoteId = $test -> upload($testVideoFile);
$remoteId = 'xhlco1';
$ar1 = explode(",", "mytaga,mytagb,mytagd");
$ar2 = array('tag3', 'tag4');
print_r($ar1);
print_r($ar2);
$popsArray = array('tags' => $ar1, 'title' => 'about to delete', 'channel' => 'shortfilms', 'description' => 'yabadaa', 'language' => 'en', 'date' => time(), 'published' => true);
$test -> update($remoteId, $popsArray);*/
//$test -> delete('xh0k99');
