<?php
class UnitTestsGenerator extends ClientGeneratorFromPhp 
{
	private $_txtBase = "";
	private $_txtTest = "";
	private $_txtIni = "";
	private $_txtXml = "";
	private $_txtXmlSource = "";
	
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
		
		$bootstrapPath = '/../../bootstrap.php';
		
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
			$bootstrapPath = str_repeat('/..', $upCounter) . '/tests/bootstrap.php';
		}
		
		$this->_txtBase = '';
		$this->_txtTest = '';
		$this->_txtIni = '';
		$this->_txtXml = '';
		$this->_txtXmlSource = '';
		
		$this->writeXml("<?xml version='1.0'?>");
		$this->writeXmlSource("<?xml version='1.0'?>");
		$this->writeXmlSource("<TestsDataSource>");
		
		$this->writeBase("<?php");
		$this->writeBase("");
		$this->writeBase("/**");
		$this->writeBase(" * $serviceName service base test case.");
		$this->writeBase(" */");
		$this->writeBase("abstract class {$serviceClass}TestBase extends KalturaApiTestCase");
		$this->writeBase("{");
		
		
		$this->writeTest("<?php");
		$this->writeTest("");
		$this->writeTest("require_once(dirname(__FILE__) . '$bootstrapPath');");
		$this->writeTest("require_once(dirname(__FILE__) . '/{$serviceClass}TestBase.php');");
		$this->writeTest("");
		$this->writeTest("/**");
		$this->writeTest(" * $serviceName service test case.");
		$this->writeTest(" */");
		$this->writeTest("class {$serviceClass}Test extends {$serviceClass}TestBase");
		$this->writeTest("{");

		$this->writeIni("[config]");
		$this->writeIni("source                                            = xml");
		$this->writeIni("serviceUrl                                        = http://devtests.kaltura.dev/");
		$this->writeIni("partnerId                                         = 495787");
		$this->writeIni("clientTag                                         = unitTest");
		$this->writeIni("curlTimeout                                       = 90");
		$this->writeIni("startSession                                      = 1");
		$this->writeIni("secret                                            = 2dc17b5563696fceb430a8431a2e4a32");
		$this->writeIni("userId                                            = ");
		$this->writeIni("sessionType                                       = 0");
		$this->writeIni("expiry                                            = 86400");
		$this->writeIni("privileges                                        = ");
		
		$this->writeXml("<TestCaseData testCaseName='{$serviceClass}Test'>");
		$this->writeXmlSource("	<TestCaseData testCaseName='{$serviceClass}Test'>");
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
			
			if($actionInfo->serverOnly)
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

		$this->writeXml("</TestCaseData>"); // Close the XML tag for the test case
		$this->writeXmlSource("	</TestCaseData>"); // Close the XML tag for the test case
		$this->writeXmlSource("</TestsDataSource>");
		
		$this->writeToFile("$testPath/{$serviceClass}TestBase.php", $this->_txtBase);
		$this->writeToFile("$testPath/{$serviceClass}Test.php", $this->_txtTest, false);
		$this->writeToFile("$testPath/{$serviceClass}Test.php.ini", $this->_txtIni, false);
		$this->writeToFile("$testPath/testsData/{$serviceClass}Test.data", $this->_txtXml, false);
		$this->writeToFile("$testPath/testsData/{$serviceClass}Test.config", $this->_txtXmlSource, false); //TODO: change the file extension to source
	}

	protected function writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, KalturaParamInfo $outputTypeReflector = null, $testReturnedType = 'int')
	{
		$actionName = ucfirst($action);
		
		$this->writeIni("");
		$this->writeIni("[test{$actionName}]");
		$this->writeXml("<TestProcedureData testProcedureName='test$actionName'>");
		$this->writeXml("	<TestCaseData testCaseInstanceName='test$actionName with data set #0'>");
				
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
				if($paramName == 'type')
				{
					$this->writeIni("test1.objType.$paramName = dependency");
				}
				else
				{
					$this->writeIni("test1.$paramName.type = dependency");
				}
				
				$this->writeXml("		<Input name = '$paramName' type = '$paramType' key = ''>");
				$this->writeXml("		</Input>");
				continue;
			}
		
			if($actionParam->isSimpleType() || $actionParam->isEnum())
			{
				$paramDefaultValue = $actionParam->getDefaultValue();
				$this->writeIni("test1.$paramName = " . $paramDefaultValue );
				$this->writeXml("		<Input name = '$paramName' type = '$paramType' key = '$paramDefaultValue'>");
				$this->writeXml("		</Input>");
			}
			elseif($actionParam->isFile())
			{
				$this->writeIni("test1.$paramName.type = file");
				$this->writeIni("test1.$paramName.path = ");
				$this->writeXml("		<Input name = '$paramName' type = 'file' key = ''>");
				$this->writeXml("		</Input>");
			}
			else
			{
				if($paramName == 'type')
				{
					$this->writeIni("test1.objType.$paramName = $paramType");
				}
				else
				{
					$this->writeIni("test1.$paramName.type = $paramType");
				}
				
				$this->writeXml("		<Input name = '$paramName' type = '$paramType' key = ''>");
				
				$actionParamProperties = $actionParam->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly() || $actionParamProperty->isInsertOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType() || $actionParamProperty->isEnum())
					{
						$defaultValue = $actionParamProperty->getDefaultValue();
						$this->writeIni("test1.$paramName.$propertyName = " . $defaultValue);
						$this->writeXml("			<$propertyName>$defaultValue</$propertyName>");
					}
					elseif($actionParamProperty->isFile())
					{
						$this->writeIni("test1.$paramName.$propertyName.type = file");
						$this->writeIni("test1.$paramName.$propertyName.path = ");
						$this->writeXml("			<$propertyName>file not supported yet...</$propertyName>");
					}
					else
					{
						if($propertyName == 'type')
						{
							$this->writeIni("test1.$paramName.objType.$propertyName = $propertyType");
						}
						else
						{
							$this->writeIni("test1.$paramName.$propertyName.type = $propertyType");
						}
						
						$this->writeXml("			<$propertyName>$propertyType</$propertyName>");
					}
				}
				$this->writeXml("		</Input>");
			}
			
			$paramDesc = $actionParam->getDescription();
			$this->writeBase("	 * @param $paramType \$$paramName $paramDesc");
			
			if(!$actionParam->isComplexType() || //it the param is not: complex
			   $actionParam->isEnum() || //or it is an enum then we dont print the type 
			   $actionParam->isStringEnum() || 
			   $actionParam->isDynamicEnum())
					$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if ($actionParam->isSimpleType())
				{
					$defaultValue = $actionParam->getDefaultValue();
					
					if ($defaultValue === "false")
						$testParam .= " = false";
					else if ($defaultValue === "true")
						$testParam .= " = true";
					else if ($defaultValue === "null")
						$testParam .= " = null";
					else if ($paramType == "string")
						$testParam .= " = \"$defaultValue\"";
					else if ($paramType == "int")
					{
						if ($defaultValue == "")
							$testParam .= " = \"\""; // hack for partner.getUsage
						else
							$testParam .= " = $defaultValue";
					} 
				}
				else
					$testParam .= " = null";
			}
					
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
			$validateValues[] = "\$$paramName";
		}
		
		if($outputTypeReflector)
		{
			$paramType = $outputTypeReflector->getType();
			$paramName = $outputTypeReflector->getName();
			
			if($outputTypeReflector->isSimpleType() || $outputTypeReflector->isEnum())
			{
				$this->writeIni("test1.reference = " . $outputTypeReflector->getDefaultValue());
				$this->writeXml("		<OutputReference name = '$paramName' type = '$paramType' key = '$defaultValue' >");
				$this->writeXml("		</OutputReference>");
			}
			elseif($outputTypeReflector->isFile())
			{
				$this->writeIni("test1.reference.type = file");
				$this->writeIni("test1.reference.path = ");
				$this->writeXml("		<OutputReference name = '$paramName' type = 'file' key = 'path/to/file'>");
				$this->writeXml("		</OutputReference>");
			}
			else
			{
				$this->writeIni("test1.reference.type = $paramType");
				$this->writeXml("		<OutputReference name = '$paramName' type = '$paramType' key = 'object key'>");
				
				$actionParamProperties = $outputTypeReflector->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType() || $actionParamProperty->isEnum())
					{
						$defaultValue = $actionParamProperty->getDefaultValue();
						$this->writeIni("test1.reference.$propertyName = " . $defaultValue);
						$this->writeXml("			<$propertyName>$defaultValue</$propertyName>");
					}
					elseif($actionParamProperty->isFile())
					{
						$this->writeIni("test1.reference.$propertyName.type = file");
						$this->writeIni("test1.reference.$propertyName.path = ");
						$this->writeXml("			<$propertyName>'file is not supported yet'</$propertyName>");
					}
					else
					{
						if($propertyName == 'type')
						{
							$this->writeIni("test1.reference.objType.$propertyName = $propertyType");
						}
						else
						{
							$this->writeIni("test1.reference.$propertyName.type = $propertyType");
						}
						
						$this->writeXml("			<$propertyName>'$propertyType'</$propertyName>");
					}
				}
			}
			$this->writeXml("		</OutputReference>");
			
			$paramDesc = $outputTypeReflector->getDescription();
			$this->writeBase("	 * @param $paramType \$reference $paramDesc");
			if(!$outputTypeReflector->isComplexType())
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
		
		
		$this->writeXml("	</TestCaseData>");
		$this->writeXml("</TestProcedureData>");
		
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
		{
			$this->writeBase("		\$this->assertType('$outputType', \$resultObject);");
			$this->writeBase("		\$this->compareApiObjects('$paramName', \$reference);");
		}
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
			
		if(in_array($action, array("list", "clone", "goto")))
			$action = "{$action}Action";
		
		KalturaLog::info("Generates action [$serviceName.$action]");
		if($action == 'add')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'update')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'listAction')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector, null);
		if($action == 'get')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector);
		if($action == 'delete')
			return $this->writeServiceBaseAction($serviceId, $serviceName, $action, $actionParams, null, null);
			
		$actionName = ucfirst($action);
		
		$this->writeIni("");
		$this->writeIni("[test{$actionName}]");

		$this->writeXml("<TestProcedureData testProcedureName='test$actionName'>");
		$this->writeXml("	<TestCaseData testCaseInstanceName='test$actionName with data set #0'>");
		
		$this->writeXmlSource("		<TestProcedureData testProcedureName='test$actionName'>");
		$this->writeXmlSource("			<TestCaseData testCaseInstanceName='test$actionName with template data set'>");
		
		$this->writeTest("	/**");
		$this->writeTest("	 * Tests {$serviceName}->{$action} action");
		
		$testParams = array();
		$testValues = array();
		$addId = false;
		foreach($actionParams as $actionParam)
		{
			$paramType = $actionParam->getType();
			$paramName = $actionParam->getName();
			$this->writeXmlSource("				<Input name = '$paramName' type = '$paramType' key = 'Fill object key'/>");
			
			if($paramType == 'int' && $paramName == 'id')
			{
				$addId = true;
				$testValues[] = '$id';
				continue;
			}
			
			$this->writeTest("	 * @param $paramType \$$paramName");
			if(!$actionParam->isComplexType() || //it the param is not: complex
			   $actionParam->isEnum() || //or it is an enum then we dont print the type 
			   $actionParam->isStringEnum() || 
			   $actionParam->isDynamicEnum())
				$testParam = "\$$paramName";
			else
				$testParam = "$paramType \$$paramName";
				
			if($actionParam->isOptional())
			{
				if ($actionParam->isSimpleType())
				{
					$defaultValue = $actionParam->getDefaultValue();
					
					if ($defaultValue === "false")
						$testParam .= " = false";
					else if ($defaultValue === "true")
						$testParam .= " = true";
					else if ($defaultValue === "null")
						$testParam .= " = null";
					else if ($paramType == "string")
						$testParam .= " = \"$defaultValue\"";
					else if ($paramType == "int")
					{
						if ($defaultValue == "")
							$testParam .= " = \"\""; // hack for partner.getUsage
						else
							$testParam .= " = $defaultValue";
					} 
				}
				else
					$testParam .= " = null";
			}
				
			$testParams[] = $testParam;
			$testValues[] = "\$$paramName";
		}
		
		if($outputTypeReflector)
		{
			$paramType = $outputTypeReflector->getType();
			$paramName = $outputTypeReflector->getName();
			$this->writeXmlSource("				<OutputReference name = '$paramName' type = '$paramType' key = 'Fill the object key' />");
			
			if($outputTypeReflector->isSimpleType() || $outputTypeReflector->isEnum())
			{
				$defaultValue = $outputTypeReflector->getDefaultValue();
				
				$this->writeIni("test1.reference = " . $defaultValue );
				$this->writeXml("		<OutputReference name = '$paramName' type = '$paramType' key = '$defaultValue' >");
				
			}
			elseif($outputTypeReflector->isFile())
			{
				$this->writeIni("test1.reference.type = file");
				$this->writeIni("test1.reference.path = ");
				
				//TODO: add support for files in XML
				$this->writeXml("		<OutputReference name = '$paramName' type='file' key='path/to/file'>");
				$this->writeXml("		</OutputReference>");
			}
			else
			{
				$this->writeIni("test1.reference.type = $paramType");
				$this->writeXml("		<OutputReference name = '$paramName' type = '$paramType' key = ''>");
								
				$actionParamProperties = $outputTypeReflector->getTypeReflector()->getProperties();
				foreach($actionParamProperties as $actionParamProperty)
				{
					if($actionParamProperty->isReadOnly())
						continue;
						
					$propertyType = $actionParamProperty->getType();
					$propertyName = $actionParamProperty->getName();
					
					if($actionParamProperty->isSimpleType() || $actionParamProperty->isEnum())
					{
						$paramDefaultValue = $actionParamProperty->getDefaultValue();
						$this->writeIni("test1.reference.$propertyName = " . $paramDefaultValue);
						$this->writeXml("			<$propertyName>$paramDefaultValue</$propertyName>");
					}
					elseif($actionParamProperty->isFile())
					{
						$this->writeIni("test1.reference.$propertyName.type = file");
						$this->writeIni("test1.reference.$propertyName.path = ");
						
						//TODO: add support for files in XML
						$this->writeXml("			<OutputReference name = '$paramName' type='file' key= 'path/to/file'>");
					}
					else
					{
						if($propertyName == 'type')
						{
							//Causes bug in the Zend config
							$this->writeIni("test1.reference.objType.$propertyName = $propertyType");
						}
						else
						{
							$this->writeIni("test1.reference.$propertyName.type = $propertyType");
						}
						$this->writeXml("			<$propertyName>$propertyType</$propertyName>");
					}
				}
			}
			$this->writeXml("		</OutputReference>");
			
			$this->writeTest("	 * @param $paramType \$reference");
			if(!$outputTypeReflector->isComplexType())
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
		
		$this->writeXml("	</TestCaseData>");
		$this->writeXml("</TestProcedureData>");
	
		$this->writeXmlSource("			</TestCaseData>");
		$this->writeXmlSource("		</TestProcedureData>");
	
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
	
	/**
	 * 
	 * Writes data to the ini var
	 * @param string $txt
	 */
	private function writeIni($txt = "")
	{
		$this->_txtIni .= $txt ."\n";
	}
	
	/**
	 * 
	 * Writes data to the xml var
	 * @param string $txt
	 */
	private function writeXml($txt = "")
	{
		$this->_txtXml .= $txt ."\n";
	}
		
	/**
	 * 
	 * Writes data to the xml var
	 * @param string $txt
	 */
	private function writeXmlSource($txt = "")
	{
		$this->_txtXmlSource .= $txt ."\n";
	}
	
	/**
	 * 
	 * Writes a given string into a given file (creates if non exists)
	 * @param string $fileName
	 * @param string $contents
	 * @param bool $overwrite
	 */
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
