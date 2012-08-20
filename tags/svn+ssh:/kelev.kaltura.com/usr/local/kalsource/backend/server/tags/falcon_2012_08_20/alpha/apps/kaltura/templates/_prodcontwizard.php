<?php
	$host = requestUtils::getHost() ;
	$recorderUrl = "";
	if ( ! kString::beginsWith( $host , "http://www.kaltura.com" ) )
	{ 
		$rtmp_host = str_replace ( "http:" , "rtmp:" , $host );
		$recorderUrl = "Red5ServerURL=$rtmp_host/oflaDemo&"; 
	}
	
	echo 'wizardInitVars.recorderUrl = "'. $recorderUrl .'";';
?>

if (typeof(prodWizLogic) == 'undefined') // load the contribute.js only once
{
	activeWizard.waitForScript = true;
	createDynamicScript('/js/contribute.js');
}
else
	prodWizLogic.preInit();

</script>

<div class="cont2 step1" id="_prodWizPageType" autoFocus="true">
	<div class="top2_hint">
		<div class="top2 step1">
			<h1>Choose the type of Kaltura to create</h1>
		</div>
	</div>
	<div class="container">
		<div class="radioGroup clearfix">			
			<?php  $types =  kshow::getTypes();
				foreach ( $types as $type => $type_text ) 
					echo "<label><input name='prod_ShowType' type='radio' value='".$type."' />".$type_text."</label>"; 
			?>
		</div>
	</div>
</div>

<div class="cont2 step1_1" id="_prodWizPageTitle" autoFocus="true">
	<div class="top2_hint">
		<div class="top2 step1_1">
			<h1>Describe your Kaltura</h1>
		</div>
	</div>
	<div class="container">
		<fieldset>
			<input type="text" id="prod_ShowTitle" defValue="Enter Title here" class="has_jtip width60"/><div class="jTip"><div></div><h3>A title is required</h3><p>(you can change it later)</p></div>
			<textarea cols="33" rows="7" id="prod_ShowDescription" defValue="Add short description here" class="has_jtip width60"></textarea><div class="jTip"><div></div><h3>Tell us more</h3><p>How would you describe what this Kaltura is about?</p></div>
			<textarea cols="33" rows="7" id="prod_ShowTags" defValue="Enter Tags (separated by commas)" class="has_jtip width60"></textarea><div class="jTip"><div></div><h3>Tags</h3><p>Help users find your show by adding tags (separated by commas)</p></div>
		</fieldset>
	</div>
</div>

<div class="cont2 step2" id="_prodWizPageCustomizeNow" noNextButton="true">
	<div class="top2_hint">
		<div class="top2 step2">
		</div>
	</div>
	<div class="container">
		<p>Would you like to customize your Kaltura now?<br />(Background, Colors, Fonts)</p>
		<button class="btn3" style="margin-right:20px;">Yes!</button>
		<button class="btn3">No (later)</button>
	</div>
</div>

