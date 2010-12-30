<?php
class UnitTestsGenerator extends ClientGeneratorFromPhp 
{
	private $_txtBase = "";
	private $_txtTest = "";
	private $_txtIni = "";
	
	protected function writeHeader(){}
	protected function writeFooter(){}
	protected function writeBeforeServices(){}
	protected function writeAfterServices(){}
	protected function writeBeforeTypes(){}
	protected function writeAfterTypes(){}
	protected function writeType(KalturaTypeReflector $type){}

	public function generate() 
	{
		$this->load();
		
		$this->writeBeforeServices();
		
		foreach($this->_services as $serviceReflector)
		{
			$this->writeBeforeService($serviceReflector);
			$this->writeService($serviceReflector);
			$this->writeAfterService($serviceReflector);
		}
			
		$this->writeAfterServices();
	}

	protected function writeBeforeService(KalturaServiceReflector $serviceReflector)
	{
		$serviceName = $serviceReflector->getServiceName();
		$serviceClass = $serviceReflector->getServiceClass();
		
		$bootstrapPath = '/../../base/bootstrap.php';
		
		if($serviceReflector->isFromPlugin())
		{
			$serviceClass = $serviceReflector->getServiceClass();
			$servicePath = KAutoloader::getClassFilePath($serviceClass);
			$currentFolder = realpath(dirname($servicePath));
			$rootPath = realpath(dirname(__FILE__) . '/../');
			$upCounter = 4;
			while($currentFolder && $currentFolder != $rootPath)
			{
				$currentFolder = realpath("$servicePath/../");
				$upCounter++;
			}
			$bootstrapPath = str_repeat('/..', $upCounter) . '/tests/base/bootstrap.php';
		}
		
		$this->_txtBase = '';
		$this->_txtTest = '';
		$this->_txtIni = '';
		
		$this->writeBase("<?php");
		$this->writeBase("");
		$this->writeBase("/**");
		$this->writeBase(" * $serviceName service base test case.");
		$this->writeBase(" */");
		$this->writeBase("abstract class {$serviceClass}BaseTest extends KalturaApiUnitTestCase");
		$this->writeBase("{");
		
		
		$this->writeTest("<?php");
		$this->writeTest("");
		$this->writeTest("require_once(dirname(__FILE__) . '$bootstrapPath');");
		$this->writeTest("require_once(dirname(__FILE__) . '/{$serviceClass}BaseTest.php');");
		$this->writeTest("");
		$this->writeTest("/**");
		$this->writeTest(" * $serviceName service test case.");
		$this->writeTest(" */");
		$this->writeTest("class {$serviceClass}Test extends {$serviceClass}BaseTest");
		$this->writeTest("{");

		
		$this->writeIni("[config]");
		$this->writeIni("source                                            = ini");
		$this->writeIni("serviceUrl                                        = http://www.kaltura.com/");
		$this->writeIni("partnerId                                         = 100");
		$this->writeIni("clientTag                                         = unitTest");
		$this->writeIni("curlTimeout                                       = 90");
		$this->writeIni("startSession                                      = 1");
		$this->writeIni("secret                                            = PARTNER_SECRET");
		$this->writeIni("userId                                            = ");
		$this->writeIni("sessionType                                       = 0");
		$this->writeIni("expiry                                            = 86400");
		$this->writeIni("privileges                                        = ");
	}
	
	protected function writeService(KalturaServiceReflector $serviceReflector)
	{
		$serviceName = $serviceReflector->getServiceName();
		$serviceId = $serviceReflector->getServiceId();
		$serviceClass = $serviceReflector->getServiceClass();
		$actions = $serviceReflector->getActions();
	
		
		foreach($actions as $action => $actionName)
		{
			$actionInfo = $serviceReflector->getActionInfo($action);
			
			if($actionInfo->deprecated || $actionInfo->serverOnly)
				continue;
				
			if (strpos($actionInfo->clientgenerator, "ignore") !== false)
				continue;
				
			$outputTypeReflector = $serviceReflector->getActionOutputType($action);
			$actionParams = $serviceReflector->getActionParams($action);
			$this->writeServiceAction($serviceId, $serviceName, $actionInfo->action, $actionParams, $outputTypeReflector);				
		}
	}
	
