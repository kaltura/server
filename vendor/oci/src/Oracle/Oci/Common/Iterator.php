<?php

namespace Oracle\Oci\Common;

use ArgumentCountError;
use BadMethodCallException;
use InvalidArgumentException;
use Iterator;
use Oracle\Oci\Common\Logging\Logger;

/**
 * This class stores a table of iterator configs for paginated operations
 * that do not use the original OCI paginated lists convention.
 * That convention is:
 * - operationId starts with "list"
 * - next page token is returned in "opc-next-page" response header
 * - next page is set as "page" query parameter in the next request
 * - items are a top level array "['item1', 'item2']"
 * If paginated operations do not follow that convention, they can be added
 * to this class and still supported.
 * The methods in this class allow the user to create response and item iterators
 * for all paginated operations (those that follow the convention
 * and those that are added to the iterator configs table).
 */
abstract class Iterators
{
    /*map from operationName => IteratorConfig*/ private $iteratorConfigs;
    /*AbstractClient*/ protected $client;

    /**
     * Create the iterators object.
     * @param AbstractClient $client the OCI client to use
     * @param array $iteratorConfigs map from operationId to IteratorConfig
     */
    public function __construct(AbstractClient $client, $iteratorConfigs)
    {
        $this->client = $client;
        $this->iteratorConfigs = $iteratorConfigs;
    }

    /**
     * Create a response iterator for the specified operation, with the
     * specified original parameters.
     * @param string $operation operationId of the operation to be called, e.g. "listObjects"
     * @param array $originalParams parameters for the first request.
     * @return OciResponseIterator iterator providing one response at a time (i.e. one page at a time)
     */
    public function responseIterator(
        /*string*/
        $operation,
        /*array*/
        $originalParams
    ) {
        $iteratorConfig = $this->getIteratorConfig($operation);
        return new OciResponseIterator(
            $this->client,
            $operation,
            $originalParams,
            $iteratorConfig->getNextTokenResponseGetter(),
            $iteratorConfig->getPageRequestSetter()
        );
    }

    /**
     * Create an item iterator for the specified operation, with the
     * specified original parameters.
     * @param string $operation operationId of the operation to be called, e.g. "listObjects"
     * @param array $originalParams parameters for the first request.
     * @return OciItemIterator iterator providing one item at a time (i.e. one row at a time)
     */
    public function itemIterator(
        /*string*/
        $operation,
        /*[]*/
        $originalParams
    ) {
        $iteratorConfig = $this->getIteratorConfig($operation);
        return new OciItemIterator(
            $this->client,
            $operation,
            $originalParams,
            $iteratorConfig->getNextTokenResponseGetter(),
            $iteratorConfig->getPageRequestSetter(),
            $iteratorConfig->getResponseItemsGetter()
        );
    }

    /**
     * Get the iterator config for the specified operation from the table,
     * or if not in the table, return a default iterator config, which means
     * the operation matches the OCI convention.
     * @param string $operation operationId of the operation
     * @return IteratorConfig iterator config for the operation
     */
    private function getIteratorConfig($operation)
    {
        if (array_key_exists($operation, $this->iteratorConfigs)) {
            return $this->iteratorConfigs[$operation];
        } else {
            return new IteratorConfig();
        }
    }

    /**
     * Return true if the named operation has an iterator config in the table.
     * @param string $operation operationId of the operation
     * @return bool true if the operation has an iterator config in the table.
     */
    public function hasIteratorForOperation($operation)
    {
        return array_key_exists($operation, $this->iteratorConfigs);
    }
}

/**
 * This class encapsulates how a paginated operation is used.
 * - The $nextTokenResponseGetter* properties specify how the next page token is retrieved from the response.
 * - The $pageRequestSetter* properties specify how the next page token is set on the next request.
 * - The $responseItemsGetter* properties specify how an OciItemIterator retrieves the items from a response.
 *
 * These are all triples, where *Class indicates the class name; *Method indicates the static method name;
 * and $*Args indicates the single argument passed to that class.
 *
 * The function that is invoked must be a static function, take in a single argument, and return a
 * function that performs the task. For $nextTokenResponseGetter* and $responseItemsGetter*, the returned function
 * must take an OciResponse object as single parameter; for $pageRequestSetter*, the function must
 * take a reference to a params array and the token.
 *
 * As examples for these functions, look at:
 * - $nextTokenResponseGetter* -- OciResponseIterator::buildNextTokenResponseGetterFromHeader
 * - $pageRequestSetter* -- OciResponseIterator::buildPageRequestSetterToParams
 * - $responseItemsGetter* -- OciItemIterator::buildResponseItemsGetter
 */