<div class="cont2 permissions" id="_prodWizPagePermissions">
	<div class="top2_hint">
		<div class="top2 permissions">
			<h1>Who can view and participate?</h1>
		</div>
	</div>
	<div class="container">
		<fieldset>
			<div class="col">
				<span>Who can view</span>
				<label><input type="radio" class="radio" id="prod_ViewShow1" name="prod_ViewShow" checked="checked" value="<?php echo kshow::KSHOW_PERMISSION_EVERYONE; ?>" />Everyone</label>
				<label><input type="radio" class="radio" id="prod_ViewShow2" name="prod_ViewShow" value="<?php echo kshow::KSHOW_PERMISSION_JUST_ME; ?>" />Just me (draft)</label>
				<label><input type="radio" class="radio" id="prod_ViewShow3" name="prod_ViewShow" value="<?php echo kshow::KSHOW_PERMISSION_INVITE_ONLY; ?>" />By invitation only</label><div class="pass"><input id="prod_ViewShow_Invite_pass" defValue="Enter password"/>Send password by email to friends.</div>
			</div>
			<div class="col">
				<span>Who can contribute media</span>
				<label><input type="radio" class="radio" id="prod_ContributeShow1" name="prod_ContributeShow" checked="checked" value="<?php echo kshow::KSHOW_PERMISSION_REGISTERED; ?>" />Everyone</label>
				<label><input type="radio" class="radio" id="prod_ContributeShow2" name="prod_ContributeShow" value="<?php echo kshow::KSHOW_PERMISSION_JUST_ME; ?>" />Just me</label>
				<label><input type="radio" class="radio" id="prod_ContributeShow3" name="prod_ContributeShow" value="<?php echo kshow::KSHOW_PERMISSION_INVITE_ONLY; ?>" />By invitation only</label><div class="pass"><input id="prod_ContributeShow_Invite_pass" defValue="Enter password"/>Send password by email to friends.</div>
			</div>
			<div class="col">
				<span>Who can edit</span>
				<label><input type="radio" class="radio" id="prod_EditShow1" name="prod_EditShow" checked="checked" value="<?php echo kshow::KSHOW_PERMISSION_REGISTERED; ?>" />Everyone</label>
				<label><input type="radio" class="radio" id="prod_EditShow2" name="prod_EditShow" value="<?php echo kshow::KSHOW_PERMISSION_JUST_ME; ?>" />Just me</label>
				<label><input type="radio" class="radio" id="prod_EditShow3" name="prod_EditShow" value="<?php echo kshow::KSHOW_PERMISSION_INVITE_ONLY; ?>" />By invitation only</label><div class="pass"><input id="prod_EditShow_Invite_pass" defValue="Enter password"/>Send password by email to friends.</div>
			</div>
		</fieldset>
	</div>
</div>

<div class="cont2 intro" id="_prodWizPageIntro">
	<div class="top2_hint">Create an invite (pitch your Kaltura to the world)</div>
	<div class="container">
		<fieldset>
			<div class="buttons">
				<button class="btn5 other" id="prod_introDefault">Use Default Invite</button><br/>
				<button class="btn5 cam" id="prod_introWebCam" style="text-indent: -16px;">Record from webcam<b></b></button><br/>
				<button class="btn5 other" id="prod_introOther">Upload or Import</button>
			</div>
		</fieldset>
	</div>
</div>

<div class="cont2 thumb" id="_prodWizPageThumb">
	<div class="top2_hint">Create a thumbnail for your Kaltura</div>
	<div class="container">
		<fieldset>
			<div class="buttons">
				<span><button class="btn5" id="prod_thumbBG"><div>Use background image</div></button></span>
				<span><button class="btn5 other" id="prod_thumbOther">Upload or Import</button></span>
			</div>
		</fieldset>
	</div>
</div>

<div class="cont2 customize1" id="_prodWizPageStyle" noNextButton="true">
	<div class="top2_hint">
		<div class="top2 customize1">
			<h1>Select a style for your Kaltura title</h1>
		</div>
	</div>
	<div class="container">
		<div class="centred clearfix">
			<div class="chose_style" id="prod_titleStyleSample"><span>Some Text Title</span></div>
			<span class="btn next" id="prod_nextStyle">Next Style <span class="xaquo">&raquo;</span></span>
			<span class="btn pre" id="prod_prevStyle"><span class="xaquo">&laquo;</span> Previous Style</span>
		</div><!-- end centred-->
	</div>
</div>

<div class="cont2 customize1 color_scheme" id="_prodWizPageColorScheme" noNextButton="true">
	<div class="top2_hint">
		<div class="top2 customize1 color_scheme">
			<h1>Select a color scheme</h1>
		</div>
	</div>
	<div class="container">
		<ul class="choose_scheme clearfix">
			<?php  $colorSchemes = kshow::getColorSchemes();
				foreach ( $colorSchemes as $colorScheme )
					echo '<li><div style="background-color:'.$colorScheme[0].'"><div style="background-color:'.$colorScheme[2].'; color:'.$colorScheme[1].';">a</div></div></li>';
			?>
		</ul><!-- end chose_scheme-->
		<button class="btn2" id="prod_colorSchemePreview">Preview</button>
	</div>
</div>

