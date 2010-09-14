<?php

require_once ( "myEntryUtils.class.php");

class myKshowUtils
{
	public static function getWidgetCmdUrl($kdata, $cmd = "") //add, kshow, edit
	{
		$domain = requestUtils::getRequestHost();

		$baseCmd = "$domain/index.php/keditorservices/redirectWidgetCmd?kdata=$kdata&cmd=$cmd";

		return $baseCmd;
	}


	public static function createGenericWidgetHtml ( $partner_id, $subp_id, $partner_name ,  $widget_host  , $kshow_id , $user_id , $size='l' , $align='l', $version=null , $version_kshow_name=null , $version_kshow_description=null)
	{
/*		global $partner_id, $subp_id, $partner_name;
		global $WIDGET_HOST;
	*/
	    $media_type = 2;
	    $widget_type = 3;
	    $entry_id = null;

	     // add the version as an additional parameter
		$domain = $widget_host; 
		$swf_url = "/index.php/widget/$kshow_id/" .
			( $entry_id ? $entry_id : "-1" ) . "/" .
			( $media_type ? $media_type : "-1" ) . "/" .
			( $widget_type ? $widget_type : "3" ) . "/" . // widget_type=3 -> WIKIA
			( $version ? "$version" : "-1" );

		$current_widget_kshow_id_list[] = $kshow_id;

		$kshowCallUrl = "$domain/index.php/browse?kshow_id=$kshow_id";
		$widgetCallUrl = "$kshowCallUrl&browseCmd=";
		$editCallUrl = "$domain/index.php/edit?kshow_id=$kshow_id";

	/*
	  widget3:
	  url:  /widget/:kshow_id/:entry_id/:kmedia_type/:widget_type/:version
	  param: { module: browse , action: widget }
	 */
	    if ( $size == "m")
	    {
	    	// medium size
	    	$height = 198 + 105;
	    	$width = 267;
	    }
	    else
	    {
	    	// large size
	    	$height = 300 + 105 + 20;
	    	$width = 400;
	    }

		$root_url = "" ; //getRootUrl();

	    $str = "";//$extra_links ; //"";

	    $external_url = "http://" . @$_SERVER["HTTP_HOST"] ."$root_url";

		$share = "TODO" ; //$titleObj->getFullUrl ();

		// this is a shorthand version of the kdata
	    $links_arr = array (
	    		"base" => "$external_url/" ,
	    		"add" =>  "Special:KalturaContributionWizard?kshow_id=$kshow_id" ,
	    		"edit" => "Special:KalturaVideoEditor?kshow_id=$kshow_id" ,
	    		"share" => $share ,
	    	);

	    $links_str = str_replace ( array ( "|" , "/") , array ( "|01" , "|02" ) , base64_encode ( serialize ( $links_arr ) ) ) ;

		$kaltura_link = "<a href='http://www.kaltura.com' style='color:#bcff63; text-decoration:none; '>Kaltura</a>";
		$kaltura_link_str = "A $partner_name collaborative video powered by  "  . $kaltura_link;

		$flash_vars = array (  "CW" => "gotoCW" ,
	    						"Edit" => "gotoEdit" ,
	    						"Editor" => "gotoEditor" ,
								"Kaltura" => "",//gotoKalturaArticle" ,
								"Generate" => "" , //gotoGenerate" ,
								"share" => "" , //$share ,
								"WidgetSize" => $size );

		// add only if not null
		if ( $version_kshow_name ) $flash_vars["Title"] = $version_kshow_name;
		if ( $version_kshow_description ) $flash_vars["Description"] = $version_kshow_description;

		$swf_url .= "/" . $links_str;
	   	$flash_vars_str = http_build_query( $flash_vars , "" , "&" )		;

	    $widget = /*$extra_links .*/
			 '<object id="kaltura_player_' . (int)microtime(true) . '" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
				'<param name="allowScriptAccess" value="always" />'.
				'<param name="allowNetworking" value="all" />'.
				'<param name="bgcolor" value=#000000 />'.
				'<param name="movie" value="'.$domain. $swf_url . '"/>'.
				'<param name="flashVars" value="' . $flash_vars_str . '"/>'.
				'<param name="wmode" value="opaque"/>'.
				$kaltura_link .
				'</object>' ;

			"</td></tr><tr><td style='background-color:black; color:white; font-size: 11px; padding:5px 10px; '>$kaltura_link</td></tr></table>";

		if ( $align == 'r' )
		{
			$str .= '<div class="floatright"><span>' . $widget . '</span></div>';
		}
		elseif ( $align == 'l' )
		{
			$str .= '<div class="floatleft"><span>' . $widget . '</span></div>';
		}
		elseif ( $align == 'c' )
		{
			$str .= '<div class="center"><div class="floatnone"><span>' . $widget . '</span></div></div>';
		}
		else
		{
			$str .= $widget;
		}

		return $str ;
	}
	/**
	 * Will create the URL for the embedded player for this kshow_id assuming is placed on the current server with the same http protocol.
	 * @param string $kshow_id
	 * @return string URL
	 */
	public static function getEmbedPlayerUrl ( $kshow_id , $entry_id , $is_roughcut = false, $kdata = "")
	{
		// TODO - PERFORMANCE - cache the versions per kshow_id
		// - if an entry_id exists - don't fetch the version for the kshow

		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		if ( !$kshow )
		return array("", "");

		$media_type = entry::ENTRY_MEDIA_TYPE_SHOW;

		if ($entry_id)
		{
			$entry = entryPeer::retrieveByPK($entry_id);
			if ($entry)
			$media_type = $entry->getMediaType();

			// if the entry is one of the kshow roughcuts we want to share the latest roughcut
			if ($entry->getType() == entry::ENTRY_TYPE_SHOW)
			$entry_id = -1;
		}

		if ( $is_roughcut )
		{
			$show_entry_id = $kshow->getShowEntryId();
			$show_entry = entryPeer::retrieveByPK( $show_entry_id );
			if ( !$show_entry ) return null;
			$media_type = $show_entry->getMediaType();

			$show_version = $show_entry->getLastVersion();
			// set the entry_id to -1 == we want to show the roughcut, not a specific entry.
			$entry_id = $show_entry_id;
		}
		else
		{
			$show_version = -1;
		}

		$partnerId = $kshow->getPartnerId();

		$swf_url = "/index.php/widget/$kshow_id/" . ( $entry_id ? $entry_id : "-1" ) . "/" . ( $media_type ? $media_type : "-1" ) ;

		$domain = requestUtils::getRequestHost();

		$kshowName = $kshow->getName();
		$producer = kuser::getKuserById( $kshow->getProducerId() );
		$producerName = $producer->getScreenName();

		if ($entry_id >= 0)
			$headerImage = $domain.'/index.php/browse/getWidgetImage/entry_id/'.$entry_id;
		else
			$headerImage = $domain.'/index.php/browse/getWidgetImage/kshow_id/'.$kshow_id;


		if (in_array($partnerId, array(1 , 8, 18, 200))) // we're sharing a wiki widget
		{
			$footerImage = $domain.'/index.php/browse/getWidgetImage/partner_id/'.$partnerId;

			$baseCmd = self::getWidgetCmdUrl($kdata);

			$widgetCallUrl = $baseCmd."add";
			$kshowCallUrl = $baseCmd."kshow";
			$editCallUrl = $baseCmd."edit";

			$genericWidget =
			'<object type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="405" width="400" data="'.$domain. $swf_url . '/4/-1/'.$kdata.'"/>'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value="#000000" />'.
			'<param name="movie" value="'.$domain. $swf_url . '/4/-1/'.$kdata.'"/>'.
			'</object>';

			$myspaceWidget = <<<EOT
<table cellpadding="0" cellspacing="0" style="width:400px; margin:0 auto;">
	<tr style="background-color:black;">
		<th colspan="2" style="background-color:black; background: url($headerImage) 0 0 no-repeat;">
			<a href="$kshowCallUrl" style="display:block; height:30px; overflow:hidden;"></a>
		</th>
	</tr>
	<tr style="background-color:black;">
		<td colspan="2">
			<object type="application/x-shockwave-flash" allowScriptAccess="never" allowNetworking="internal" height="320" width="400" data="{$domain}{$swf_url}/1/-1/{$kdata}">
				<param name="allowScriptAccess" value="never" />
				<param name="allowNetworking" value="internal" />
				<param name="bgcolor" value="#000000" />
				<param name="movie" value="{$domain}{$swf_url}/1/-1/{$kdata}" />
			</object>
		</td>
	</tr>
	<tr style="background-color:black;">
		<td style="height:33px;"><a href="$widgetCallUrl" style="display:block; width:199px; height:33px; background:black url(http://www.kaltura.com/images/widget/wgt_btns2.gif) center 0 no-repeat; border-right:1px solid #000; overflow:hidden;"></a></td>
		<td style="height:33px;"><a href="$editCallUrl" style="display:block; width:199px; height:33px; background:black url(http://www.kaltura.com/images/widget/wgt_btns2.gif) center -33px no-repeat; border-left:1px solid #555; overflow:hidden;"></a></td>
	</tr>
	<tr>
		<td colspan="2" style="background-color:black; border-top:1px solid #222; background: url($footerImage) 0 0 no-repeat;">
			<a href="$domain" style="display:block; height:20px; overflow:hidden;"></a>
		</td>
	</tr>
</table>
EOT;
return array($genericWidget, $myspaceWidget);
		}

		$kshowCallUrl = "$domain/index.php/browse?kshow_id=$kshow_id";
		if ($entry_id >= 0)
		$kshowCallUrl .= "&entry_id=$entry_id";

		$widgetCallUrl = "$kshowCallUrl&browseCmd=";

		$editCallUrl = "$domain/index.php/edit?kshow_id=$kshow_id";
		if ($entry_id >= 0)
		$editCallUrl .= "&entry_id=$entry_id";

		if (in_array($partnerId, array(315, 387)))
		{
			$genericWidget =
			'<object type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="407" width="400" data="'.$domain. $swf_url . '/21">'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value="#000000" />'.
			'<param name="flashvars" value="hasHeadline=1&hasBottom=1&sourceLink=remixurl" />';
			'<param name="movie" value="'.$domain. $swf_url . '/21"/>'.
			'</object>';
		}
		else if (in_array($partnerId, array(250)))
		{
			$genericWidget =
			'<object type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="407" width="400" data="'.$domain. $swf_url . '/40">'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value="#000000" />'.
			'<param name="flashvars" value="hasHeadline=1&hasBottom=1&sourceLink=remixurl" />';
			'<param name="movie" value="'.$domain. $swf_url . '/40"/>'.
			'</object>';
		}
		else if (in_array($partnerId, array(321,449)))
		{
			$genericWidget =
			'<object type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="407" width="400" data="'.$domain. $swf_url . '/60">'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value="#000000" />'.
			'<param name="flashvars" value="hasHeadline=1&hasBottom=1&sourceLink=remixurl" />';
			'<param name="movie" value="'.$domain. $swf_url . '/60"/>'.
			'</object>';
		}		
		else
		{
			$genericWidget =
			'<object type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="340" width="400" data="'.$domain. $swf_url . '/2">'.
			'<param name="allowScriptAccess" value="always" />'.
			'<param name="allowNetworking" value="all" />'.
			'<param name="bgcolor" value="#000000" />'.
			'<param name="movie" value="'.$domain. $swf_url . '/2"/>'.
			'</object>';
		}

		$myspaceWidget =
		'<table cellpadding="0" cellspacing="0" style="width:400px; margin:6px auto; padding:0; background-color:black; border:1px solid black;">'.
		'<tr>'.
		'<th colspan="2" style="background-color:black; background: url('.$headerImage.') 0 0 no-repeat;"><a href="'.$kshowCallUrl.'" style="display:block; height:30px; overflow:hidden;"></a></th>'.
		'</tr>'.
		'<tr>'.
		'<td colspan="2">'.
		'<object type="application/x-shockwave-flash" allowScriptAccess="never" allowNetworking="internal" height="320" width="400" data="'.$domain. $swf_url . '/1">'.
		'<param name="allowScriptAccess" value="never" />'.
		'<param name="allowNetworking" value="internal" />'.
		'<param name="bgcolor" value="#000000" />'.
		'<param name="movie" value="'.$domain. $swf_url . '/1"/>'.
		'</object>'.
		'</td>'.
		'</tr>'.
		'<tr>'.
		'<td style="height:33px;"><a href="'.$widgetCallUrl.'contribute" style="display:block; width:199px; height:33px; background: url('.$domain.'/images/widget/wgt_btns2.gif) center 0 no-repeat; border-right:1px solid #000; overflow:hidden;"></a></td>'.
		'<td style="height:33px;"><a href="'.$editCallUrl.'" style="display:block; width:199px; height:33px; background: url('.$domain.'/images/widget/wgt_btns2.gif) center -33px no-repeat; border-left:1px solid #555; overflow:hidden;"></a></td>'.
		'</tr>'.
		'</table>';

		return array($genericWidget, $myspaceWidget);
	}

