<?php
class assetsUtils
{
	public static function createAssets ( $list , $list_name )
	{
		foreach ( $list as $entry )
		{
			$is_ready = $entry->getStatus() == entry::ENTRY_STATUS_READY;
			$data = $entry->getDataPath();

			// this should not happen !
			$duration =  $entry->getLengthInMsecs();
			if ( $duration == NULL || $duration <= 0 )
			{
				$duration = 10.0;
			}
			else
			{
				$duration = $duration/1000;
			}

			$source_link = '';
			$credit = $entry->getCredit();
			if ($credit == null)
			{
				$credit = '';
			}
			else
			{
				$source_link = $entry->getSourceLink();
				if ($source_link == null)
					$source_link = '';
}
			echo "\t" .  baseObjectUtils::objToXml ( $entry , array ( 'id' , 'name' , 'media_type' , 'kshow_id' ) ,
			'asset' , true ,
			array ( 'url' => $data , 'ready' => $is_ready , 'thumbnail_path' => $entry->getThumbnailPath() ,
			'credit' => $credit, 'source_link' => $source_link,
			'duration' => $duration  , 'list_type'=>$list_name , 'contributor_screen_name' => $entry->getKuser()->getScreenName() ));

		}
	}
}
?>