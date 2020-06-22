<?php

class kKavaVpaasReports extends kKavaReports
{

	protected static $reports_def = array(

		ReportType::CONTENT_DROPOFF_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::CONTENT_DROPOFF,
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'partner_id' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('entry_name', 'partner_id'),
				self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::genericVpaasQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'entryPeer',
					'columns' => array('NAME', 'PARTNER_ID'),
				)
			)
		),

		ReportType::TOP_SYNDICATION_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::TOP_SYNDICATION,
		),

		ReportType::USER_TOP_CONTENT_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::USER_TOP_CONTENT,
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID,
				'partner_id' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('name', 'partner_id'),
				self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::getUsersInfoVpaas',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'PARTNER_ID'),
				)
			),
		),

		ReportType::USER_USAGE_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::USER_USAGE,
			self::REPORT_DIMENSION_MAP => array(
				'kuser_id' => self::DIMENSION_KUSER_ID,
				'name' => self::DIMENSION_KUSER_ID,
				'partner_id' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('name', 'partner_id'),
				self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::getUsersInfoVpaas',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'PARTNER_ID'),
					'hash' => false,
				)
			),
		),

		ReportType::PLATFORMS_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::PLATFORMS,
		),

		ReportType::OPERATING_SYSTEM_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::OPERATING_SYSTEM,
		),

		ReportType::BROWSERS_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::BROWSERS,
		),

		ReportType::OPERATING_SYSTEM_FAMILIES_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::OPERATING_SYSTEM_FAMILIES,
		),

		ReportType::BROWSERS_FAMILIES_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::BROWSERS_FAMILIES,
		),

		ReportType::USER_ENGAGEMENT_TIMELINE_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::USER_ENGAGEMENT_TIMELINE,
		),

		ReportType::UNIQUE_USERS_PLAY_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::UNIQUE_USERS_PLAY,
		),

		ReportType::MAP_OVERLAY_COUNTRY_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::MAP_OVERLAY_COUNTRY,
		),

		ReportType::MAP_OVERLAY_REGION_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::MAP_OVERLAY_REGION,
		),

		ReportType::MAP_OVERLAY_CITY_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::MAP_OVERLAY_CITY,
		),

		ReportType::TOP_CONTENT_CREATOR_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::TOP_CONTENT_CREATOR,
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'creator_name' => self::DIMENSION_ENTRY_ID,
				'created_at' => self::DIMENSION_ENTRY_ID,
				'status' => self::DIMENSION_ENTRY_ID,
				'media_type' => self::DIMENSION_ENTRY_ID,
				'duration_msecs' => self::DIMENSION_ENTRY_ID,
				'entry_source' => self::DIMENSION_ENTRY_ID,
				'partner_id' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name', 'creator_name', 'created_at', 'status', 'media_type', 'duration_msecs', 'entry_source', 'partner_id'),
					self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::genericVpaasQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'peer' => 'entryPeer',
						'columns' => array('NAME', 'KUSER_ID', '@CREATED_AT', 'STATUS', 'MEDIA_TYPE', 'LENGTH_IN_MSECS', 'ID', 'PARTNER_ID'),
					)
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('creator_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',//we already validated in prev enrich
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)'),
						'peer' => 'kuserPeer',
					)
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_source'),
					self::REPORT_ENRICH_FUNC => 'self::getEntriesSource',
				),
			)
		),

		ReportType::TOP_CONTENT_CONTRIBUTORS_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::TOP_CONTENT_CONTRIBUTORS,
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'creator_name' => self::DIMENSION_KUSER_ID,
				'created_at' => self::DIMENSION_KUSER_ID,
				'partner_id' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'creator_name', 'created_at', 'partner_id'),
				self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::genericVpaasQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)', '@CREATED_AT', 'PARTNER_ID'),
					'peer' => 'kuserPeer',
				)
			),
		),

		ReportType::TOP_SOURCES_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::TOP_SOURCES,
		),

		ReportType::CONTENT_REPORT_REASONS_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::CONTENT_REPORT_REASONS,
		),

		ReportType::PLAYER_RELATED_INTERACTIONS_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::PLAYER_RELATED_INTERACTIONS,
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'status' => self::DIMENSION_ENTRY_ID,
				'entry_source' => self::DIMENSION_ENTRY_ID,
				'partner_id' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name', 'status', 'entry_source','partner_id'),
					self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::genericVpaasQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'peer' => 'entryPeer',
						'columns' => array('NAME', 'STATUS', 'ID', 'PARTNER_ID'),
					),
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_source'),
					self::REPORT_ENRICH_FUNC => 'self::getEntriesSource',
				),
			),
		),

		ReportType::PLAYBACK_RATE_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::PLAYBACK_RATE,
		),

		ReportType::PARTNER_USAGE_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::PARTNER_USAGE,
			self::REPORT_SKIP_PARTNER_FILTER => null,
		),

		ReportType::TOP_PLAYBACK_CONTEXT_VPAAS => array(
			self::REPORT_BASE_DEF => ReportType::TOP_PLAYBACK_CONTEXT,
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('name'),
				self::REPORT_ENRICH_FUNC => 'kKavaVpaasReports::genericVpaasQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'categoryPeer',
					'int_ids_only' => true,
					'columns' => array('NAME', 'PARTNER_ID'),
				)
			),
		),

	);

	protected static function getValidEnrichedPartners($partner_id, $partner_ids)
	{
		$partner_ids = array_values(array_unique($partner_ids));
		$c = KalturaCriteria::create(PartnerPeer::OM_CLASS);
		$c->addSelectColumn(PartnerPeer::ID);
		$c->add(PartnerPeer::ID, $partner_ids, Criteria::IN);
		$c->add(PartnerPeer::PARTNER_PARENT_ID, $partner_id);

		PartnerPeer::setUseCriteriaFilter(false);
		$stmt = PartnerPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$partners_map = $stmt->fetchAll(PDO::FETCH_COLUMN);
		PartnerPeer::setUseCriteriaFilter(true);
		return $partners_map;
	}

	protected static function getUsersInfoVpaas($ids, $partner_id, $context)
	{
		$context['skip_partner_filter'] = true;
		list($columns, $hash_conf, $result, $rows) = self::getBaseUsersInfo($ids, $partner_id, $context);

		$partner_ids = array();
		foreach ($rows as $row)
		{
			$partner_ids[] = $row['PARTNER_ID'];
		}

		$partners_map = self::getValidEnrichedPartners($partner_id, $partner_ids);
		$column_count = count($columns);
		foreach ($rows as $row)
		{
			$cur_partner_id = $row['PARTNER_ID'];
			$puser_id = $row['PUSER_ID'];
			$kuser_id = $row['ID'];

			$output = array();

			$hash = self::hashUserId($hash_conf, $partner_id, $puser_id, $kuser_id);
			if ($cur_partner_id != $partner_id && !in_array($cur_partner_id, $partners_map))
			{
				$output = array_fill(0, $column_count, '');
			}
			else if ($hash === false)
			{
				foreach ($columns as $column)
				{
					$output[] = $row[$column];
				}
			}
			else
			{
				foreach ($columns as $column)
				{
					// do not expose any column other than the hashed id
					$output[] = $column == 'PUSER_ID' ? $hash : '';
				}
			}

			$result[$kuser_id] = $output;
		}
		return $result;
	}

	protected static function genericVpaasQueryEnrich($ids, $partner_id, $context)
	{
		$context['skip_partner_filter'] = true;
		$rows = self::genericQueryEnrich($ids, $partner_id, $context);

		$partner_ids = array();
		//collect all partner ids
		foreach ($rows as $id => $row)
		{
			$cur_partner_id = end($row);
			$partner_ids[] = $cur_partner_id;
		}
		$partners_map = self::getValidEnrichedPartners($partner_id, $partner_ids);

		//validate result
		foreach ($rows as &$row)
		{
			$cur_partner_id = end($row);
			//cur partner id or parent of cur partner id == partner id
			if ($cur_partner_id == $partner_id || in_array($cur_partner_id, $partners_map))
			{
				continue; //result is valid
			}

			$row = array_fill(0, count($row), '');
		}

		return $rows;
	}

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}
		
		if (isset($report_def[self::REPORT_BASE_DEF]))
		{
			$report_def = array_merge(parent::getReportDef($report_def[self::REPORT_BASE_DEF], $input_filter), $report_def);
		}

		if (isset($report_def[self::REPORT_JOIN_GRAPHS]))
		{
			foreach ($report_def[self::REPORT_JOIN_GRAPHS] as &$cur_report)
			{
				$cur_report[self::REPORT_PARENT_PARTNER_FILTER] = true;
			}
		}
		else if (isset($report_def[self::REPORT_JOIN_REPORTS]))
		{
			foreach ($report_def[self::REPORT_JOIN_REPORTS] as &$cur_report)
			{
				$cur_report[self::REPORT_PARENT_PARTNER_FILTER] = true;
			}
		}
		else
		{
			$report_def[self::REPORT_PARENT_PARTNER_FILTER] = true;

		}

		self::initTransformTimeDimensions();

		return $report_def;
	}

}