	/**
	 * Will create the URL for the kshow_id to be used as an HTML link
	 *
	 * @param string $kshow_id
	 * @return string URL link
	 */
	public static function getUrl ( $kshow_id )
	{
		return requestUtils::getWebRootUrl() . "browse?kshow_id=$kshow_id";
	}

	/**
	 * Will return an array of kshows that are 'related' to a given show
	 *
	 * @param string $kshow_id
	 * @return array of
	 */
	public static function getRelatedShows( $kshow_id, $kuser_id, $amount )
	{
		$c = new Criteria();
		$c->addJoin( kshowPeer::PRODUCER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		$c->add( kshowPeer::ID, 10000, Criteria::GREATER_EQUAL);

		//$c->add( kshowPeer::PRODUCER_ID, $kuser_id );

		// our related algorithm is based on finding shows that have similar 'heavy' tags
		if( $kshow_id )
		{
			$kshow = kshowPeer::retrieveByPK( $kshow_id );
			if( $kshow )
			{
				$tags_string = $kshow->getTags();
				if( $tags_string )
				{
					$tagsweight = array();
					foreach( ktagword::getTagsArray( $tags_string ) as $tag )
					{
						$tagsweight[$tag] = ktagword::getWeight( $tag );
					}
					arsort( $tagsweight );
					$counter = 0;
					foreach( $tagsweight as $tag => $weight )
					{
						if( $counter++ > 2 ) break;
						else
						{
							//we'll be looking for shows that have similar top tags (3 in this case)
							$c->addOr( kshowPeer::TAGS, '%'.$tag.'%', Criteria::LIKE );
						}
					}
				}

				// and of course, we don't want the show itself
				$c->addAnd( kshowPeer::ID, $kshow_id, Criteria::NOT_IN);
			}
		}
		// we want recent ones
		$c->addDescendingOrderByColumn( kshowPeer::UPDATED_AT );
		$c->setLimit( $amount );

		$shows = kshowPeer::doSelectJoinKuser( $c );

		//did we get enough?
		$amount_related = count ($shows);
		if(  $amount_related < $amount )
		{
			// let's get some more, which are not really related, but recent
			$c = new Criteria();
			$c->addJoin( kshowPeer::PRODUCER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
			$c->addDescendingOrderByColumn( kshowPeer::UPDATED_AT );
			$c->setLimit( $amount - $amount_related );
			$moreshows = kshowPeer::doSelectJoinKuser( $c );
			return array_merge( $shows, $moreshows );
		}

		return $shows;

	}

	/**
	 * Will return formatted array of kshows data for shows that are 'related' to a given show
	 *
	 * @param string $kshow_id
	 * @return array of
	 */
	public static function getRelatedShowsData ( $kshow_id, $kuser_id = null, $amount = 50 )
	{
		$kshow_list = self::getRelatedShows ( $kshow_id, $kuser_id, $amount );

		$kshowdataarray = array();

		foreach( $kshow_list as $kshow )
		{

			$data = array ( 'id' => $kshow->getId(),
			'thumbnail_path' => $kshow->getThumbnailPath(),
			'show_entry_id' => $kshow->getShowEntryId(),
			'name' => $kshow->getName(),
			'producer_name' => $kshow->getkuser()->getScreenName(),
			'views' => $kshow->getViews()
			);
			$kshowdataarray[] = $data;
		}
		return $kshowdataarray;
	}

	public static function createTeamImage ( $kshow_id )
	{
		self::createTeam1Image($kshow_id);
		self::createTeam2Image($kshow_id);
	}

	/**
	 * Creates an combined image of the producer and some of the contributors
	 *
	 * @param int $kshow_id
	 */
	const DIM_X = 26;
	const DIM_Y = 23;
	public static function createTeam1Image ( $kshow_id )
	{
		try
		{
			$contentPath = myContentStorage::getFSContentRootPath() ;

			$kshow = kshowPeer::retrieveByPK( $kshow_id );
			if ( ! $kshow ) return NULL;

			// the canvas for the output -
			$im = imagecreatetruecolor(120 , 90 );

			$logo_path = kFile::fixPath( SF_ROOT_DIR.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'kLogoBig.gif' );
			$logoIm = imagecreatefromgif( $logo_path );
			$logoIm_x = imagesx($logoIm);
			$logoIm_y = imagesy($logoIm);
			imagecopyresampled($im, $logoIm, 0, 0, 0 , 0 , $logoIm_x *0.25 ,$logoIm_y*0.25, $logoIm_x , $logoIm_y);
			imagedestroy($logoIm);

			// get producer's image
			$producer = kuser::getKuserById( $kshow->getProducerId() );
			$producer_image_path = kFile::fixPath(  $contentPath . $producer->getPicturePath () );
			if (file_exists($producer_image_path))
			{
				list($sourcewidth, $sourceheight, $type, $attr, $srcIm ) = myFileConverter::createImageByFile( $producer_image_path );

				$srcIm_x = imagesx($srcIm);
				$srcIm_y = imagesy($srcIm);
				// producer -
				imagecopyresampled($im, $srcIm, 0, 0, $srcIm_x * 0.1 , $srcIm_y * 0.1 , self::DIM_X * 2  ,self::DIM_Y * 2, $srcIm_x * 0.9 , $srcIm_y * 0.9 );
				imagedestroy($srcIm);
			}

			// fetch as many different kusers as possible who contributed to the kshow
			// first entries willcome up first
			$c = new Criteria();
			$c->add ( entryPeer::KSHOW_ID , $kshow_id );
			$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP, Criteria::EQUAL );
			//$c->add ( entryPeer::PICTURE, null, Criteria::NOT_EQUAL );
			$c->setLimit( 16 ); // we'll need 16 images of contributers
			$c->addGroupByColumn(entryPeer::KUSER_ID);
			$c->addDescendingOrderByColumn ( entryPeer::CREATED_AT );
			$entries = entryPeer::doSelectJoinkuser( $c );

			if ( $entries == NULL || count ( $entries ) == 0 )
			{
				imagedestroy($im);
				return;
			}

			//		$entry_index = 0;
			$entry_list_len = count ( $entries );
			reset ( $entries );

			if ( $entry_list_len > 0 )
			{
				/*
				 $pos = array(2,3,4, 7,8,9, 10,11,12,13,14, 15,16,17,18,19);
				 $i = 20;
				 while(--$i)
				 {
					$p1 = rand(0, 15);
					$p2 = rand(0, 15);
					$p = $pos[$p1];
					$pos[$p1] = $pos[$p2];
					$pos[$p2] = $p;
					}

					$i = count($entries);
					while($i--)
					{
					$x = current($pos) % 5;
					$y = floor(current($pos) / 5);
					next($pos);
					self::addKuserPictureFromEntry ( $contentPath , $im ,$entries , $x , $y );
					}
					*/

				for ( $y = 0 ; $y <= 1 ; ++$y )
				for ( $x = 2 ; $x <= 4 ; ++ $x  )
				{
					self::addKuserPictureFromEntry ( $contentPath , $im ,$entries , $x , $y );
				}

				for ( $y = 2 ; $y <= 3 ; ++$y )
				for ( $x = 0 ; $x <= 4 ; ++ $x  )
				{
					self::addKuserPictureFromEntry ( $contentPath , $im ,$entries , $x , $y );
				}
			}
			else
			{
				// no contributers - need to create some other image
			}


			// add the clapper image on top


			$clapper_path = kFile::fixPath( SF_ROOT_DIR.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'mykaltura'.DIRECTORY_SEPARATOR.'productionicon.png' );
			$clapperIm = imagecreatefrompng( $clapper_path );
			imagecopyresampled($im, $clapperIm, ( 1.2 * self::DIM_X ) , (1.2 * self::DIM_Y), 0, 0, self::DIM_X ,self::DIM_Y , imagesx($clapperIm) , imagesy($clapperIm) );
			imagedestroy($clapperIm);

			$path = kFile::fixPath( $contentPath.$kshow->getTeamPicturePath() );

			myContentStorage::fullMkdir($path);

			imagepng($im, $path);
			imagedestroy($im);

			$kshow->setHasTeamImage ( true );
			$kshow->save();
		}
		catch ( Exception $ex )
		{
			// nothing much we can do here !

		}
	}

	public static function createTeam2Image ( $kshow_id )
	{
		try
		{
			$kshow = kshowPeer::retrieveByPK( $kshow_id );
			if ( ! $kshow ) return NULL;

			$contentPath = myContentStorage::getFSContentRootPath() ;

			// TODO - maybe start from some kaltura background - so if image is not full - still interesting
			$im = imagecreatetruecolor(24 * 7 - 1, 24  * 2 - 1);

			$logo_path = kFile::fixPath( SF_ROOT_DIR.'/web/images/browse/contributorsBG.gif');
			$im = imagecreatefromgif( $logo_path );

			// fetch as many different kusers as possible who contributed to the kshow
			// first entries will come up first
			$c = new Criteria();
			$c->add ( entryPeer::KSHOW_ID , $kshow_id );
			$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP, Criteria::EQUAL );
			//$c->add ( entryPeer::PICTURE, null, Criteria::NOT_EQUAL );
			$c->setLimit( 14 ); // we'll need 14 images of contributers
			$c->addGroupByColumn(entryPeer::KUSER_ID);
			$c->addDescendingOrderByColumn ( entryPeer::CREATED_AT );
			$entries = baseentryPeer::doSelectJoinkuser( $c );

			if ( $entries == NULL || count ( $entries ) == 0 )
			{
				imagedestroy($im);
				return;
			}

			$entry_list_len = count ( $entries );
			reset ( $entries );

			if ( $entry_list_len > 0 )
			{
				for ( $y = 0 ; $y <= 1 ; ++$y )
				for ( $x = 0 ; $x <= 6 ; ++ $x  )
				{
					self::addKuserPictureFromEntry ( $contentPath , $im ,$entries , $x , $y, 1, 24, 24 );
				}
			}
			else
			{
				// no contributers - need to create some other image
			}


			$path = kFile::fixPath( $contentPath.$kshow->getTeam2PicturePath() );

			myContentStorage::fullMkdir($path);

			imagepng($im, $path);
			imagedestroy($im);

			$kshow->setHasTeamImage ( true );
			$kshow->save();
		}
		catch ( Exception $ex )
		{
			// nothing much we can do here !

		}
	}