	protected function writeAfterService(KalturaServiceReflector $serviceReflector)
	{
		$this->writeBase("}");
		$this->writeTest("}");
		
		$serviceName = $serviceReflector->getServiceName();
		$serviceClass = $serviceReflector->getServiceClass();
		$testPath = realpath(dirname(__FILE__) . '/../') . "/tests/api/$serviceName";
		
		if($serviceReflector->isFromPlugin())
		{
			$servicePath = KAutoloader::getClassFilePath($serviceClass);
			$testPath = realpath(dirname($servicePath) . '/../') . "/tests/services/$serviceName";
		}
		
		$this->writeToFile("$testPath/{$serviceClass}BaseTest.php", $this->_txtBase);
		$this->writeToFile("$testPath/{$serviceClass}Test.php", $this->_txtTest, false);
		$this->writeToFile("$testPath/{$serviceClass}Test.php.ini", $this->_txtIni, false);
	}

	protected function writeServiceAddAction($serviceId, $serviceName, $action, $actionParams, KalturaParamInfo $outputTypeReflector)
	{
		$this->writeIni("");
		$this->writeIni("[testAdd]");

		$this->writeBase("	/**");
		$this->writeBase("	 * Tests {$serviceName}->add action");
		
		$testParams = array();
		$testValues = array();
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
		
			if($actionParam->isSimpleType())
			{
				$this->writeIni("test1.$paramName = " . $actionParam->getDefaultValue());
			}
			elseif($actionParam->isFile())
			{
				$this->writeIni("test1.$paramName.type = file");
				$this->writeIni("test1.$paramName.path = ");
			}
			else
			{
				$this->writeIni("test1.$paramName.type = $paramType");
				$actionParamProperties = $actionParam->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType())
						$this->writeIni("test1.$paramName.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
				}
			}
		
			$this->writeBase("	 * @param $paramType \$$paramName");
			if($actionParam->isSimpleType())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if($actionParam->getDefaultValue())
				{
					if($actionParam->getType() == 'string')
						$testParam .= " = '" . $actionParam->getDefaultValue() . "'";
					else
						$testParam .= " = " . $actionParam->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
				
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
			
		$outputType = $outputTypeReflector->getType();
		
		$this->writeBase("	 * @return int");
		$this->writeBase("	 * @dataProvider provideData");
		$this->writeBase("	 */");
		$this->writeBase("	public function testAdd($testParams)");
		$this->writeBase("	{");
		$this->writeBase("		\$resultObject = \$this->client->{$serviceName}->add($testValues);");
		$this->writeBase("		\$this->assertType('$outputType', \$resultObject);");
		$this->writeBase("		\$this->assertNotNull(\$resultObject->id);");
		$this->writeBase("		return \$resultObject->id;");
		$this->writeBase("	}");
		$this->writeBase("");
	}
	
	protected function writeServiceUpdateAction($serviceId, $serviceName, $action, $actionParams, KalturaParamInfo $outputTypeReflector)
	{
		$this->writeIni("");
		$this->writeIni("[testUpdate]");
	
		$this->writeBase("	/**");
		$this->writeBase("	 * Tests {$serviceName}->update action");
		
		$testParams = array();
		$testValues = array();
		$addId = false;
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
			if($paramType == 'int' && $paramName == 'id')
			{
				$addId = true;
				$testValues[] = '$id';
				$this->writeIni("test1.$paramName.type = dependency");
				continue;
			}
		
			if($actionParam->isSimpleType())
			{
				$this->writeIni("test1.$paramName = " . $actionParam->getDefaultValue());
			}
			else
			{
				$this->writeIni("test1.$paramName.type = $paramType");
				$actionParamProperties = $actionParam->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly() || $actionParamProperty->isInsertOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType())
						$this->writeIni("test1.$paramName.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
				}
			}
			
			$this->writeBase("	 * @param $paramType \$$paramName");
			if($actionParam->isSimpleType())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if($actionParam->getDefaultValue())
				{
					if($actionParam->getType() == 'string')
						$testParam .= " = '" . $actionParam->getDefaultValue() . "'";
					else
						$testParam .= " = " . $actionParam->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
				
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
		if($addId)
		{
			$this->writeBase("	 * @param int id - returned from testAdd");
			$testParams[] = '$id';
		}
			
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
				
		$outputType = $outputTypeReflector->getType();
		
		$this->writeBase("	 * @return int");
		$this->writeBase("	 * @depends testAdd with data set #0");
		$this->writeBase("	 * @dataProvider provideData");
		$this->writeBase("	 */");
		$this->writeBase("	public function testUpdate($testParams)");
		$this->writeBase("	{");
		$this->writeBase("		\$resultObject = \$this->client->{$serviceName}->update($testValues);");
		$this->writeBase("		\$this->assertType('$outputType', \$resultObject);");
		$this->writeBase("		\$this->assertNotNull(\$resultObject->id);");
		$this->writeBase("		return \$resultObject->id;");
		$this->writeBase("	}");
		$this->writeBase("");
	}
	
