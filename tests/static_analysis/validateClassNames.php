<?php

$classDefs = array();
$classCalls = array();

function stripComments($fileStr)
{
	$newStr  = '';

	$commentTokens = array(T_COMMENT);

	if (defined('T_DOC_COMMENT'))
		$commentTokens[] = T_DOC_COMMENT; // PHP 5
	if (defined('T_ML_COMMENT'))
		$commentTokens[] = T_ML_COMMENT;  // PHP 4
		
	$commentTokens[] = T_CONSTANT_ENCAPSED_STRING;
	$commentTokens[] = T_NUM_STRING;
 	$commentTokens[] = T_INLINE_HTML;
	$commentTokens[] = T_ENCAPSED_AND_WHITESPACE;

	$tokens = token_get_all($fileStr);

	foreach ($tokens as $token) 
	{
		if (is_array($token)) 
		{
			if (in_array($token[0], $commentTokens))
				continue;

			$token = $token[1];
		}

		$newStr .= $token;
	}

	return $newStr;
}

function processFile($filePath, $defineOnly)
{
	global $classDefs, $classCalls;

	$fileData = stripComments(file_get_contents($filePath));

	$classes = array();
	if (preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+)~mi', $fileData, $classes))
	{
		foreach ($classes[1] as $curClass)		
			$classDefs[strtolower($curClass)] = $curClass;
	}
	
	if ($defineOnly)
	{
		return;
	}
	
	$classes = array();	
	if (preg_match_all('~[^a-zA-Z\$](\w+)::~', $fileData, $classes))
	{
		foreach ($classes[1] as $curClass)		
			$classCalls[$curClass][] = $filePath;
	}

	$classes = array();	
	if (preg_match_all('~new\s+(\w+)~', $fileData, $classes))
	{
		foreach ($classes[1] as $curClass)		
			$classCalls[$curClass][] = $filePath;
	}
}

function scanDirectory($directory, $ignoredFolders, $defineOnly = false)
{
	if (!is_dir($directory))
	{
		return;
	}

	foreach(scandir($directory) as $file)
	{
		if ($file[0] == ".") // ignore linux hidden files
		{
			continue;
		}

		$path = realpath($directory."/".$file);
		if (is_dir($path))
		{
			scanDirectory($path, array(), $defineOnly || in_array($file, $ignoredFolders));
		}
		else if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) == "php") 
		{
			processFile($path, $defineOnly);
		}
	}
}

$baseFolder = realpath(dirname(__FILE__) . '/../../');

/*

Uncomment to generate builtin classes list

scanDirectory('C:\Users\<user name>\Zend\workspaces\DefaultWorkspace\.metadata\.plugins\org.eclipse.php.core\__language__\cd2c5360', array(), true);
foreach ($classDefs as $className)
{
	print "'{$className}'," . PHP_EOL;
}
die;
*/