	private static function addKuserPictureFromEntry ( $contentPath , $im , &$entries , $x , $y , $border = 1, $width = self::DIM_X, $height = self::DIM_Y)
	{
		$entry = current ($entries );

		if ( $entry == NULL )
		{
			// for now - if there are not enough images - stop !
			return ;

			// if we reach here - we want to rotate the images we already used
			reset ( $entries );
			$entry = current ($entries );
		}
		$kuser =  $entry->getKuser();
		$kuser_image_path = kFile::fixPath(  $contentPath . $kuser->getPicturePath () );

		if (file_exists($kuser_image_path))
		{
			list($sourcewidth, $sourceheight, $type, $attr, $kuserIm ) = myFileConverter::createImageByFile( $kuser_image_path );

			if ($kuserIm)
			{
				$kuserIm_x = imagesx($kuserIm);
				$kuserIm_y = imagesy($kuserIm);
				// focus on the ceter of the image - ignore 10% from each side to make the center bigger
				imagecopyresampled($im, $kuserIm, $width * $x , $height * $y, $kuserIm_x * 0.1 , $kuserIm_y * 0.1 , $width - $border  ,$height - $border, $kuserIm_x * 0.9  , $kuserIm_y * 0.9 );
				imagedestroy($kuserIm);
			}
		}
		next ( $entries );
	}