<div class="cont2 customize1 set_bg" id="_prodWizPagePositionBG" noNextButton="true">
	<div class="top2_hint">
		<div class="top2 customize1 set_bg">
			<h1>Select where to position the background image</h1>
		</div>
	</div>
	<div class="container">
		<p>Select where to position the background image</p>
		<ul class="choose_scheme clearfix">
				<li><img src="/images/wizard/customize/bg_tiled.png" alt="" /></li>
				<li><img src="/images/wizard/customize/bg_tl.png" alt="" /></li>
				<li><img src="/images/wizard/customize/bg_tc.png" alt="" /></li>
				<li><img src="/images/wizard/customize/bg_tr.png" alt="" /></li>		
		</ul><!-- end chose_scheme-->
		<button class="btn2" id="prod_positionBGPreview">Preview</button>
	</div>
</div>

<div class="cont2 add_intro" id="_prodWizPageAddIntro">
	<div class="top2_hint">
		<div class="top2 add_intro">
			<h1>Add a video invite</h1>
		</div>
	</div>
	<div class="container">
		<p>To begin recording your video, click the record button.</p>
		<div id="prod_VideoPlayer">
		</div>
	</div>
</div>

<div class="cont2 contType" id="_contWizPageMediaType">
	<div class="top2_hint">
		<div class="top2 media_type">
			<h1>Choose your contribution type</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="buttons">
			<button class="btn3 img" id="cont_ChooseTypePhoto">Photo<b></b></button>
			<button class="btn3 mov" id="cont_ChooseTypeVideo">Movie<b></b></button>
			<button class="btn3 snd" id="cont_ChooseTypeSound">Sound<b></b></button>
		</div>
	</div>
</div>

<div class="cont2 uploading" id="_contWizPageUpload" noNextButton="true">
	<div class="top2_hint">
		<div class="top2 uploading">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<p id="cont_uploadMessage"></p>
		<div class="loader"><div id="cont_uploadProgress" style="width:0%"></div></div>
	</div>
</div>

<div class="cont2 preview_movie" id="_contWizPagePreviewMovie">
	<div class="top2_hint">
		<div class="top2 preview_movie">
			<h1>Preview movie</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div id="cont_previewPlayer"></div>
	</div>
</div>

<div class="cont2 media_title" id="_contWizPageTitle" autoFocus="true">
	<div class="top2_hint">
		<div class="top2 media_title">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<p id="cont_titleScreenTitle">The movie is currently being converted.</p>
		<label>Give it a title</label>
		<input type="text" id="cont_mediaTitle" defValue="Enter title here" />
		<label>Add tags (so people could find it)</label>
		<input type="text" id="cont_mediaTags" defValue="Add tags seperated by commas" />
		<label for="cont_titleScreenLegitBtn"><input type="checkbox" id="cont_titleScreenLegitBtn"/><span id="cont_titleScreenLegit"></span><span class="btn" onclick="onClickNavBarStatic('tandc', true)">Terms of Use</span><span>.</span></label>
	</div>
</div>

<div class="cont2 media_title while_converting" id="_contWizPageConverting">
	<div class="top2_hint">
		<div class="top2 media_title while_converting">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<p>
			You can close the window now.<br />
			We'll let you know when the movie has been converted.
		</p>
	</div>
</div>

<div class="cont2 chose_keyframe" id="_contWizPageKeyFrame">
	<div class="top2_hint">
		<div class="top2 chose_keyframe">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<p>Select a keyframe from your movie that will represent your media</p>
		<ul class="carousel unselectable">
		</ul>
	</div>
</div>

<div class="cont2 import_OutSource" id="_contWizPageImport" autoFocus="true">
	<div class="top2_hint">
		<div class="top2 import_OutSource">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="searchAll">
			<h2 id="cont_ImportTitle">Search All YouTube</h2>
			<input type="text" id="cont_ImportSearch" defValue="" /><button class="btn1s" id="cont_ImportButton">Go!</button>
		</div><!-- end searchAll-->
		
		<div class="importOwn">
			<h2 id="cont_ImportYourOwn1">Import your own movies</h2>
			<p class="txtbox"><a id="cont_ImportYourOwn2" href="http://www.flickr.com" target="_blank">Please click here to sign into Flickr</a></p>
			<input type="text" id="cont_ImportUsername" defValue="Enter Username & Password" />
			<div class="moreFeilds">
				<input type="password" id="cont_ImportPassword" defValue="Password" />
				<br />
			</div>
			<button class="btn1s" id="cont_ImportYourOwnBtn">Go!</button>
		</div><!-- end importOwn-->
		
	</div><!-- end container-->