	protected function writeServiceListAction($serviceId, $serviceName, $action, $actionParams, KalturaParamInfo $outputTypeReflector)
	{
		$this->writeIni("");
		$this->writeIni("[testList]");
	
		$this->writeBase("	/**");
		$this->writeBase("	 * Tests {$serviceName}->list action");
		
		$testParams = array();
		$testValues = array();
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
		
			if($actionParam->isSimpleType())
			{
				$this->writeIni("test1.$paramName = " . $actionParam->getDefaultValue());
			}
			else
			{
				$this->writeIni("test1.$paramName.type = $paramType");
				$actionParamProperties = $actionParam->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType())
						$this->writeIni("test1.$paramName.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
				}
			}
			
			$this->writeBase("	 * @param $paramType \$$paramName");
			if($actionParam->isSimpleType())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if($actionParam->getDefaultValue())
				{
					if($actionParam->getType() == 'string')
						$testParam .= " = '" . $actionParam->getDefaultValue() . "'";
					else
						$testParam .= " = " . $actionParam->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
				
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
			
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
			
		$outputType = $outputTypeReflector->getType();
		
		$this->writeBase("	 * @dataProvider provideData");
		$this->writeBase("	 */");
		$this->writeBase("	public function testList($testParams)");
		$this->writeBase("	{");
		$this->writeBase("		\$resultObject = \$this->client->{$serviceName}->listAction($testValues);");
		$this->writeBase("		\$this->assertType('$outputType', \$resultObject);");
		$this->writeBase("		\$this->assertNotEquals(\$resultObject->totalCount, 0);");
		$this->writeBase("	}");
		$this->writeBase("");
	}
	
	protected function writeServiceGetAction($serviceId, $serviceName, $action, $actionParams, KalturaParamInfo $outputTypeReflector)
	{
		$this->writeIni("");
		$this->writeIni("[testGet]");
		
		$this->writeBase("	/**");
		$this->writeBase("	 * Tests {$serviceName}->get action");
		
		$testParams = array();
		$testValues = array();
		$addId = false;
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
			if($paramType == 'int' && $paramName == 'id')
			{
				$addId = true;
				$testValues[] = '$id';
				$this->writeIni("test1.$paramName.type = dependency");
				continue;
			}
		
			if($actionParam->isSimpleType())
			{
				$this->writeIni("test1.$paramName = " . $actionParam->getDefaultValue());
			}
			else
			{
				$this->writeIni("test1.$paramName.type = $paramType");
				$actionParamProperties = $actionParam->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType())
						$this->writeIni("test1.$paramName.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
				}
			}
			
			$this->writeBase("	 * @param $paramType \$$paramName");
			if($actionParam->isSimpleType())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if($actionParam->getDefaultValue())
				{
					if($actionParam->getType() == 'string')
						$testParam .= " = '" . $actionParam->getDefaultValue() . "'";
					else
						$testParam .= " = " . $actionParam->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
				
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
		if($addId)
		{
			$this->writeBase("	 * @param int id - returned from testAdd");
			$testParams[] = '$id';
		}
			
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
			
		$outputType = $outputTypeReflector->getType();
		
		$this->writeBase("	 * @return int");
		$this->writeBase("	 * @depends testAdd with data set #0");
		$this->writeBase("	 */");
		$this->writeBase("	public function testGet($testParams)");
		$this->writeBase("	{");
		$this->writeBase("		\$resultObject = \$this->client->{$serviceName}->get($testValues);");
		$this->writeBase("		\$this->assertType('$outputType', \$resultObject);");
		$this->writeBase("		\$this->assertNotNull(\$resultObject->id);");
		$this->writeBase("		return \$resultObject->id;");
		$this->writeBase("	}");
		$this->writeBase("");
	}
	