	public static function isSubscribed($kshow_id, $kuser_id, $subscription_type = null)
	{
		$c = new Criteria ();
		$c->add ( KshowKuserPeer::KSHOW_ID , $kshow_id);
		$c->add ( KshowKuserPeer::KUSER_ID , $kuser_id);

		if ($subscription_type !== null)
		$c->add ( KshowKuserPeer::SUBSCRIPTION_TYPE, $subscription_type );

		return KshowKuserPeer::doSelectOne( $c );
	}

	public static function subscribe($kshow_id, $kuser_id, &$message)
	{
		// first check if user already subscribed to this show
		$kshowKuser = self::isSubscribed($kshow_id, $kuser_id);
		if ( $kshowKuser != NULL )
		{
			$message = "You are already subscribed to this Kaltura";
			return false;
		}

		$kshow = kshowPeer::retrieveByPK($kshow_id);
		if (!$kshow)
		{
			$message = "Kaltura $kshow_id doesn't exist";
			return false;
		}

		$kuser = kuserPeer::retrieveByPK($kuser_id);
		if (!$kuser)
		{
			$message = "User $user_id doesn't exist";
			return false;
		}

		$showname = $kshow->getName();
		$subscriberscreenname = $kuser->getScreenName();

		// subscribe
		$kshowKuser = new KshowKuser();
		$kshowKuser->setKshowId($kshow_id);
		$kshowKuser->setKuserId($kuser_id);
		$kshowKuser->setSubscriptionType(KshowKuser::KSHOW_SUBSCRIPTION_NORMAL);
		$kshowKuser->setAlertType(alert:: KALTURAS_PRODUCED_ALERT_TYPE_SUBSCRIBER_ADDED);
		$kshowKuser->save();

		$message = "You are now subscribed to $showname. You can receive updates and join the discussion.";
		return true;
	}