</div>

<div class="cont2 photoSource" id="_contWizPagePhotoSource">
	<div class="top2_hint">
		<div class="top2 photoSource" id="cont_photoSourceGeneralTitle">
			<h1>Select photo source</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="buttons">
			<button class="btn6 browse" id="cont_ChoosePhotoBrowse"><span>Browse your computer</span><b></b></button>
			<button class="btn6 flickr" id="cont_ChoosePhotoFlickr"><span>Import from</span><b></b></button>
			<button class="btn6 photobucket" id="cont_ChoosePhotoPhotobucket"><span>Import from</span><b></b></button>
			<button class="btn6 nypl" id="cont_ChoosePhotoNYPL"><span>Import from NYPL</span><b></b></button>
		</div>
		<button class="btn1s floatr" id="cont_ChoosePhotoUrlBtn">Go</button><input type="text" class="floatr width40" id="cont_ChoosePhotoUrl" defValue="Enter URL" />
		<p class="note">We currently support these file types:<br/>.bmp, .gif, .jpg, .png.</p>
	</div><!-- end container-->
</div>

<div class="cont2 videoSource photoSource" id="_contWizPageVideoSource">
	<div class="top2_hint">
		<div class="top2 VideoSource">
			<h1>Select video source</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="buttons">
			<button class="btn6 myspace" id="cont_ChooseVideoMySpace"><span>Import from</span><b></b></button>
			<button class="btn6 youtube" id="cont_ChooseVideoYouTube"><span>Import from</span><b></b></button>
			<button class="btn6 photobucket" id="cont_ChooseVideoPhotobucket"><span>Import from</span><b></b></button>
			<button class="btn6 current" id="cont_ChooseVideoCurrent" style="display:none"><span>Import from</span><b></b></button>
		</div>
		<div class="buttons" style="float: right; height: auto;">
			<button class="btn6 browse floatr" id="cont_ChooseVideoBrowse"><span>Browse your computer</span><b></b></button>
			<button class="btn6 webcam floatr" id="cont_ChooseVideoWebCam"><span>Record from webcam</span><b></b></button>
		</div>
		<button class="btn1s floatr" id="cont_ChooseVideoUrlBtn">Go</button><input type="text" id="cont_ChooseVideoUrl" class="floatr width40" defValue="Enter URL" />
		<p class="note">We currently support these file types:<br/>.flv, .wmv, .asf, .avi, .qt, .mov, .mpg. <br/>Maximum file size: 200MB</p>
	</div>
</div>

<div class="cont2 SoundSource photoSource" id="_contWizPageSoundSource">
	<div class="top2_hint">
		<div class="top2 SoundSource">
			<h1>Select sound source</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="buttons">
			<button class="btn6 browse" id="cont_ChooseSoundBrowse"><span>Browse your computer</span><b></b></button>
			<button class="btn6 mic" id="cont_ChooseSoundMic"><span>Record from mic</span><b></b></button>
		</div>
		<div class="field floatr">
			<input type="text" class="width80" id="cont_ChooseSoundJamendo" defValue="Search Jamendo" />
			<button class="btn1s" id="cont_ChooseSoundJamendoBtn" >Go!</button>
		</div>
		<div class="field floatr">
			<input type="text" class="width80" id="cont_ChooseSoundCCMixter" defValue="Search CCmixter" />
			<button class="btn1s" id="cont_ChooseSoundCCMixterBtn">Go!</button>
		</div>
		<div class="field floatr">
			<input type="text" class="width80" id="cont_ChooseSoundUrl" defValue="Enter URL" />
			<button class="btn1s" id="cont_ChooseSoundUrlBtn">Go!</button>
		</div>
		<p class="note">We currently support these file types: .mp3.</p>
	</div>
</div>


