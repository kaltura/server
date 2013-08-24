<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionPlaylistsSync
{
	/**
	 * @var Google_YouTubeService
	 */
	protected $_youtubeService;

	/**
	 * @var array
	 */
	protected $_allPlaylists;

	/**
	 * @var string
	 */
	protected $_channelId;

	/**
	 * @var int
	 */
	protected $_playlistLoadIterationLimit = 10;

	/**
	 * @var int
	 */
	protected $_playlistLoadAttemptsLimit = 2;

	public function __construct(Google_YouTubeService $youtubeService)
	{
		$this->_youtubeService = $youtubeService;
		$this->_allPlaylists = array();
	}

	/**
	 * @param int $playlistLoadIterationLimit
	 */
	public function setPlaylistLoadIterationLimit($playlistLoadIterationLimit)
	{
		$this->_playlistLoadIterationLimit = $playlistLoadIterationLimit;
	}

	/**
	 * @return int
	 */
	public function getPlaylistLoadIterationLimit()
	{
		return $this->_playlistLoadIterationLimit;
	}

	public function sync($youtubeUsername, $videoId, $currentVideoPlaylists, $newVideoPlaylists)
	{
		$playlistsLoaded = false;
		for($i = 1; $i <= $this->_playlistLoadAttemptsLimit; $i++)
		{
			try
			{
				KalturaLog::debug('Trying to load all playlists, attempt '.$i);
				$this->loadPlaylistsForChannel($youtubeUsername);
				$playlistsLoaded = true;
				break;
			}
			catch(Exception $ex)
			{
				KalturaLog::err('Failed to load all playlists on attempt '.$i);
				KalturaLog::debug($ex);
			}
		}

		if (!$playlistsLoaded)
		{
			KalturaLog::err('An error occurred while loading playlists, sync playlists proccess cannot proceed');
			return $currentVideoPlaylists;
		}

		KalturaLog::debug('Current playlists: ' . $currentVideoPlaylists);
		KalturaLog::debug('New playlists: ' . $newVideoPlaylists);
		$currentPlaylistsArray = explode(',', $currentVideoPlaylists);
		$newPlaylistsArray = explode(',', $newVideoPlaylists);
		sort($currentPlaylistsArray);
		sort($newPlaylistsArray);

		foreach($currentPlaylistsArray as &$tempPlaylist)
			$tempPlaylist = trim($tempPlaylist);
		foreach($newPlaylistsArray as &$tempPlaylist)
			$tempPlaylist = trim($tempPlaylist);

		if (count($currentVideoPlaylists) == 0 && count($newVideoPlaylists) == 0)
			return null;

		if (var_export($currentPlaylistsArray, true) === var_export($newPlaylistsArray, true)) // nothing changed
			return null;

		/**
		 * used to store the playlists that were really linked to an entry,
		 * if an api error occurs, the playlist will not be added
		 */
		$currentPlaylistsReal = array();

		// playlists we need to add the video to
		foreach($newPlaylistsArray as $playlist)
		{
			if (!$playlist)
				continue;

			if (!in_array($playlist, $currentPlaylistsArray))
			{
				$playlistId = $this->getPlaylistIdByTitle($playlist);
				if (!$playlistId)
				{
					// create the playlist
					$playlistSnippet = new Google_PlaylistSnippet();
					$playlistSnippet->setChannelId($this->_channelId);
					$playlistSnippet->setTitle($playlist);
					$googlePlaylist = new Google_Playlist();
					$googlePlaylist->setSnippet($playlistSnippet);
					try
					{
						$playlistResponse = $this->_youtubeService->playlists->insert('snippet', $googlePlaylist);
					}
					catch(Exception $ex)
					{
						KalturaLog::err('Failed to create new playlist '.$playlist);
						KalturaLog::debug($ex);
						continue;
					}
					$playlistId = $playlistResponse['id'];
				}

				$playlistItem = new Google_PlaylistItem();
				$contentDetails = new Google_PlaylistItemContentDetails();
				$contentDetails->setVideoId($videoId);
				$playlistItemSnippet = new Google_PlaylistItemSnippet();
				$playlistItemSnippet->setPlaylistId($playlistId);
				$resourceId = new Google_ResourceId();
				$resourceId->setKind('youtube#video');
				$resourceId->setVideoId($videoId);
				$playlistItemSnippet->setResourceId($resourceId);
				$playlistItem->setSnippet($playlistItemSnippet);

				try
				{
					$this->_youtubeService->playlistItems->insert('snippet', $playlistItem);
				}
				catch(Exception $ex)
				{
					KalturaLog::err('Failed to insert playlist item for playlist '.$playlist);
					KalturaLog::debug($ex);
					continue;
				}

				$currentPlaylistsReal[] = $playlist;
			}
			else
			{
				$currentPlaylistsReal[] = $playlist;
			}
		}

		// playlists we need to remove the video from
		foreach($currentPlaylistsArray as $playlist)
		{
			if (!$playlist)
				continue;

			if (!in_array($playlist, $newPlaylistsArray))
			{
				$playlistId = $this->getPlaylistIdByTitle($playlist);

				if (!$playlistId)
					continue;

				$playlistItems = null;
				try
				{
					$playlistItems = $this->_youtubeService->playlistItems->listPlaylistItems('snippet', array('playlistId' => $playlistId));
				}
				catch(Exception $ex)
				{
					// failed to list playlists items, we need to keep that playlist in our records
					$currentPlaylistsReal[] = $playlist;
					KalturaLog::err('Failed to list playlist items for playlist '.$playlist);
					KalturaLog::debug($ex);
					continue;
				}

				if (!isset($playlistItems['items']) || !count($playlistItems['items']))
					continue;

				foreach($playlistItems['items'] as $playlistItem)
				{
					if (!isset($playlistItem['snippet']['resourceId']['videoId']))
						continue;

					$playlistItemVideoId = $playlistItem['snippet']['resourceId']['videoId'];
					if ($playlistItemVideoId == $videoId)
					{
						try
						{
							$this->_youtubeService->playlistItems->delete($playlistItem['id']);
						}
						catch(Exception $ex)
						{
							// playlist item could not be deleted, we need to keep that playlist in our records as well
							$currentPlaylistsReal[] = $playlist;
							KalturaLog::err('Failed to delete playlist item for playlist '.$playlist);
							KalturaLog::debug($ex);
						}
					}
				}
			}
		}

		return $currentPlaylistsReal;

	}

	protected function getPlaylistIdByTitle($playlistTitle)
	{
		foreach($this->_allPlaylists as $id => $title)
		{
			if ($playlistTitle == $title)
			{
				return $id;
			}
		}
		return null;
	}

	protected function loadPlaylistsForChannel($youtubeUsername)
	{
		$this->_allPlaylists = array();
		$channels = $this->_youtubeService->channels->listChannels('snippet', array('forUsername' => $youtubeUsername));
		if (!isset($channels['items'][0]['id']))
		{
			KalturaLog::err('Channel id could not be found for username '.$youtubeUsername);
			return;
		}
		$this->_channelId = $channels['items'][0]['id'];

		$pageToken = null;
		for($i = 0; $i < $this->_playlistLoadIterationLimit; $i++)
		{
			$params = array(
				'channelId' => $this->_channelId,
				'fields' => 'items(id,snippet(channelId,title)),pageInfo,nextPageToken',
				'maxResults' => 5,
			);

			if ($pageToken)
				$params['pageToken'] = $pageToken;

			$playlistsResult = $this->_youtubeService->playlists->listPlaylists('snippet', $params);

			if (!isset($playlistsResult['items']) || !count($playlistsResult['items']))
				break;

			foreach($playlistsResult['items'] as $item)
			{
				$id = $item['id'];
				$title = $item['snippet']['title'];
				$this->_allPlaylists[$id] = $title;
			}

			if (isset($playlistsResult['nextPageToken']))
				$pageToken = $playlistsResult['nextPageToken'];
			else
				break;
		}
	}
}