	public static function unsubscribe( $kshow_id, $kuser_id, &$message )
	{
		// first check if user already subscribed to this show
		$kshowKuser = self::isSubscribed($kshow_id, $kuser_id, KshowKuser::KSHOW_SUBSCRIPTION_NORMAL);

		if ( !$kshowKuser )
		{
			$kshow = kshowPeer::retrieveByPK($kshow_id);
			if (!$kshow)
			{
				$message = "Kaltura $kshow_id doesn't exist.";
			}
			else
			{
				$kuser = kuserPeer::retrieveByPK($kuser_id);
				if (!$kuser)
				{
					$message = "User $user_id doesn't exist.";
				}
				else
				$message = "Error - You are not subscribed to this Kaltura.";
			}

			return false;
		}

		// ok, we found he entry, so delete it.
		$kshowKuser->delete();
		$message = "You have unsubscribed from this Kaltura.";
		return true;
	}

	public static function canEditKshow ( $kshow_id , $existing_kshow , $likuser_id )
	{
		if ( $existing_kshow == NULL )
		{
			// TODO - some good error -
			// TODO - let's make a list of all errors we encounter and see how we use the I18N and built-in configuration mechanism to maintain the list
			// and later on translate the errors.
			ERROR::fatal ( 12345 ,
			"Kshow with id [" .  $kshow_id . "] does not exist in the system. This is either an innocent mistake or you are a wicked bastard" );
			// TODO - think of our policy - what do we do if we notice what looks like an attemp to harm the system ?
			// because the system is not stable, mistakes like this one might very possibly be innocent, but later on - what should happen in XSS / SQL injection /
			// attemp to insert malformed data ?

			return false;
		}

		// make sure the logged-in user is allowed to access this kshow in 2 aspects:
		// 1. - it is produced by him or a template
		if ( $existing_kshow->getProducerId() != $likuser_id )
		{
			ERROR::fatal ( 10101 ,
			"User (with id [" . $likuser_id . "] is attempting to modify a kshow with id [$kshow_id] that does not belong to him (producer_id [" .
			$existing_kshow->getProducerId() . "] !!" );

			return false;
		}

		return true;
	}

