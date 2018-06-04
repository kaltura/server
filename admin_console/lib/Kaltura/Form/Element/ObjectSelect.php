<?php
/**
 * @package Admin
 * @subpackage forms
 */
class Kaltura_Form_Element_ObjectSelect extends Zend_Form_Element_Select
{
	function __construct($spec, $options = null)
	{
		parent::__construct($spec, $options);

		if (!isset($options['nameAttribute']))
		{
			throw new Zend_Form_Exception('Please specify API name attribute');
		}
		$nameAttribute = $options['nameAttribute'];

		$idAttribute = 'id';
		if (isset($options['idAttribute']))
		{
			$idAttribute = $options['idAttribute'];
		}
		
		if (!isset($options['service']))
		{
			throw new Zend_Form_Exception('Please specify API service');
		}
		$service = $options['service'];

		$action = 'listAction';
		if (isset($options['action']))
		{
			$action = $options['action'];
		}

		
		$filter = null;
		if(isset($options['filter']))
		{
			$filter = $options['filter'];
		}

		$pager = new Kaltura_Client_Type_FilterPager();
		if(isset($options['pageIndex']))
		{
			$pager->pageIndex = $options['pageIndex'];
		}
		if(isset($options['pageSize']))
		{
			$pager->pageSize = $options['pageSize'];
		}

		if(isset($options['impersonate']))
		{
			Infra_ClientHelper::impersonate($options['impersonate']);
		}
		$client = Infra_ClientHelper::getClient();
		
		$listResponse = $client->$service->$action($filter, $pager);
		Infra_ClientHelper::unimpersonate();
		
		if(isset($options['addNull']) && $options['addNull'])
		{
			$this->addMultiOption(null, "None");
		}
		
		foreach($listResponse->objects as $object)
		{
			$this->addMultiOption($object->$idAttribute, $object->$nameAttribute);
		}
	}
}
