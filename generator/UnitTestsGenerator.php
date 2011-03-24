<?php
class UnitTestsGenerator extends ClientGeneratorFromPhp 
{
	private $_txtBase = "";
	private $_txtTest = "";
	private $_txtIni = "";
	private $lastDependencyTest = "testFunction";
	
	protected function writeHeader(){}
	protected function writeFooter(){}
	protected function writeBeforeServices(){}
	protected function writeAfterServices(){}
	protected function writeBeforeTypes(){}
	protected function writeAfterTypes(){}
	protected function writeType(KalturaTypeReflector $type){}

	public function generate() 
	{
		parent::generate();
	
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
				$currentFolder = realpath("$currentFolder/../");
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
		$this->writeIni("serviceUrl                                        = http://localhost/");
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
		$this->writeTest("	/**");
		$this->writeTest("	 * Called when all tests are done");
		$this->writeTest("	 * @param int \$id");
		$this->writeTest("	 * @return int");
		$this->writeTest("	 * @depends {$this->lastDependencyTest} - TODO: replace {$this->lastDependencyTest} with last test function that uses that id");
		$this->writeTest("	 */");
		$this->writeTest("	public function testFinished(\$id)");
		$this->writeTest("	{");
		$this->writeTest("		return \$id;");
		$this->writeTest("	}");
		$this->writeTest("");
		$this->writeTest("}");
		
	
		$this->writeBase("	/**");
		$this->writeBase("	 * Called when all tests are done");
		$this->writeBase("	 * @param int \$id");
		$this->writeBase("	 * @return int");
		$this->writeBase("	 */");
		$this->writeBase("	abstract public function testFinished(\$id);");
		$this->writeBase("");
		$this->writeBase("}");
		
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

	protected function writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, KalturaParamInfo $outputTypeReflector = null, $testReturnedType = 'int')
	{
		$actionName = ucfirst($action);
		
		$this->writeIni("");
		$this->writeIni("[test{$actionName}]");
		
		$this->writeBase("	/**");
		$this->writeBase("	 * Tests {$serviceName}->{$action} action");
	
		$testParams = array();
		$testValues = array();
		$validateValues = array();
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
		
			if($actionParam->isSimpleType() || $actionParam->isEnum())
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
					if($actionParamProperty->isReadOnly() || $actionParamProperty->isInsertOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType() || $actionParamProperty->isEnum())
						$this->writeIni("test1.$paramName.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
				}
			}
			
