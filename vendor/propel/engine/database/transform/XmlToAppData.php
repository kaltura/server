<?php

/*
 *  $Id: XmlToAppData.php 1262 2009-10-26 20:54:39Z francois $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

include_once 'propel/engine/database/model/AppData.php';

// Phing dependencies
require_once 'phing/parser/AbstractHandler.php';
include_once 'phing/system/io/FileReader.php';

/**
 * A class that is used to parse an input xml schema file and creates an AppData
 * PHP object.
 *
 * @author     Hans Lellelid <hans@xmpl.org> (Propel)
 * @author     Leon Messerschmidt <leon@opticode.co.za> (Torque)
 * @author     Jason van Zyl <jvanzyl@apache.org> (Torque)
 * @author     Martin Poeschl <mpoeschl@marmot.at> (Torque)
 * @author     Daniel Rall <dlr@collab.net> (Torque)
 * @version    $Revision: 1262 $
 * @package    propel.engine.database.transform
 */
class XmlToAppData extends AbstractHandler {

	/** enables debug output */
	const DEBUG = false;

	private $app;
	private $platform;
	private $currDB;
	private $currTable;
	private $currColumn;
	private $currFK;
	private $currIndex;
	private $currUnique;
	private $currValidator;
	private $currBehavior;
	private $currVendorObject;

	private $isForReferenceOnly;
	private $currentPackage;
	private $currentXmlFile;
	private $defaultPackage;

	private $encoding;

	/** two-dimensional array,
		first dimension is for schemas(key is the path to the schema file),
		second is for tags within the schema */
	private $schemasTagsStack = array();

	public $parser;

	/**
	 * Creates a new instance for the specified database type.
	 *
	 * @param      Platform $platform The type of database for the application.
	 * @param      string $defaultPackage the default PHP package used for the om
	 * @param      string $encoding The database encoding.
	 */
	public function __construct(Platform $platform, $defaultPackage, $encoding = 'iso-8859-1')
	{
		$this->app = new AppData($platform);
		$this->platform = $platform;
		$this->defaultPackage = $defaultPackage;
		$this->firstPass = true;
		$this->encoding = $encoding;
	}

	/**
	 * Parses a XML input file and returns a newly created and
	 * populated AppData structure.
	 *
	 * @param      string $xmlFile The input file to parse.
	 * @return     AppData populated by <code>xmlFile</code>.
	 */
	public function parseFile($xmlFile)
	{
		// we don't want infinite recursion
		if ($this->isAlreadyParsed($xmlFile)) {
			return;
		}

		$domDocument = new DomDocument('1.0', 'UTF-8');
		$domDocument->load($xmlFile);

		// store current schema file path
		$this->schemasTagsStack[$xmlFile] = array();

		$this->currentXmlFile = $xmlFile;

		try {
			$fr = new FileReader($xmlFile);
		} catch (Exception $e) {
			$f = new PhingFile($xmlFile);
			throw new Exception("XML File not found: " . $f->getAbsolutePath());
		}

		$br = new BufferedReader($fr);

		$this->parser = new ExpatParser($br);
		$this->parser->parserSetOption(XML_OPTION_CASE_FOLDING, 0);
		$this->parser->setHandler($this);

		try {
			$this->parser->parse();
		} catch (Exception $e) {
			$br->close();
			throw $e;
		}
		$br->close();

		array_pop($this->schemasTagsStack);

		return $this->app;
	}