	public static function fromatPermissionText ( $kshow_id , $kshow = null )
	{
		if ( $kshow == NULL )
		{
			$kshow = kshowPeer::retrieveByPK ( $kshow_id );
		}

		if ( !$kshow )
		{
			// ERROR !
			return "";
		}

		$pwd_permissions = $kshow->getViewPermissions() == kshow::KSHOW_PERMISSION_INVITE_ONLY ||
		$kshow->getEditPermissions() == kshow::KSHOW_PERMISSION_INVITE_ONLY ||
		$kshow->getContribPermissions() == kshow::KSHOW_PERMISSION_INVITE_ONLY;

		// no password protection
		if ( ! $pwd_permissions ) return "";


		$str =
		( $kshow->getViewPermissions() == kshow::KSHOW_PERMISSION_INVITE_ONLY ? "View password " . $kshow->getViewPassword() . " " : "") .
		( $kshow->getContribPermissions() == kshow::KSHOW_PERMISSION_INVITE_ONLY ? "Contribute password " . $kshow->getContribPassword() . " " : "") .
		( $kshow->getEditPermissions() == kshow::KSHOW_PERMISSION_INVITE_ONLY ? "Edit password " . $kshow->getEditPassword() . " " : "") ;

		return $str;
	}

	public static function getViewerType($kshow, $kuserId)
	{
		$viewerType = KshowKuser::KSHOWKUSER_VIEWER_USER; // viewer
		if ($kuserId)
		{
			// for admin - act as the producer
			if ( kuser::isAdmin( $kuserId ))
			$viewerType = KshowKuser::KSHOWKUSER_VIEWER_PRODUCER; // producer
			elseif ($kshow->getProducerId() == $kuserId)
			$viewerType = KshowKuser::KSHOWKUSER_VIEWER_PRODUCER; // producer
			else
			{
				if (myKshowUtils::isSubscribed($kshow->getId(), $kuserId))
				$viewerType = KshowKuser::KSHOWKUSER_VIEWER_SUBSCRIBER; // subscriber;
			}
		}

		return $viewerType;
	}

	public static function deepCloneById ( $source_kshow_id , $new_entries = NULL )
	{
		$kshow = kshowPeer::retrieveByPK( $source_kshow_id );
		if ( $kshow ) return self::deepClone( $kshow , $new_entries );
		else NULL;
	}

	public static function deepClone ( kshow $source_kshow , &$new_entries )
	{
		$target_kshow = $source_kshow->copy();
		// will have to save to retrieve the id from the DB.
		$target_kshow->save();

		$target_id = $target_kshow->getId();

		echo "Creating new kshow $target_id\n";

		$special_entries = array ();
		$special_entries [] = $source_kshow->getShowEntryId();
		$special_entries [] = $source_kshow->getIntroId();

		$skin = $source_kshow->getSkinObj();
		///bg_entry_id=2171&bg_entry_path=/content/entry/data/0/2/2171_100000.jpg
		$bg_entry_id = $skin->get ( "bg_entry_id" );
		$special_entries [] = $bg_entry_id;

		echo "special entry_ids: " . print_r ( $special_entries , true );

		// clone the show_entry and intro
		$entries = entryPeer::retrieveByPKs ( $special_entries );

		echo "special entries count " . count ($entries ) ."\n";

		// it's hard to assume the order - if a PK was not found, there is no placeholder
		foreach ( $entries as $entry )
		{
			$new_entry = myEntryUtils::deepClone( $entry , $target_id , NULL );
			$new_entries[] = $new_entry;

			if ( $entry->getId() == $source_kshow->getShowEntryId() )
			{
				echo "ShowEntry:\n";
				$target_kshow->setShowEntryId ( $new_entry->getId() );
			}
			elseif ( $entry->getId() == $source_kshow->getIntroId() )
			{
				echo "Intro:\n";
				$target_kshow->setIntroId ( $new_entry->getId() );
			}
			elseif ( $entry->getId() == $bg_entry_id )
			{
				echo "Background:\n";
				$skin->set ( "bg_entry_id" , $new_entry->getId() );
				$skin->set ( "bg_entry_path" , $new_entry->getDataPath() ); // replaced__getDataPath
			}
			else
			{
				// ERROR !
			}
		}

		$source_thumbnail_path = $source_kshow->getThumbnailPath();
		$target_kshow->setThumbnail ( $source_kshow->getThumbnail() );
		$target_thumbnail_path = $target_kshow->getThumbnailPath();

		// TODO - don't copy files if poiting to templates !
		$content = myContentStorage::getFSContentRootPath();

//		echo ( "Background - copying file: " . $content . $source_thumbnail_path . " -> " .  $content . $target_thumbnail_path ."\n");
//		myContentStorage::moveFile( $content . $source_thumbnail_path , $content . $target_thumbnail_path , false , true );

		self::resetKshowStats( $target_kshow );

		$target_kshow->save();

		$c = new Criteria();
		$c->add ( entryPeer::KSHOW_ID , $source_kshow->getId() );
		// don't clope the entries that were alredt cloned
		$c->add ( entryPeer::ID , $special_entries , Criteria::NOT_IN );
		$entries = entryPeer::doSelect ( $c );

		foreach ( $entries as $entry )
		{
			$new_entry = myEntryUtils::deepClone( $entry , $target_id , NULL );
			$new_entries[] = $new_entry;
		}

		echo "Ended creating new kshow $target_id. " . count ( $new_entries ) . " entries copied\n";

		return $target_kshow;
	}

