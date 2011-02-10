<?php
/**
 * @package api
 * @subpackage ps2
 */
class reportkshowAction extends addmoderationAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "reportKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"moderation" => array ("type" => "moderation", "desc" => ""),
						),
					"optional" => array (
						)
					),
				"out" => array (
					"moderation" => array ("type" => "moderation", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	protected function ticketType()				{	return self::REQUIED_TICKET_REGULAR;	}

	protected function getStatusToUpdate () 	{		return moderation::MODERATION_STATUS_REVIEW; 	}
	
	protected function fixModeration  ( moderation &$moderation ) 	
	{
		$moderation->setObjectType( moderation::MODERATION_OBJECT_TYPE_KSHOW );
	}

	// TODO - remove when decide to support
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		die();
	}
}
?>