	protected function writeServiceDeleteAction($serviceId, $serviceName, $action, $actionParams)
	{
		$this->writeIni("");
		$this->writeIni("[testDelete]");
		
		$this->writeTest("	/**");
		$this->writeTest("	 * Called when all tests are done");
		$this->writeTest("	 * @param int \$id");
		$this->writeTest("	 * @return int");
		$this->writeTest("	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id");
		$this->writeTest("	 */");
		$this->writeTest("	public function testFinished(\$id)");
		$this->writeTest("	{");
		$this->writeTest("		return \$id;");
		$this->writeTest("	}");
		$this->writeTest("");
		
	
		$this->writeBase("	/**");
		$this->writeBase("	 * Called when all tests are done");
		$this->writeBase("	 * @param int \$id");
		$this->writeBase("	 * @return int");
		$this->writeBase("	 */");
		$this->writeBase("	abstract public function testFinished(\$id);");
		$this->writeBase("");
	
		$this->writeBase("	/**");
		$this->writeBase("	 * Tests {$serviceName}->delete action");
		
		$testParams = array();
		$testValues = array();
		$addId = false;
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
			if($paramType == 'int' && $paramName == 'id')
			{
				$addId = true;
				$testValues[] = '$id';
				$this->writeIni("test1.$paramName.type = dependency");
				continue;
			}
		
			if($actionParam->isSimpleType())
			{
				$this->writeIni("test1.$paramName = " . $actionParam->getDefaultValue());
			}
			else
			{
				$this->writeIni("test1.$paramName.type = $paramType");
				$actionParamProperties = $actionParam->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType())
						$this->writeIni("test1.$paramName.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
				}
			}
			
			$this->writeBase("	 * @param $paramType \$$paramName");
			if($actionParam->isSimpleType())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if($actionParam->getDefaultValue())
				{
					if($actionParam->getType() == 'string')
						$testParam .= " = '" . $actionParam->getDefaultValue() . "'";
					else
						$testParam .= " = " . $actionParam->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
							
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
		if($addId)
		{
			$this->writeBase("	 * @param int id - returned from testAdd");
			$testParams[] = '$id';
		}
			
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
			
		$this->writeBase("	 * @return int");
		$this->writeBase("	 * @depends testFinished");
		$this->writeBase("	 */");
		$this->writeBase("	public function testDelete($testParams)");
		$this->writeBase("	{");
		$this->writeBase("		\$resultObject = \$this->client->{$serviceName}->delete($testValues);");
		$this->writeBase("	}");
		$this->writeBase("");
	}
			
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
		KalturaLog::info("Generates action [$serviceName.$action]");
		if($action == 'add')
			return $this->writeServiceAddAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'update')
			return $this->writeServiceUpdateAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'list')
			return $this->writeServiceListAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'get')
			return $this->writeServiceGetAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'delete')
			return $this->writeServiceDeleteAction($serviceId, $serviceName, $action, $actionParams);
			
		$actionName = ucfirst($action);
		
		$this->writeIni("");
		$this->writeIni("[test{$actionName}]");
		
		$this->writeTest("	/**");
		$this->writeTest("	 * Tests {$serviceName}->{$action} action");
		
		$testParams = array();
		$testValues = array();
		$addId = false;
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
			if($paramType == 'int' && $paramName == 'id')
			{
				$addId = true;
				$testValues[] = '$id';
				continue;
			}
			
			$this->writeTest("	 * @param $paramType \$$paramName");
			if($actionParam->isSimpleType())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if($actionParam->getDefaultValue())
				{
					if($actionParam->getType() == 'string')
						$testParam .= " = '" . $actionParam->getDefaultValue() . "'";
					else
						$testParam .= " = " . $actionParam->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
				
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
		if($addId)
		{
			$this->writeTest("	 * @param int id - returned from testAdd");
			$testParams[] = '$id';
		}
			
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
				
		$outputType = null;
		if($outputTypeReflector)
			$outputType = $outputTypeReflector->getType();
		
		if($addId)
			$this->writeTest("	 * @depends testAdd with data set #0");
		if(count($testValues))
			$this->writeTest("	 * @dataProvider provideData");
			
		$this->writeTest("	 */");
		$this->writeTest("	public function test{$actionName}($testParams)");
		$this->writeTest("	{");
		$this->writeTest("		\$resultObject = \$this->client->{$serviceName}->{$action}($testValues);");
		if($outputType)
		$this->writeTest("		\$this->assertType('$outputType', \$resultObject);");
		$this->writeTest("	}");
		$this->writeTest("");
	}
	
	private function writeBase($txt = "")
	{
		$this->_txtBase .= $txt ."\n";
	}
	
	private function writeTest($txt = "")
	{
		$this->_txtTest .= $txt ."\n";
	}
	
	private function writeIni($txt = "")
	{
		$this->_txtIni .= $txt ."\n";
	}
	
	private function writeToFile($fileName, $contents, $overwrite = true)
	{
		if(file_exists($fileName) && !$overwrite)
		{
			KalturaLog::info("File [$fileName] already exists");
			return;
		}
			
		$dirname = dirname($fileName);
		if(!file_exists($dirname))
			mkdir($dirname, 777, true);
			
		$handle = fopen($fileName, "w");
		fwrite($handle, $contents);
		fclose($handle);
	}
}