class IteratorConfig
{
    /*string*/ private $nextTokenResponseGetterClass = OciResponseIterator::class;
    /*string*/ private $nextTokenResponseGetterMethod = null;
    /*string*/ private $nextTokenResponseGetterArgs = null;
    /*string*/ private $pageRequestSetterClass = OciResponseIterator::class;
    /*string*/ private $pageRequestSetterMethod = null;
    /*string*/ private $pageRequestSetterArgs = null;
    /*string*/ private $responseItemsGetterClass = OciItemIterator::class;
    /*string*/ private $responseItemsGetterMethod = null;
    /*string*/ private $responseItemsGetterArgs = null;

    public function __construct($params = [])
    {
        foreach ($params as $k => $v) {
            if (!property_exists($this, $k)) {
                throw new InvalidArgumentException("Class " . static::class . " does not have a $k property.");
            }
            $this->{$k} = $v;
        }
    }

    /**
     * Get the function to retrieve the next token, or return null if there is no special function registered
     * in this iterator config.
     * @return callable function to retrieve the next token; for an example, see function returned by OciResponseIterator::buildNextTokenResponseGetterFromHeader
     */
    public function getNextTokenResponseGetter()
    {
        return self::invoke(
            $this->nextTokenResponseGetterClass,
            $this->nextTokenResponseGetterMethod,
            $this->nextTokenResponseGetterArgs
        );
    }

    /**
     * Get the function to retrieve the next token, or return null if there is no special function registered
     * in this iterator config.
     * @return callable function to retrieve the next token; for an example, see function returned by OciResponseIterator::buildNextTokenResponseGetterFromHeader
     */
    public function getPageRequestSetter()
    {
        return self::invoke(
            $this->pageRequestSetterClass,
            $this->pageRequestSetterMethod,
            $this->pageRequestSetterArgs
        );
    }

    /**
     * Get the function to retrieve the next token, or return null if there is no special function registered
     * in this iterator config.
     * @return callable function to retrieve the next token; for an example, see function returned by OciResponseIterator::buildNextTokenResponseGetterFromHeader
     */
    public function getResponseItemsGetter()
    {
        return self::invoke(
            $this->responseItemsGetterClass,
            $this->responseItemsGetterMethod,
            $this->responseItemsGetterArgs
        );
    }

    /**
     * Invoke the static method named $m in class $c, pass $a as argument, and return the result.
     * But only do that if $c and $m are non-null.
     * @param string $c class name
     * @param string $m method name
     * @param mixed $a argument
     * @return mixed|null result of calling the method, or null if $c or $m are null.
     */
    private static function invoke($c, $m, $a)
    {
        if ($c == null || $m == null) {
            return null;
        }
        return $c::$m($a);
    }
}

/**
 * An iterator that serves one response (i.e. one page) at a time.
 */
class OciResponseIterator implements Iterator
{
    const PAGE_KEY = "page";
    const OPC_NEXT_PAGE_HEADER_NAME = "opc-next-page";

    /**
     * The OCI client making the requests.
     */
    /*AbstractClient*/ private $client;

    /**
     * The operation being called.
     */
    /*string*/ private $operation;

    /**
     * The original parameters for the request.
     */
    /*[]*/ private $originalParams;

    /**
     * Last response.
     */
    /*OciResponse*/ private $response = null;

    /**
     * This is the number of the page, starting with 0.
     */
    /*int*/ private $absoluteIndex = 0;

    /**
     * Since there is only one response per page, this is always 0 if the iterator is valid.
     * If the current index increases to 1, then we need a new page.
     */
    /*int*/ private $currentIndex;
    
    /**
     * Function to retrieve the next token from a response.
     */
    private $nextTokenResponseGetter;

    /**
     * Function to set the next page token in a request.
     */
    private $pageRequestSetter;