	private static function resetKshowStats ( $target_kshow , $reset_entry_stats = false )
	{
		// set all statistics to 0
		$target_kshow->setComments ( 0 );
		$target_kshow->setRank ( 0 );
		$target_kshow->setViews ( 0 );
		$target_kshow->setVotes ( 0 );
		$target_kshow->setFavorites ( 0 );
		if ( $reset_entry_stats )
		{
			$target_kshow->setEntries ( 0 );
			$target_kshow->setContributors ( 0 );
		}
		$target_kshow->setSubscribers ( 0 );
		$target_kshow->setNumberOfUpdates ( 0 );

		$target_kshow->setCreatedAt( time() );
		$target_kshow->setUpdatedAt( time() );

	}

	public static function shalowCloneById ( $source_kshow_id , $new_prodcuer_id )
	{
		$kshow = kshowPeer::retrieveByPK( $source_kshow_id );
		if ( $kshow ) return self::shalowClone( $kshow , $new_prodcuer_id );
		else NULL;
	}

	public static function shalowClone ( kshow $source_kshow , $new_prodcuer_id )
	{
		$target_kshow = $source_kshow->copy();

		$target_kshow->setProducerId( $new_prodcuer_id ) ;

		$target_kshow->save();

		self::resetKshowStats( $target_kshow , true );
		if (!$source_kshow->getEpisodeId())
			$target_kshow->setEpisodeId( $source_kshow->getId());
		//$target_kshow->setHasRoughcut($source_kshow->getHasRoughcut());

		$target_show_entry = $target_kshow->createEntry ( entry::ENTRY_MEDIA_TYPE_SHOW , $new_prodcuer_id );

		$content = myContentStorage::getFSContentRootPath();
		$source_thumbnail_path = $source_kshow->getThumbnailPath();
		$target_kshow->setThumbnail ( null );
		$target_kshow->setThumbnail ( $source_kshow->getThumbnail() );
		$target_thumbnail_path = $target_kshow->getThumbnailPath();

//		myContentStorage::moveFile( $content . $source_thumbnail_path , $content . $target_thumbnail_path , false , true );

		$target_kshow->save();

		// copy the show_entry file content
		$source_show_entry = entryPeer::retrieveByPK( $source_kshow->getShowEntryId() );

		$source_show_entry_data_key = $source_show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		$target_show_entry->setData ( null );
		$target_show_entry->setData ( $source_show_entry->getData() );
		$target_show_entry_data_key = $target_show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		
		$target_show_entry->setName ( $source_show_entry->getName() );
		$target_show_entry->setLengthInMsecs( $source_show_entry->getLengthInMsecs() );
		
		kFileSyncUtils::softCopy($source_show_entry_data_key, $target_show_entry_data_key);
		//myContentStorage::moveFile( $content . $source_show_entry_path , $content . $target_show_entry_path , false , true );

		myEntryUtils::createThumbnail($target_show_entry, $source_show_entry, true);
		
//		$target_kshow->setHasRoughcut(true);
//		$target_kshow->save();
		
		$target_show_entry->save();

		return $target_kshow;
	}


	// use the entry's thumbnail as this kshow's thumbnail
	public static function updateThumbnail ( $kshow , entry $entry , $should_force = false )
	{
		// We don't want to copy thumbnails of entries that are not ready - they are bad and will later be replaced anyway
		if ( $entry->getThumbnail() != null && $entry->isReady() )
		{
			$show_entry = $kshow->getShowEntry();
			return myEntryUtils::createThumbnail ( $show_entry , $entry , $should_force );
		}
		return false;
	}


	public static function getKshowAndEntry ( &$kshow_id , &$entry_id )
	{
		$error = null;
		$kshow = null;
		$entry = null;
		$error_obj = null;
		if ( $entry_id == NULL || $entry_id == "-1" )
		{
			if ($kshow_id)
			{
				$kshow = kshowPeer::retrieveByPK( $kshow_id );
				if ( ! $kshow )
				{
					$error =  APIErrors::INVALID_KSHOW_ID; // "kshow [$kshow_id] does not exist";
					$error_obj = array ( $error , $kshow_id  );
				}
				else
				{
					$entry_id = $kshow->getShowEntryId();
					$entry = $kshow->getShowEntry();
				}
			}
		}
		else
		{
			$entry = entryPeer::retrieveByPK($entry_id);
			if ( $entry )
			{
				$kshow = @$entry->getKshow();
				$kshow_id = $entry->getKshowId();
			}
		}

		if ( $entry == NULL )
		{
			$error =  APIErrors::INVALID_ENTRY_ID; //"No such entry [$entry_id]" ;
			$error_obj = array ( $error , "entry" , $entry_id  );
		}

		return array ( $kshow , $entry , $error , $error_obj );
	}

	/*
	 * @param unknown_type $generic_id
	 * A generic_id is a strgin starting with w- or k- or e-
	 * then comes the real id -
	 * 	w- a widget id which is a 32 character md5 string
	 *  k- a kshow id which is an integer
	 *  e- an entry id which is an integer
	 */
// TODO - cache the ids !!!
	public static function getWidgetKshowEntryFromGenericId( $generic_id )
	{
		if ( $generic_id == null )
			return null;
		$prefix = substr ( $generic_id , 0 , 2 );
		if ( $prefix == "w-" )
		{
			$id = substr ( $generic_id , 2 ); // the rest of the string
			$widget = widgetPeer::retrieveByPK( $id , null , widgetPeer::WIDGET_PEER_JOIN_ENTRY +  widgetPeer::WIDGET_PEER_JOIN_KSHOW ) ;
			if ( ! $widget )
				return null;
			$kshow = $widget->getKshow();
			$entry = $widget->getEntry();

			return array ( $widget , $kshow , $entry );
		}
		elseif ( $prefix == "k-" )
		{
			$id = substr ( $generic_id , 2 ); // the rest of the string
			list ( $kshow , $entry , $error ) = self::getKshowAndEntry ( $id , -1 );
			if ( $error )	return null;
			return array ( null , $kshow , $entry );
		}
		elseif ( $prefix == "e-" )
		{
			$id = substr ( $generic_id , 2 ); // the rest of the string
			list ( $kshow , $entry , $error ) = self::getKshowAndEntry ( -1 , $id );
			if ( $error )	return null;
			return array ( null , $kshow , $entry );
		}
		else
		{
			// not a good prefix - why guess ???
			return null;
		}
	}

