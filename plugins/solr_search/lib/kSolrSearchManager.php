<?php
require_once("Solr/Service.php");

class kSolrSearchManager implements kObjectChangedEventConsumer, kObjectCreatedEventConsumer, kObjectDeletedEventConsumer
{
	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object) 
	{
		if(!($object instanceof entry))
			return;

		$this->createEntryDocument($object);
	}

	/**
	 * @param BaseObject $object
	 */
	public function objectDeleted(BaseObject $object) 
	{
		if(!($object instanceof entry))
			return;

		$solr = self::createSolrService();
		$solr->deleteById($object->getIntId());
	}

	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		if(!($object instanceof entry))
			return;
			
		$this->createEntryDocument($object);
	}
	
	public function createEntryDocument(entry $entry)
	{
		$document = new Apache_Solr_Document();
		$document->plugins_data = '';
		
		foreach(self::$solrFields as $solrField)
		{
			$value = $entry->getByName($solrField->phpName);
			$solrName = $solrField->solrName;
			
			switch($solrField->type) {
			case "array":
				if ($value != '')
				{
				        $values = explode(",", $value);
				        foreach($vals as $value)
				        {
				                $document->addField($solrName, $value);
				        }
				}
				break;
				
			case "date":
				$value = strftime("%Y-%m-%dT%H:%M:%SZ", $value);
				$document->addField($solrName, $value);
				break;
				
			default:
				$document->addField($solrName, $value);
			}
		}

		$solr = createSolrService();
		$solr->addDocument($document);
	}
	
	public static function createSolrService()
	{
		return new Apache_Solr_Service('localhost', 8983, '/solr');
	}
	
	public static $solrFields = array(
		array("solrName" => "id", "phpName" => "Id", "type" => "int"),
		array("solrName" => "name", "phpName" => "Name", "type" => "string"),
		array("solrName" => "tags", "phpName" => "Tags", "type" => "string"),
		
		array("solrName" => "categories", "phpName" => "CategoriesIds", "type" => "array"),
		array("solrName" => "flavor_params", "phpName" => "FlavorParamsIds", "type" => "array"),
		
		array("solrName" => "source_link", "phpName" => "SourceLink", "type" => "string"),
		array("solrName" => "kshow_id", "phpName" => "KshowId", "type" => "string"),
		array("solrName" => "group_id", "phpName" => "GroupId", "type" => "string"),
		array("solrName" => "description", "phpName" => "Description", "type" => "string"),
		array("solrName" => "admin_tags", "phpName" => "AdminTags", "type" => "string"),
		array("solrName" => "kuser_id", "phpName" => "KuserId", "type" => "int"),
		array("solrName" => "entry_status", "phpName" => "Status", "type" => "int"),
		array("solrName" => "type", "phpName" => "Type", "type" => "int"),
		array("solrName" => "media_type", "phpName" => "MediaType", "type" => "int"),
		array("solrName" => "views", "phpName" => "Views", "type" => "int"),
		array("solrName" => "partner_id", "phpName" => "PartnerId", "type" => "int"),
		array("solrName" => "moderation_status", "phpName" => "ModerationStatus", "type" => "int"),
		array("solrName" => "display_in_search", "phpName" => "DisplayInSearch", "type" => "int"),
		array("solrName" => "duration", "phpName" => "LengthInMsecs", "type" => "int"),
		array("solrName" => "access_control_id", "phpName" => "AccessControlId", "type" => "int"),
		array("solrName" => "moderation_count", "phpName" => "ModerationCount", "type" => "int"),
		array("solrName" => "rank", "phpName" => "Rank", "type" => "int"),
		array("solrName" => "plays", "phpName" => "Plays", "type" => "int"),
		
		array("solrName" => "created_at", "phpName" => "CreatedAt", "type" => "date"),
		array("solrName" => "updated_at", "phpName" => "UpdatedAt", "type" => "date"),
		array("solrName" => "modified_at", "phpName" => "ModifiedAt", "type" => "date"),
		array("solrName" => "media_date", "phpName" => "MediaDate", "type" => "date"),
		array("solrName" => "start_date", "phpName" => "StartDate", "type" => "date"),
		array("solrName" => "end_date", "phpName" => "EndDate", "type" => "date"),
		array("solrName" => "available_from", "phpName" => "AvailableFrom", "type" => "date"));
}
