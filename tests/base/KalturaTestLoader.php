<?php

class KalturaTestLoader extends PHPUnit_Runner_StandardTestSuiteLoader
{
    /**
     * @param  string  $suiteClassName
     * @param  string  $suiteClassFile
     * @param  boolean $syntaxCheck
     * @return ReflectionClass
     * @throws RuntimeException
     */
    public function load($suiteClassName, $suiteClassFile = '', $syntaxCheck = FALSE)
    {
    	print("in KalturaTestLoader::load() suiteClassName [$suiteClassName], suiteClassFile [$suiteClassFile] \n");
    	
        $suiteClassName = str_replace('.php', '', $suiteClassName);

        if (empty($suiteClassFile)) {
        	print("1\n");
            $suiteClassFile = PHPUnit_Util_Filesystem::classNameToFilename(
              $suiteClassName
            );
        }

        if (!class_exists($suiteClassName, FALSE)) {
        	print("2\n");
            if (!file_exists($suiteClassFile)) {
                $includePaths = explode(PATH_SEPARATOR, get_include_path());

                foreach ($includePaths as $includePath) {
                    $file = $includePath . DIRECTORY_SEPARATOR .
                            $suiteClassFile;

                    if (file_exists($file)) {
                        $suiteClassFile = $file;
                        break;
                    }
                }
            }

            PHPUnit_Util_Class::collectStart();
            PHPUnit_Util_Fileloader::checkAndLoad($suiteClassFile, $syntaxCheck);
            $loadedClasses = PHPUnit_Util_Class::collectEnd();
        }

        if (!class_exists($suiteClassName, FALSE) && !empty($loadedClasses)) {
        	print("3\n");
            $offset = 0 - strlen($suiteClassName);

            foreach ($loadedClasses as $loadedClass) {
                if (substr($loadedClass, $offset) === $suiteClassName) {
                    $suiteClassName = $loadedClass;
                    break;
                }
            }
        }

        if (!class_exists($suiteClassName, FALSE) && !empty($loadedClasses)) {
        	print("4\n");
        	print("Setting testCaseClasses = PHPUnit_Framework_TestCase");
            $testCaseClass = 'KalturaTestCaseBase';

            foreach ($loadedClasses as $loadedClass) {
                $class     = new ReflectionClass($loadedClass);
                $classFile = $class->getFileName();

                if ($class->isSubclassOf($testCaseClass) &&
                    !$class->isAbstract()) {
                    $suiteClassName = $loadedClass;
                    $testCaseClass  = $loadedClass;

                    if ($classFile == realpath($suiteClassFile)) {
                        break;
                    }
                }
				print("4.5\n");
                if ($class->hasMethod('suite')) {
                	print("5\n");
                    $method = $class->getMethod('suite');

                    if (!$method->isAbstract() &&
                        $method->isPublic() &&
                        $method->isStatic()) {
                        $suiteClassName = $loadedClass;

                        if ($classFile == realpath($suiteClassFile)) {
                            break;
                        }
                    }
                }
            }
        }

        if (class_exists($suiteClassName, FALSE)) {
        	print("6\n");
//            $class = new KalturaTestSuite($suiteClassName);
			$class = new ReflectionClass($suiteClassName);
			//print("class [" . print_r($class, true). "]");
			
            if ($class->getFileName() == realpath($suiteClassFile)) {
				return $class;
            }
        }

        throw new PHPUnit_Framework_Exception(
          sprintf(
            'Class %s could not be found in %s.',

            $suiteClassName,
            $suiteClassFile
          )
		);
		print("7\n");
    }
    
}