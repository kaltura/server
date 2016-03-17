<?php 
/**
 * @package plugins.logView
 * @subpackage admin
 */
class Form_ObjectInvestigateLogForm extends Infra_Form
{
	private function query()
	{
		$post = array(
			"aggs" => array(
				"objectType" => array(
					"terms" => array(
						"field" => "objectType",
						"size" => 100
					)
				)
			),
			"size" => 0
		);
	
		$post = json_encode($post);
	
		$settings = Zend_Registry::get('config')->settings;
		$logViewUrl = 'http://localhost:9200';
		if(isset($settings->logViewUrl))
		{
			$logViewUrl = $settings->logViewUrl;
		}
	
		$url = "$logViewUrl/*/_search";
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$results = curl_exec($ch);
		curl_close($ch);
	
		return json_decode($results);
	}
	
	public function init()
	{
		$objectTypesResponse = $this->query();
		$objectTypes = array();
		
		foreach($objectTypesResponse->aggregations->objectType->buckets as $bucket){
			$objectTypes[$bucket->key] = $bucket->key;
		}
		asort($objectTypes);
		
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'inline-form');

		$this->addElement('select', 'objectType', array(
				'label'			=> 'Object Type:',
				'filters'		=> array('StringTrim'),
				'multiOptions' 	=> $objectTypes
		));

		$this->addElement('text', 'objectId', array(
				'label'			=> 'Object ID:',
				'filters'		=> array('StringTrim'),
		));

		$this->addElement('button', 'submit', array(
				'label'		=> 'Search',
				'decorators'	=> array('ViewHelper'),
		));
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			array('Description', array('placement' => 'prepend')),
			'Fieldset',
			'Form',
		));
	}
}