			$paramDesc = $actionParam->getDescription();
			$this->writeBase("	 * @param $paramType \$$paramName $paramDesc");
			if($actionParam->isSimpleType() || $actionParam->isEnum())
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
			$validateValues[] = "\$$paramName";
		}
		
		if($outputTypeReflector)
		{
			$paramType = $outputTypeReflector->getType();
			if($outputTypeReflector->isSimpleType() || $outputTypeReflector->isEnum())
			{
				$this->writeIni("test1.reference = " . $outputTypeReflector->getDefaultValue());
			}
			elseif($outputTypeReflector->isFile())
			{
				$this->writeIni("test1.reference.type = file");
				$this->writeIni("test1.reference.path = ");
			}
			else
			{
				$this->writeIni("test1.reference.type = $paramType");
				$actionParamProperties = $outputTypeReflector->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType() || $actionParamProperty->isEnum())
						$this->writeIni("test1.reference.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.reference.$propertyName.type = $propertyType");
				}
			}
			
			$paramDesc = $outputTypeReflector->getDescription();
			$this->writeBase("	 * @param $paramType \$reference $paramDesc");
			if($outputTypeReflector->isSimpleType() || $outputTypeReflector->isEnum())
				$testParam = "\$reference";
			else
				$testParam = "$paramType \$reference";
				
			if($outputTypeReflector->isOptional())
			{
				if($outputTypeReflector->getDefaultValue())
				{
					if($outputTypeReflector->getType() == 'string')
						$testParam .= " = '" . $outputTypeReflector->getDefaultValue() . "'";
					else
						$testParam .= " = " . $outputTypeReflector->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
			$testParams[] = $testParam;
			$validateValues[] = "\$reference";
		}
		
		if($addId)
		{
			$this->writeBase("	 * @param int id - returned from testAdd");
			$testParams[] = '$id';
		}
		
		$testParams = implode(', ', $testParams);
		$testValues = implode(', ', $testValues);
		$validateValues = implode(', ', $validateValues);
			
		$outputType = null;
		if($outputTypeReflector)
			$outputType = $outputTypeReflector->getType();
		
		if($testReturnedType)
		{
			$this->lastDependencyTest = "test{$actionName}";
			$this->writeBase("	 * @return $testReturnedType");
		}
		
		if($addId)
		{
			if($testReturnedType)
				$this->writeBase("	 * @depends testAdd with data set #0");
			else
				$this->writeBase("	 * @depends testFinished");
		}
		
		$this->writeBase("	 * @dataProvider provideData");
		$this->writeBase("	 */");
		$this->writeBase("	public function test{$actionName}($testParams)");
		$this->writeBase("	{");
		$this->writeBase("		\$resultObject = \$this->client->{$serviceName}->{$action}($testValues);");
		if($outputType)
		$this->writeBase("		\$this->assertType('$outputType', \$resultObject);");
		if($testReturnedType)
		$this->writeBase("		\$this->assertNotNull(\$resultObject->id);");
		$this->writeBase("		\$this->validate{$actionName}($validateValues);");
		if($testReturnedType)
		$this->writeBase("		return \$resultObject->id;");
		$this->writeBase("	}");
		$this->writeBase("");
		
		$this->writeBase("	/**");
		$this->writeBase("	 * Validates test{$actionName} results");
		$this->writeBase("	 */");
		$this->writeBase("	protected function validate{$actionName}($testParams)");
		$this->writeBase("	{");
		// TODO - add compare based on object type
		$this->writeBase("	}");
		$this->writeBase("");
		
		$this->writeTest("	/**");
		$this->writeTest("	 * Validates test{$actionName} results");
		$this->writeTest("	 */");
		$this->writeTest("	protected function validate{$actionName}($testParams)");
		$this->writeTest("	{");
		$this->writeTest("		parent::validate{$actionName}($validateValues);");
		$this->writeTest("		// TODO - add your own validations here");
		$this->writeTest("	}");
		$this->writeTest("");
	}
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
		if($outputTypeReflector && $outputTypeReflector->isFile())
			return;
			
		KalturaLog::info("Generates action [$serviceName.$action]");
		if($action == 'add')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'update')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'list')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector, null);
		if($action == 'get')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'delete')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, null, null);
			
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
			if($actionParam->isSimpleType() || $actionParam->isEnum())
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
		
		if($outputTypeReflector)
		{
			$paramType = $outputTypeReflector->getType();
			if($outputTypeReflector->isSimpleType() || $outputTypeReflector->isEnum())
			{
				$this->writeIni("test1.reference = " . $outputTypeReflector->getDefaultValue());
			}
			elseif($outputTypeReflector->isFile())
			{
				$this->writeIni("test1.reference.type = file");
				$this->writeIni("test1.reference.path = ");
			}
			else
			{
				$this->writeIni("test1.reference.type = $paramType");
				$actionParamProperties = $outputTypeReflector->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType() || $actionParamProperty->isEnum())
						$this->writeIni("test1.reference.$propertyName = " . $actionParamProperty->getDefaultValue());
					else
						$this->writeIni("test1.reference.$propertyName.type = $propertyType");
				}
			}
			$this->writeTest("	 * @param $paramType \$reference");
			if($outputTypeReflector->isSimpleType() || $outputTypeReflector->isEnum())
				$testParam = "\$reference";
			else
				$testParam = "$paramType \$reference";
				
			if($outputTypeReflector->isOptional())
			{
				if($outputTypeReflector->getDefaultValue())
				{
					if($outputTypeReflector->getType() == 'string')
						$testParam .= " = '" . $outputTypeReflector->getDefaultValue() . "'";
					else
						$testParam .= " = " . $outputTypeReflector->getDefaultValue();
				}
				else
				{
					$testParam .= " = null";
				}
			}
			$testParams[] = $testParam;
			$testValues[] = "\$reference";	
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
		$this->writeTest("		// TODO - add here your own validations");
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
			mkdir($dirname, 0777, true);
			
		$handle = fopen($fileName, "w");
		fwrite($handle, $contents);
		fclose($handle);
	}
}