<div class="cont2 SoundSource SoundSearch" id="_contWizPageSoundSearch">
	<div class="top2_hint">
		<div class="top2 SoundSource SoundSearch">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="col0">
			<p>Search by keywords or tags</p>
		</div>
		<div class="col1">
			<input type="text" id="cont_ChooseSoundTerm" defValue="Enter Search Term" /><button class="btn1s" id="cont_ChooseSoundTermBtn">Go!</button>
			<ul id="cont_SoundsList">
			</ul>
		</div>
		<div id="cont_mp3player_cont" style='position:absolute;left:-1000px;top:-1000px'>
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
				codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" 
				width="5" height="5" id="mp3player" align="middle">
				<param name="allowScriptAccess" value="sameDomain" />
				<param name="allowFullScreen" value="false" />
				<param name="movie" value="/swf/mp3.swf" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />	
				<embed src="/swf/mp3.swf" quality="high" bgcolor="#ffffff" width="5" height="5" name="mp3player" 
				align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" 
				type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>
		</div>
	</div>
</div>

<div class="cont2 import_media" id="_contWizPageImportMedia" noNextButton="true" autoFocus="true">
	<div class="top2_hint">
		<div class="top2 import_media">
			<h1>Import Media</h1>
		</div>
	</div>
	<div class="container">
		<div class="top clearfix">
			<h2>Search results</h2>
			<button id="cont_ImportMediaButton" class="search1"></button><input type="text" id="cont_ImportMediaField" />
		</div>
		<div class="centered clearfix">
			<ul class="carousel unselectable">
			</ul>
		</div><!-- end centered-->
		<div class="info clearfix">
			<div class="tiled"></div>
			<div class="tl"></div>
			<div class="tc"></div>
			<div class="tr"></div>
		</div><!-- end info-->
	</div>
</div>

<div class="cont2 record_video" id="_contWizPageRecordVideo">
	<div class="top2_hint">
		<div class="top2 record_video">
			<h1>Record Video</h1>
		</div>
	</div>
	<div class="container">
		<p id="cont_recordInfo"></p>
		<div id="cont_videoRecorder"></div>
	</div>
</div>

<div class="cont2 " id="_contWizPagePhotoBrowse" noNextButton="true">
	<div class="top2_hint">
		<div class="top2">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<p><img id="cont_previewImage" style='width:100px;height:75px'/><span id="cont_photoBrowseAgain">Browse again</span></p>
	</div>
</div>

<div class="cont2 " id="_contWizPagePhotoUrl" noNextButton="true">
	<div class="top2_hint">
		<div class="top2">
			<h1>Contribute media</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<p><img style='width:100px;height:75px'/><input id="cont_photoUrl"/><span>Preview</span></p>
	</div>
</div>

<div class="cont2 contWizPageSuccess" id="_contWizPageSuccess">
	<div class="top2_hint">
		<div class="top2">
		</div>
	</div>
	<div class="container">
		<p id="cont_successMessage"></p>
	</div>
</div>


<div class="cont2 step2" id="_contWizPageCancel" noNextButton="true">
	<div class="top2_hint">
		<div class="top2 step2">
			Do you really want to cancel?
		</div>
	</div>
	<div class="container">
		<p>Are you sure?</p>
		<span><button class="btn1">Yes</button></span>
		<span><button class="btn1">No!!!</button></span>
	</div>
</div>


<div class="cont2 prod_edit" id="_prodWizPageEdit">
	<div class="top2_hint">
		<div class="top2 prod_edit">
			<h1>Customize your Kaltura</h1>
		</div><!-- end top-->
	</div>
	<div class="container clearfix">
		<div class="buttons">
			<button class="btn5" id="prod_EditTitle">Kaltura Info<b></b></button>
			<button class="btn5" id="prod_EditStyle">Title style<b></b></button>
			<button class="btn5" id="prod_EditPermissions">Permissions<b></b></button>
			<button class="btn5" id="prod_EditBG">Background<b></b></button>
			<button class="btn5" id="prod_EditIntro">Invite<b></b></button>
			<button class="btn5" id="prod_EditRemoveBG">Remove background<b></b></button>
			<button class="btn5" id="prod_EditThumb">Thumbnail<b></b></button>
			<button class="btn5" id="prod_EditColors">Colors<b></b></button>
		</div>
	</div>
</div>