    public function __construct(
        AbstractClient $client,
        /*string*/
        $operation,
        /*[]*/
        $originalParams,
        $nextTokenResponseGetter = null,
        $pageRequestSetter = null
    ) {
        $this->client = $client;
        $this->operation = $operation;
        $this->originalParams = $originalParams;

        $this->nextTokenResponseGetter = $nextTokenResponseGetter ?: self::buildNextTokenResponseGetterFromHeader(self::OPC_NEXT_PAGE_HEADER_NAME);
        $this->pageRequestSetter = $pageRequestSetter ?: self::buildPageRequestSetterToParams(self::PAGE_KEY);

        // sanity check to make sure the pageRequestSetter can modify the request params (&$params, not $params)
        $fn = $this->pageRequestSetter;
        $testParams = [];
        $fn($testParams, "nextPageToken");
        if ($testParams == []) {
            throw new InvalidArgumentException("The pageRequestSetter does not modify the request parameters; ensure that the signature matches function (&\$params, \$token).");
        }
    }

    /* Methods */

    public function current() // : mixed
    {
        if (!$this->valid()) {
            throw new ArgumentCountError("Iterator has no more results.");
        }
        return $this->response;
    }

    public function key() // : mixed
    {
        return $this->absoluteIndex;
    }

    public function next() // : void
    {
        if ($this->response == null) {
            // make the first request
            $this->makeNextRequest();
        }
        $fn = $this->nextTokenResponseGetter;
        $nextPageToken = $fn($this->response);
        if ($nextPageToken) {
            $this->makeNextRequest($nextPageToken);
        } else {
            $this->currentIndex = 1; // the only valid index is 0
        }
        ++$this->absoluteIndex;
    }

    public function rewind() // : void
    {
        $this->absoluteIndex = 0;
        $this->response = null;
    }

    public function valid() // : bool
    {
        if ($this->response == null) {
            // make the first request
            $this->makeNextRequest();
        }
        return $this->currentIndex == 0;
    }

    /* Helpers */

    /**
     * Build a function that returns the next page token from the named header.
     * @param string $headerName name of the header
     * @return callable function to extract the next page token from the named header
     */
    public static function buildNextTokenResponseGetterFromHeader($headerName)
    {
        return function ($response) use ($headerName) {
            if ($response && $response->getHeaders() && array_key_exists($headerName, $response->getHeaders())) {
                $headerCount = count($response->getHeaders()[$headerName]);
                if ($headerCount > 1) {
                    throw new InvalidArgumentException("Expected 0 or 1 values for the $headerName header, received $headerCount.");
                }
                if ($headerCount == 0) {
                    return null;
                }
                return $response->getHeaders()[$headerName][0];
            }
            return null;
        };
    }

    /**
     * Build a function that returns the next page token from body property with the given name.
     * E.g. for '{"nextStartWith":"token"}', the property name is "nextStartWith". Only top-level properties
     * are supported.
     * @param string $propertyName name of the property
     * @return callable function to extract the next page token from the named property
     */
    public static function buildNextTokenResponseGetterFromJson($propertyName)
    {
        return function ($response) use ($propertyName) {
            if ($response && $response->getJson() && property_exists($response->getJson(), $propertyName)) {
                $property = $response->getJson()->{$propertyName};
                if (is_array($property)) {
                    throw new InvalidArgumentException("Expected a single value for $propertyName, received array.");
                }
                if (is_object($property)) {
                    throw new InvalidArgumentException("Expected a single value for $propertyName, received object.");
                }
                return $property;
            }
            return null;
        };
    }

    /**
     * Build a function that sets the next page token in the params for the next request
     * @param string $paramName name of the parameter to set
     * @return callable function to set the next page token in the params for the next request
     */
    public static function buildPageRequestSetterToParams($paramName)
    {
        return function (&$params, $token) use ($paramName) {
            $params[$paramName] = $token;
        };
    }

    private function makeNextRequest($nextPageToken = null)
    {
        $nextParams = [] + $this->originalParams;
        if ($this->response != null) {
            // we have made a request before
            if ($nextPageToken == null) {
                throw new BadMethodCallException("Should not have called makeNextRequest, there is no more data to retrieve.");
            }
            $fn = $this->pageRequestSetter;
            $fn($nextParams, $nextPageToken);
        }

        $this->response = $this->client->{$this->operation}($nextParams);

        $this->currentIndex = 0;
    }
}