	/**
	 * Handles opening elements of the xml file.
	 *
	 * @param      string $uri
	 * @param      string $localName The local name (without prefix), or the empty string if
	 *		 Namespace processing is not being performed.
	 * @param      string $rawName The qualified name (with prefix), or the empty string if
	 *		 qualified names are not available.
	 * @param      string $attributes The specified or defaulted attributes
	 */
	public function startElement($name, $attributes) {

		try {

	  $parentTag = $this->peekCurrentSchemaTag();

	  if ($parentTag === false) {

				switch($name) {
					case "database":
						if ($this->isExternalSchema()) {
							$this->currentPackage = @$attributes["package"];
							if ($this->currentPackage === null) {
								$this->currentPackage = $this->defaultPackage;
							}
						} else {
							$this->currDB = $this->app->addDatabase($attributes);
						}
					break;

					default:
						$this->_throwInvalidTagException($name);
				}

			} elseif  ($parentTag == "database") {

				switch($name) {

					case "external-schema":
						$xmlFile = @$attributes["filename"];

						//"referenceOnly" attribute is valid in the main schema XML file only,
						//and it's ingnored in the nested external-schemas
						if (!$this->isExternalSchema()) {
							$isForRefOnly = @$attributes["referenceOnly"];
							$this->isForReferenceOnly = ($isForRefOnly !== null ? (strtolower($isForRefOnly) === "true") : true); // defaults to TRUE
						}

						if ($xmlFile[0] != '/') {
							$f = new PhingFile($this->currentXmlFile);
							$xf = new PhingFile($f->getParent(), $xmlFile);
							$xmlFile = $xf->getPath();
						}

						$this->parseFile($xmlFile);
					break;

    		  case "domain":
					  $this->currDB->addDomain($attributes);
				  break;

					case "table":
						$this->currTable = $this->currDB->addTable($attributes);
						if ($this->isExternalSchema()) {
							$this->currTable->setForReferenceOnly($this->isForReferenceOnly);
							$this->currTable->setPackage($this->currentPackage);
						}
					break;

					case "vendor":
						$this->currVendorObject = $this->currDB->addVendorInfo($attributes);
					break;

					case "behavior":
					  $this->currBehavior = $this->currDB->addBehavior($attributes);
					break;

					default:
						$this->_throwInvalidTagException($name);
				}

			} elseif  ($parentTag == "table") {

				switch($name) {
					case "column":
						$this->currColumn = $this->currTable->addColumn($attributes);
					break;

					case "foreign-key":
						$this->currFK = $this->currTable->addForeignKey($attributes);
					break;

					case "index":
						$this->currIndex = $this->currTable->addIndex($attributes);
					break;

					case "unique":
						$this->currUnique = $this->currTable->addUnique($attributes);
					break;

					case "vendor":
						$this->currVendorObject = $this->currTable->addVendorInfo($attributes);
					break;

		  		case "validator":
					  $this->currValidator = $this->currTable->addValidator($attributes);
		  		break;

		  		case "id-method-parameter":
						$this->currTable->addIdMethodParameter($attributes);
					break;
          
					case "behavior":
					  $this->currBehavior = $this->currTable->addBehavior($attributes);
					break;
					
					default:
						$this->_throwInvalidTagException($name);
				}

			} elseif  ($parentTag == "column") {

				switch($name) {
					case "inheritance":
						$this->currColumn->addInheritance($attributes);
					break;

					case "vendor":
						$this->currVendorObject = $this->currColumn->addVendorInfo($attributes);
					break;

					default:
						$this->_throwInvalidTagException($name);
				}

			} elseif ($parentTag == "foreign-key") {

				switch($name) {
					case "reference":
						$this->currFK->addReference($attributes);
					break;

					case "vendor":
						$this->currVendorObject = $this->currUnique->addVendorInfo($attributes);
					break;

					default:
						$this->_throwInvalidTagException($name);
				}

			} elseif  ($parentTag == "index") {

				switch($name) {
					case "index-column":
						$this->currIndex->addColumn($attributes);
					break;

					case "vendor":
						$this->currVendorObject = $this->currIndex->addVendorInfo($attributes);
					break;

					default:
						$this->_throwInvalidTagException($name);
				}

			} elseif ($parentTag == "unique") {

				switch($name) {
					case "unique-column":
						$this->currUnique->addColumn($attributes);
					break;

					case "vendor":
						$this->currVendorObject = $this->currUnique->addVendorInfo($attributes);
					break;

					default:
						$this->_throwInvalidTagException($name);
				}
			} elseif ($parentTag == "behavior") {

				switch($name) {
					case "parameter":
						$this->currBehavior->addParameter($attributes);
					break;

					default:
						$this->_throwInvalidTagException($name);
				}
			} elseif ($parentTag == "validator") {
				switch($name) {
					case "rule":
						$this->currValidator->addRule($attributes);
					break;
					default:
						$this->_throwInvalidTagException($name);
				}
			} elseif ($parentTag == "vendor") {

				switch($name) {
					case "parameter":
						$this->currVendorObject->addParameter($attributes);
					break;
					default:
						$this->_throwInvalidTagException($name);
				}

			} else {
				// it must be an invalid tag
				$this->_throwInvalidTagException($name);
			}

			$this->pushCurrentSchemaTag($name);

		} catch (BuildException $e) {
			throw $e;
		} catch (Exception $e) {
			echo $e;
			echo "\n";
			throw $e;
		}
	}

	function _throwInvalidTagException($tag_name)
	{
		throw new BuildException("Unexpected tag <" . $tag_name . ">", $this->parser->getLocation());
	}

	/**
	 * Handles closing elements of the xml file.
	 *
	 * @param      uri
	 * @param      localName The local name (without prefix), or the empty string if
	 *		 Namespace processing is not being performed.
	 * @param      rawName The qualified name (with prefix), or the empty string if
	 *		 qualified names are not available.
	 */
	public function endElement($name)
	{
		if (self::DEBUG) {
			print("endElement(" . $name . ") called\n");
		}

		$this->popCurrentSchemaTag();
	}

	protected function peekCurrentSchemaTag()
	{
				$keys = array_keys($this->schemasTagsStack);
		return end($this->schemasTagsStack[end($keys)]);
	}

	protected function popCurrentSchemaTag()
	{
				$keys = array_keys($this->schemasTagsStack);
		array_pop($this->schemasTagsStack[end($keys)]);
	}

	protected function pushCurrentSchemaTag($tag)
	{
				$keys = array_keys($this->schemasTagsStack);
		$this->schemasTagsStack[end($keys)][] = $tag;
	}

	protected function isExternalSchema()
	{
		return (sizeof($this->schemasTagsStack) > 1);
	}

	protected function isAlreadyParsed($filePath)
	{
		return isset($this->schemasTagsStack[$filePath]);
	}
}
