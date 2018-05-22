<?php
/**
 * @package UI-infra
 * @subpackage paginator
 */
class Infra_Paginator extends Zend_Paginator
{
	private static $index = 1;
	
	public $pageFieldName = 'page';
	public $defaultPageSize = 10;
	private $request = array();
	private $action = '';
	public $pageSizes = array(10, 25, 50, 100);
	public $submitFunction = null;
	
    /**
     * Constructor.
     *
     * @param Zend_Paginator_Adapter_Interface|Zend_Paginator_AdapterAggregate $adapter
     */
    public function __construct($adapter, $request = null, $pageFieldName = null, $defaultPageSize = null)
    {
    	parent::__construct($adapter);
    	
    	if(!is_null($pageFieldName))
    		$this->pageFieldName = $pageFieldName;
    		
    	if(!is_null($defaultPageSize))
    		$this->defaultPageSize = $defaultPageSize;
    		
    	if(!is_null($request))
    	{
    		$this->request = $request;
	    	
			$this->setCurrentPageNumber($request->getParam($this->pageFieldName));
			$this->setItemCountPerPage($request->getParam('pageSize', $this->defaultPageSize));
    	}
    }
    
    public function setAction($action)
    {
    	$this->action = $action;
    }
    
    public static function setIndex($index)
    {
    	self::$index = $index;
    }

	public function setSubmitFunction($functionName)
	{
		$this->submitFunction = $functionName;
	}
    
    public function setAvailablePageSizes(array $pageSizes)
    {
    	$this->pageSizes = $pageSizes;
    }
    
    /**
     * Renders the paginator.
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $view = $this->getView();

        $params = array(
        	'pageFieldName' => $this->pageFieldName,
        	'request' => $this->request,
        	'action' => $this->action,
        	'index' => self::$index,
        	'pageSizes' => $this->pageSizes,
        );
        self::$index ++;
        
        return $view->paginationControl($this, null, null, $params);
    }
    
    /**
     * @return int
     */
    public function getItemsCount()
    { 
    	$this->getCurrentItems(); // this will actually get the required page
    	return $this->_adapter->getTotalCount();
    }
	
	/**
	 * @return bool
	 */
	public function hasError()
	{
		return $this->_adapter->hasError();
	}
	
	/**
	 * @return string
	 */
	public function getErrorDescription()
	{
		return $this->_adapter->getErrorDescription();
	}
    
	/**
     * Sets the number of items per page.
     *
     * @param  integer $itemCountPerPage
     * @return Zend_Paginator $this
     */
    public function setItemCountPerPage($itemCountPerPage)
    {
        $this->_itemCountPerPage = (integer) $itemCountPerPage;
        if ($this->_itemCountPerPage < 1) {
            $this->_itemCountPerPage = $this->getItemCountPerPage();
        }
        
        // do not count right now, this will save the extra api request when using the adapter Kaltura_FilterPaginator
        $this->_pageCount        = null; 
        $this->_currentItems     = null;
        $this->_currentItemCount = null;

        return $this;
    }
    
	/**
     * Creates the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return stdClass
     */
    protected function _createPages($scrollingStyle = null)
    {
    	$this->getCurrentItems();
    	
    	// after loading the items, we can call the adapter to get the total count
    	$this->_pageCount = $this->_calculatePageCount();
    	 
    	/*
    	 * reproduce the original paginator behavior, for example:
    	 * if we have a total of 2 pages, but the user requested page number 3, the original paginator will return the last page (number 2).
    	 * 
    	 * this is needed bacause the previous request for getCurrentItems was made when we didn't know the total pages count. 
    	 * when page number is not normalized, we should get the items again.
    	 */
    	$normalizedPageNumber = $this->normalizePageNumber($this->_currentPageNumber);
    	if ($normalizedPageNumber != $this->_currentPageNumber)  
    	{
    		$this->_currentItems = null;
    		$this->getCurrentItems();
    		$this->_pageCount = $this->_calculatePageCount();
    	}
    	
        $page =  parent::_createPages($scrollingStyle);
	    $page->submitFunction = $this->submitFunction;
	    return $page;
    }
}