/**
 * Iterator to serve one row at a time.
 */
class OciItemIterator implements Iterator
{
    /*LogAdapterInterface*/ private $logger;

    /**
     * This is the iterator that serves pages of items.
     */
    /*OciResponseIterator*/ private $it;

    /**
     * Function to extract the items from a response.
     */
    private $responseItemsGetter;

    /**
     * absoluteIndex is the index of the row, even across pages. This essentially makes it look like it's just one giant page.
     *
     * Page 1: absoluteIndex0, absoluteIndex1, absoluteIndex2
     * Page 2: absoluteIndex3, absoluteIndex4
     */
    /*int*/ private $absoluteIndex = 0;

    /**
     * The currentIndex is the index in the current page. When currentIndex == count($items), then we're past the end of this page
     * and need the next page.
     *
     * Therefore. currentIndex looks like this:
     *
     * Page 1: currentIndex0, currentIndex1, currentIndex2
     * Page 2: currentIndex0, currentIndex1
     */
    /*int*/ private $currentIndex;

    /**
     * Items on this page.
     */
    /*[]*/ private $items = null;

    public function __construct(
        AbstractClient $client,
        /*string*/
        $operation,
        /*[]*/
        $originalParams,
        $nextTokenResponseGetter = null,
        $pageRequestSetter = null,
        $responseItemsGetter = null
    ) {
        $this->logger = Logger::logger(static::class);

        $this->responseItemsGetter = $responseItemsGetter ?: self::buildResponseItemsGetter('');

        $this->it = new OciResponseIterator($client, $operation, $originalParams, $nextTokenResponseGetter, $pageRequestSetter);
    }


    /* Methods */
    public function current() // : mixed
    {
        if (!$this->valid()) {
            throw new ArgumentCountError("Iterator has no more results.");
        }
        return $this->items[$this->currentIndex];
    }

    public function key() // : mixed
    {
        return $this->absoluteIndex;
    }

    public function next() // : void
    {
        if ($this->items == null) {
            // make the first request
            $this->getCurrentItems();
        }

        if (!$this->valid()) {
            // stays invalid
            return;
        }

        ++$this->currentIndex;
        if (!$this->valid()) {
            // moved past the end of the current page
            $this->it->next();
            if (!$this->it->valid()) {
                // the response iterator is also at the end
                $this->items = [];
                $this->currentIndex = 0;
            } else {
                $this->getCurrentItems();
            }
        }
        ++$this->absoluteIndex;
    }

    public function rewind() // : void
    {
        $this->absoluteIndex = 0;
        $this->items = null;
        $this->it->rewind();
    }

    public function valid() // : bool
    {
        if ($this->items == null) {
            // make the first request
            $this->getCurrentItems();
        }
        return ($this->currentIndex < (count($this->items)));
    }

    /* Helpers */

    /**
     * Build a function that returns the items in the specified response body property.
     * @param string $propertyName name of the response body property; only top level supported
     * @return callable function to extract the items
     */
    public static function buildResponseItemsGetter($propertyName)
    {
        return function ($response) use ($propertyName) {
            if (!$response || !$response->getJson()) {
                return null;
            }
            if (strlen($propertyName) == 0) {
                // whole response JSON
                $property = $response->getJson();
            } else {
                if (property_exists($response->getJson(), $propertyName)) {
                    $property = $response->getJson()->{$propertyName};
                } else {
                    $property = null;
                }
            }
            if ($property == null || !is_array($property)) {
                throw new InvalidArgumentException("Expected an array for $propertyName, received " . StringUtils::get_type_or_class($property));
            }
            return $property;
        };
    }
    
    private function getCurrentItems()
    {
        $logger = $this->logger->scope("getCurrentItems");
        while ($this->it->valid()) {
            $response = $this->it->current();
            $fn = $this->responseItemsGetter;
            $this->items = $fn($response);
            $this->currentIndex = 0;
            if (count($this->items) == 0) {
                $logger->debug("0 items on page, moving to next page");
                $this->it->next();
            } else {
                break;
            }
        };
        if (!$this->it->valid()) {
            $logger->debug("Moved past the end of the pages");
        } else {
            $logger->debug("New page has " . count($this->items) . " items");
        }
    }
}