$builtinClasses = array(
	'stdClass',
	'Traversable',
	'IteratorAggregate',
	'Iterator',
	'ArrayAccess',
	'Serializable',
	'Exception',
	'ErrorException',
	'Closure',
	'PDOException',
	'PDO',
	'PDOStatement',
	'PDORow',
	'PharException',
	'Phar',
	'PharData',
	'PharFileInfo',
	'ReflectionException',
	'Reflection',
	'Reflector',
	'ReflectionFunctionAbstract',
	'ReflectionFunction',
	'ReflectionParameter',
	'ReflectionMethod',
	'ReflectionClass',
	'ReflectionObject',
	'ReflectionProperty',
	'ReflectionExtension',
	'LogicException',
	'BadFunctionCallException',
	'BadMethodCallException',
	'DomainException',
	'InvalidArgumentException',
	'LengthException',
	'OutOfRangeException',
	'RuntimeException',
	'OutOfBoundsException',
	'OverflowException',
	'RangeException',
	'UnderflowException',
	'UnexpectedValueException',
	'RecursiveIterator',
	'RecursiveIteratorIterator',
	'OuterIterator',
	'IteratorIterator',
	'FilterIterator',
	'RecursiveFilterIterator',
	'ParentIterator',
	'Countable',
	'SeekableIterator',
	'LimitIterator',
	'CachingIterator',
	'RecursiveCachingIterator',
	'NoRewindIterator',
	'AppendIterator',
	'InfiniteIterator',
	'RegexIterator',
	'RecursiveRegexIterator',
	'EmptyIterator',
	'RecursiveTreeIterator',
	'ArrayObject',
	'ArrayIterator',
	'RecursiveArrayIterator',
	'SplFileInfo',
	'DirectoryIterator',
	'FilesystemIterator',
	'RecursiveDirectoryIterator',
	'GlobIterator',
	'SplFileObject',
	'SplTempFileObject',
	'SplDoublyLinkedList',
	'SplQueue',
	'SplStack',
	'SplHeap',
	'SplMinHeap',
	'SplMaxHeap',
	'SplPriorityQueue',
	'SplFixedArray',
	'SplObserver',
	'SplSubject',
	'SplObjectStorage',
	'MultipleIterator',
	'SQLiteDatabase',
	'SQLiteResult',
	'SQLiteUnbuffered',
	'SQLiteException',
	'SimpleXMLElement',
	'SimpleXMLIterator',
	'COMPersistHelper',
	'com_exception',
	'com_safearray_proxy',
	'variant',
	'com',
	'dotnet',
	'DateTime',
	'DateTimeZone',
	'DateInterval',
	'DatePeriod',
	'DOMException',
	'DOMStringList',
	'DOMNameList',
	'DOMImplementationList',
	'DOMImplementationSource',
	'DOMImplementation',
	'DOMNode',
	'DOMNameSpaceNode',
	'DOMDocumentFragment',
	'DOMDocument',
	'DOMNodeList',
	'DOMNamedNodeMap',
	'DOMCharacterData',
	'DOMAttr',
	'DOMElement',
	'DOMText',
	'DOMComment',
	'DOMTypeinfo',
	'DOMUserDataHandler',
	'DOMDomError',
	'DOMErrorHandler',
	'DOMLocator',
	'DOMConfiguration',
	'DOMCdataSection',
	'DOMDocumentType',
	'DOMNotation',
	'DOMEntity',
	'DOMEntityReference',
	'DOMProcessingInstruction',
	'DOMStringExtend',
	'DOMXPath',
	'finfo',
	'ImagickException',
	'ImagickDrawException',
	'ImagickPixelIteratorException',
	'ImagickPixelException',
	'Imagick',
	'ImagickDraw',
	'ImagickPixelIterator',
	'ImagickPixel',
	'LibXMLError',
	'mysqli_sql_exception',
	'mysqli_driver',
	'mysqli',
	'mysqli_warning',
	'mysqli_result',
	'mysqli_stmt',
	'OCI_Lob',
	'OCI_Collection',
	'SoapClient',
	'SoapVar',
	'SoapServer',
	'SoapFault',
	'SoapParam',
	'SoapHeader',
	'SQLite3',
	'SQLite3Stmt',
	'SQLite3Result',
	'__PHP_Incomplete_Class',
	'php_user_filter',
	'Directory',
	'tidy',
	'tidyNode',
	'XMLReader',
	'XMLWriter',
	'XSLTProcessor',
	'ZipArchive',
	
	'Memcache',
	'parent',
	'self',
);

scanDirectory($baseFolder, array('vendor', 'tests', 'symfony', 'symfony-data', 'generator'));

foreach ($classDefs as $classNameLower => $className)
{
	unset($classCalls[$className]);
}

foreach ($builtinClasses as $className)
{
	unset($classCalls[$className]);
}

foreach ($classCalls as $className => $classFiles)
{
	$classNameLower = strtolower($className);
	if (isset($classDefs[$classNameLower]))
		echo "Error: Incorrect case for {$className}, should be {$classDefs[$classNameLower]} in:\n";
	else
		echo "Error: Unknown class {$className} in:\n";
		
	$classFiles = array_unique($classFiles);
 	foreach ($classFiles as $classFile)
	{
		echo "\t{$classFile}\n";
	}
}
