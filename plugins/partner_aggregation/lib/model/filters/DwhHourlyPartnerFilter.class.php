<?php

class DwhHourlyPartnerFilter extends baseObjectFilter
{
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_partner_id",
			"_lte_date_id",
			"_gte_date_id",
			"_lte_hour_id",
			"_gte_hour_id",
			"_lte_sum_time_viewed",
			"_gte_sum_time_viewed",
			"_lte_count_time_viewed",
			"_gte_count_time_viewed",
			"_lte_count_plays",
			"_gte_count_plays",
			"_lte_count_loads",
			"_gte_count_loads",
			"_lte_count_plays25",
			"_gte_count_plays25",
			"_lte_count_plays50",
			"_gte_count_plays50",
			"_lte_count_plays75",
			"_gte_count_plays75",
			"_lte_count_plays100",
			"_gte_count_plays100",
			"_lte_count_edit",
			"_gte_count_edit",
			"_lte_count_shares",
			"_gte_count_shares",
			"_lte_count_download",
			"_gte_count_download",
			"_lte_count_report_abuse",
			"_gte_count_report_abuse",
			"_lte_count_media_entries",
			"_gte_count_media_entries",
			"_lte_count_video_entries",
			"_gte_count_video_entries",
			"_lte_count_image_entries",
			"_gte_count_image_entries",
			"_lte_count_audio_entries",
			"_gte_count_audio_entries",
			"_lte_count_mix_entries",
			"_gte_count_mix_entries",
			"_lte_count_mix_non_empty",
			"_gte_count_mix_non_empty",
			"_lte_count_playlists",
			"_gte_count_playlists",
			"_lte_count_bandwidth",
			"_gte_count_bandwidth",
			"_lte_count_storage",
			"_gte_count_storage",
			"_lte_count_users",
			"_gte_count_users",
			"_lte_count_widgets",
			"_gte_count_widgets",
			"_lte_flag_active_site",
			"_gte_flag_active_site",
			"_lte_flag_active_publisher",
			"_gte_flag_active_publisher",
			"_lte_aggregated_storage",
			"_gte_aggregated_storage",
			"_lte_aggregated_bandwidth",
			"_gte_aggregated_bandwidth",
			"_lte_count_buffer_start",
			"_gte_count_buffer_start",
			"_lte_count_buffer_end",
			"_gte_count_buffer_end",
			"_lte_count_open_full_screen",
			"_gte_count_open_full_screen",
			"_lte_count_close_full_screen",
			"_gte_count_close_full_screen",
			"_lte_count_replay",
			"_gte_count_replay",
			"_lte_count_seek",
			"_gte_count_seek",
			"_lte_count_open_upload",
			"_gte_count_open_upload",
			"_lte_count_save_publish",
			"_gte_count_save_publish",
			"_lte_count_close_editor",
			"_gte_count_close_editor",
			"_lte_count_pre_bumper_played",
			"_gte_count_pre_bumper_played",
			"_lte_count_post_bumper_played",
			"_gte_count_post_bumper_played",
			"_lte_count_bumper_clicked",
			"_gte_count_bumper_clicked",
			"_lte_count_preroll_started",
			"_gte_count_preroll_started",
			"_lte_count_midroll_started",
			"_gte_count_midroll_started",
			"_lte_count_postroll_started",
			"_gte_count_postroll_started",
			"_lte_count_overlay_started",
			"_gte_count_overlay_started",
			"_lte_count_preroll_clicked",
			"_gte_count_preroll_clicked",
			"_lte_count_midroll_clicked",
			"_gte_count_midroll_clicked",
			"_lte_count_postroll_clicked",
			"_gte_count_postroll_clicked",
			"_lte_count_overlay_clicked",
			"_gte_count_overlay_clicked",
			"_lte_count_preroll25",
			"_gte_count_preroll25",
			"_lte_count_preroll50",
			"_gte_count_preroll50",
			"_lte_count_preroll75",
			"_gte_count_preroll75",
			"_lte_count_midroll25",
			"_gte_count_midroll25",
			"_lte_count_midroll50",
			"_gte_count_midroll50",
			"_lte_count_midroll75",
			"_gte_count_midroll75",
			"_lte_count_postroll25",
			"_gte_count_postroll25",
			"_lte_count_postroll50",
			"_gte_count_postroll50",
			"_lte_count_postroll75",
			"_gte_count_postroll75",
			"_lte_count_live_streaming_bandwidth",
			"_gte_count_live_streaming_bandwidth",
			"_lte_aggregated_live_streaming_bandwidth",
			"_gte_aggregated_live_streaming_bandwidth",
			) , NULL );

		$this->allowed_order_fields = array(
			'date_id', 'hour_id', 'sum_time_viewed', 'count_time_viewed', 'count_plays', 'count_loads', 'count_plays_25', 'count_plays_50', 'count_plays_75', 'count_plays_100', 'count_edit', 'count_viral', 'count_download', 'count_report', 'count_media', 'count_video', 'count_image', 'count_audio', 'count_mix', 'count_mix_non_empty', 'count_playlist', 'count_bandwidth', 'count_storage', 'count_users', 'count_widgets', 'flag_active_site', 'flag_active_publisher', 'aggr_storage', 'aggr_bandwidth', 'count_buf_start', 'count_buf_end', 'count_open_full_screen', 'count_close_full_screen', 'count_replay', 'count_seek', 'count_open_upload', 'count_save_publish', 'count_close_editor', 'count_pre_bumper_played', 'count_post_bumper_played', 'count_bumper_clicked', 'count_preroll_started', 'count_midroll_started', 'count_postroll_started', 'count_overlay_started', 'count_preroll_clicked', 'count_midroll_clicked', 'count_postroll_clicked', 'count_overlay_clicked', 'count_preroll_25', 'count_preroll_50', 'count_preroll_75', 'count_midroll_25', 'count_midroll_50', 'count_midroll_75', 'count_postroll_25', 'count_postroll_50', 'count_postroll_75', 'count_streaming', 'aggr_streaming',
		);
			
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "DwhHourlyPartnerFilter",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer ( $field_name )
	{
		$res = DwhHourlyPartnerPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
	}

	public function getIdFromPeer (  )
	{
		return null;
	}
	
	public function attachToFinalCriteria(Criteria $c)
	{
		$fromHour = $c->getNewCriterion(DwhHourlyPartnerPeer::HOUR_ID, $this->get('_gte_hour_id'), Criteria::GREATER_EQUAL);
		$theDate = $c->getNewCriterion(DwhHourlyPartnerPeer::DATE_ID, $this->get('_gte_date_id'));
		$theDate->addAnd($fromHour);
		$fromDate = $c->getNewCriterion(DwhHourlyPartnerPeer::DATE_ID, $this->get('_gte_date_id'), Criteria::GREATER_THAN);
		$fromDate->addOr($theDate);
		$c->addAnd($fromDate);
		$this->unsetByName('_gte_hour_id');
		$this->unsetByName('_gte_date_id');
		
		$toHour = $c->getNewCriterion(DwhHourlyPartnerPeer::HOUR_ID, $this->get('_lte_hour_id'), Criteria::LESS_EQUAL);
		$theDate = $c->getNewCriterion(DwhHourlyPartnerPeer::DATE_ID, $this->get('_lte_date_id'));
		$theDate->addAnd($toHour);
		$toDate = $c->getNewCriterion(DwhHourlyPartnerPeer::DATE_ID, $this->get('_lte_date_id'), Criteria::LESS_THAN);
		$toDate->addOr($theDate);
		$c->addAnd($toDate);
		$this->unsetByName('_lte_hour_id');
		$this->unsetByName('_lte_date_id');
		
		return parent::attachToFinalCriteria($c);
	}
}