	// TODO -  find a better place to put this method, this is not a good one !
	public static function sendAlertsForNewRoughcut ( $kshow , $likuser_id , $user_screenname )
	{
		$kshow_id = $kshow->getId();
		// Send an email alert to producer
			if( $kshow->getProducerId() != $likuser_id ) // don't send producer alerts when the producer is the editor
				alertPeer::sendEmailIfNeeded(  $kshow->getProducerId(),
									alert::KALTURAS_PRODUCED_ALERT_TYPE_ROUGHCUT_CREATED,
									array ( 'screenname' => $user_screenname,
											'kshow_name' => $kshow->getName(),
											'kshow_id' => $kshow->getId() ) );

			// TODO:  efficiency: see if there is a wa to search for contributors based on some other method than full entry table scan
			// Send email alerts to contributors
			$c = new Criteria();
			$c->add(entryPeer::KSHOW_ID, $kshow_id);
			$c->add(entryPeer::KUSER_ID, $likuser_id, Criteria::NOT_EQUAL ); // the current user knows they just edited
			$c->addAnd(entryPeer::KUSER_ID, $kshow->getProducerId(), Criteria::NOT_EQUAL ); // the producer knows they just edited
			$c->add(entryPeer::TYPE, entry::ENTRY_TYPE_MEDIACLIP);
			$c->addGroupByColumn(entryPeer::KUSER_ID);
			$entries = entryPeer::doSelect( $c );
			$already_received_alert_array = array();
			foreach ( $entries as $entry )
			{
				alertPeer::sendEmailIfNeeded(  $entry->getKuserId(),
									alert::KALTURAS_PARTOF_ALERT_TYPE_ROUGHCUT_CREATED,
									array ( 'screenname' => $user_screenname,
											'kshow_name' => $kshow->getName(),
											'kshow_id' => $kshow->getId() ) );
				$already_received_alert_array[ $entry->getKuserId() ] = true;

			}


			// send email alert to subscribers
			$c = new Criteria();
			$c->add(KshowKuserPeer::KSHOW_ID, $kshow_id); //only subsribers of this show
			$c->add(KshowKuserPeer::KUSER_ID, $likuser_id, Criteria::NOT_EQUAL ); // the current user knows they just edited
			$c->add(KshowKuserPeer::SUBSCRIPTION_TYPE, KshowKuser::KSHOW_SUBSCRIPTION_NORMAL); // this table stores other relations too
			$subscriptions = KshowKuserPeer::doSelect( $c );
			foreach ( $subscriptions as $subscription )
			{
					if( !isset($already_received_alert_array[ $subscription->getKuserId() ]) ) // don't send emails to subscribed users who are also contributors
						alertPeer::sendEmailIfNeeded(  $subscription->getKuserId(),
									alert::KALTURAS_SUBSCRIBEDTO_ALERT_TYPE_ROUGHCUT_CREATED,
									array ( 'screenname' => $user_screenname,
											'kshow_name' => $kshow->getName(),
											'kshow_id' => $kshow->getId()  ) );
			}

	}
	
	/**
	 * Will search for a kshow for the specific partner & key.
	 * The key can be combined from the kuser_id and the group_id
	 * If not found - will create one
	 * If both the kuser_id & group_id are null - always create one
	 */
	public static function getDefaultKshow ( $partner_id , $subp_id, $puser_kuser , $group_id = null , $allow_quick_edit = null , $create_anyway = false , $default_name = null )
	{
		$kuser_id = null;
		// make sure puser_kuser object exists so function will not exit with FATAL
		if($puser_kuser)
		{
			$kuser_id = $puser_kuser->getKuserId();
		}
		$key = $group_id != null ? $group_id : $kuser_id;
		if ( !$create_anyway )
		{
			$c = new Criteria();
			myCriteria::addComment( $c , "myKshowUtils::getDefaultKshow");
			$c->add ( kshowPeer::GROUP_ID , $key );
			$kshow = kshowPeer::doSelectOne( $c );
			if ( $kshow ) return $kshow;
					// no kshow - create using the service
			$name = "{$key}'s generated show'";
		}
		else
		{
			$name = "a generated show'";
		}

		if	( $default_name ) 
			$name = $default_name;
		
		$extra_params = array ( "kshow_groupId" => $key , "kshow_allowQuickEdit" => $allow_quick_edit ); // set the groupId with the key so we'll find it next time round
		$kshow = myPartnerServicesClient::createKshow ( "" , $puser_kuser->getPuserId() , $name , $partner_id , $subp_id , $extra_params );
		
		return $kshow;
	}
	
	public static function getKshowFromPartnerPolicy ( $partner_id, $subp_id , $puser_kuser , $kshow_id , $entry )
	{
	    if ( $kshow_id == kshow::KSHOW_ID_USE_DEFAULT )
        {
            // see if the partner has some default kshow to add to
            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser  );
            if ( $kshow ) $kshow_id = $kshow->getId();
        }
		elseif ( $kshow_id == kshow::KSHOW_ID_CREATE_NEW )
        {
            // if the partner allows - create a new kshow 
            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser , null , true );
            if ( $kshow ) $kshow_id = $kshow->getId();
        }   
		else
        {
            $kshow = kshowPeer::retrieveByPK( $kshow_id );
        }

        if ( ! $kshow )
        {
            // the partner is attempting to add an entry to some invalid or non-existing kwho
            $this->addError( APIErrors::INVALID_KSHOW_ID, $kshow_id );
            return;
        }	
        return $kshow;	
	}	
}
?>