<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * kFileTransferMgr test case.
 */
class kFileTransferMgrTest extends KalturaTestCaseApiBase
{
	
	/**
	 * @var kFileTransferMgr
	 */
	private $kFileTransferMgr;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$testConfig = $this->config->get('config');
		$this->kFileTransferMgr = kFileTransferMgr::getInstance($testConfig->managerType);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->kFileTransferMgr = null;
		parent::tearDown();
	}
	
	/**
	 * Tests kFileTransferMgr->login()
	 * 
	 * @param string $server Server's hostname or IP address
	 * @param string $user User's name
	 * @param string $pass Password
	 * @param int $port Server's listening port
	 * @param bool $ftp_passive_mode Used for FTP only
	 * @param int $exceptionCode the expected exception code
	 * @dataProvider provideData
	 * @return kFileTransferMgr
	 */
	public function testLogin($server, $user, $pass, $port, $ftp_passive_mode, $exceptionCode = null)
	{
		try 
		{
			$actualReturned = $this->kFileTransferMgr->login($server, $user, $pass, $port, $ftp_passive_mode);
		}
		catch (kFileTransferMgrException $te)
		{
			$this->assertEquals($exceptionCode, $te->getCode(), "Wrong transfer exception code [" . $te->getMessage() . "]");
			return null;
		}
		catch (Exception $e)
		{
			$this->assertEquals($exceptionCode, $e->getCode(), "Wrong exception code [" . $e->getMessage() . "]");
			return null;
		}
		
		if($exceptionCode)
			$this->fail("Expected exception code [$exceptionCode]");
			
		return $this->kFileTransferMgr;
	}
	
	/**
	 * Tests kFileTransferMgr->listDir()
	 * 
	 * @param kFileTransferMgr $kFileTransferMgr
	 * @param string $remote_path
	 * @param array $list
	 * @param int $exceptionCode the expected exception code
	 * @dataProvider provideData
	 * @depends testLogin with data set #0
	 */
	public function testListDir(kFileTransferMgr $kFileTransferMgr, $remote_path, $list, $exceptionCode = null)
	{
		try 
		{
			$actualList = $kFileTransferMgr->listDir($remote_path);
			$this->assertEquals(count($list), count($actualList), 'Wrong list size');
			
			foreach($list as $value)
				if(!in_array($value, $actualList))
					$this->fail("Missing file [$value]");
		}
		catch (kFileTransferMgrException $te)
		{
			$this->assertEquals($exceptionCode, $te->getCode(), "Wrong transfer exception code [" . $te->getMessage() . "]");
			return;
		}
		catch (Exception $e)
		{
			$this->assertEquals($exceptionCode, $e->getCode(), "Wrong exception code [" . $e->getMessage() . "]");
			return;
		}
		
		if($exceptionCode)
			$this->fail("Expected exception code [$exceptionCode]");
	}
	
	/**
	 * Tests kFileTransferMgr->fileSize()
	 * 
	 * @param kFileTransferMgr $kFileTransferMgr
	 * @param string $remote_path
	 * @param string $file
	 * @param int $expectedSize
	 * @param int $exceptionCode the expected exception code
	 * @dataProvider provideData
	 * @depends testLogin with data set #0
	 */
	public function testFileSize(kFileTransferMgr $kFileTransferMgr, $remote_path, $file, $expectedSize, $exceptionCode = null)
	{
		try 
		{
			$actualSize = $kFileTransferMgr->fileSize("$remote_path/$file");
			$this->assertEquals($expectedSize, $actualSize, "Wrong file size [$remote_path/$file]");
		}
		catch (kFileTransferMgrException $te)
		{
			$this->assertEquals($exceptionCode, $te->getCode(), "Wrong transfer exception code [" . $te->getMessage() . "]");
			return;
		}
		catch (Exception $e)
		{
			$this->assertEquals($exceptionCode, $e->getCode(), "Wrong exception code [" . $e->getMessage() . "]");
			return;
		}
		
		if($exceptionCode)
			$this->fail("Expected exception code [$exceptionCode]");
	}
	
	
	/**
	 * Tests kFileTransferMgr->getResults()
	 */
	public function testGetResults()
	{
		// TODO Auto-generated kFileTransferMgrTest->testGetResults()
		$this->markTestIncomplete("getResults test not implemented");
		
		$this->kFileTransferMgr->getResults(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->getConnection()
	 */
	public function testGetConnection()
	{
		// TODO Auto-generated kFileTransferMgrTest->testGetConnection()
		$this->markTestIncomplete("getConnection test not implemented");
		
		$this->kFileTransferMgr->getConnection(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->loginPubKey()
	 */
	public function testLoginPubKey()
	{
		// TODO Auto-generated kFileTransferMgrTest->testLoginPubKey()
		$this->markTestIncomplete("loginPubKey test not implemented");
		
		$this->kFileTransferMgr->loginPubKey(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->putFile()
	 */
	public function testPutFile()
	{
		// TODO Auto-generated kFileTransferMgrTest->testPutFile()
		$this->markTestIncomplete("putFile test not implemented");
		
		$this->kFileTransferMgr->putFile(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->getFile()
	 */
	public function testGetFile()
	{
		// TODO Auto-generated kFileTransferMgrTest->testGetFile()
		$this->markTestIncomplete("getFile test not implemented");
		
		$this->kFileTransferMgr->getFile(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->mkDir()
	 */
	public function testMkDir()
	{
		// TODO Auto-generated kFileTransferMgrTest->testMkDir()
		$this->markTestIncomplete("mkDir test not implemented");
		
		$this->kFileTransferMgr->mkDir(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->chmod()
	 */
	public function testChmod()
	{
		// TODO Auto-generated kFileTransferMgrTest->testChmod()
		$this->markTestIncomplete("chmod test not implemented");
		
		$this->kFileTransferMgr->chmod(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->fileExists()
	 */
	public function testFileExists()
	{
		// TODO Auto-generated kFileTransferMgrTest->testFileExists()
		$this->markTestIncomplete("fileExists test not implemented");
		
		$this->kFileTransferMgr->fileExists(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->delFile()
	 */
	public function testDelFile()
	{
		// TODO Auto-generated kFileTransferMgrTest->testDelFile()
		$this->markTestIncomplete("delFile test not implemented");
		
		$this->kFileTransferMgr->delFile(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->delDir()
	 */
	public function testDelDir()
	{
		// TODO Auto-generated kFileTransferMgrTest->testDelDir()
		$this->markTestIncomplete("delDir test not implemented");
		
		$this->kFileTransferMgr->delDir(/* parameters */);
	
	}
	
	/**
	 * Tests kFileTransferMgr->modificationTime()
	 */
	public function testModificationTime()
	{
		// TODO Auto-generated kFileTransferMgrTest->testModificationTime()
		$this->markTestIncomplete("modificationTime test not implemented");
		
		$this->kFileTransferMgr->modificationTime(/* parameters */);
	
	}

}

