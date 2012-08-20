
<script>

function deleteReport( id )
{
	if( confirm("Are you sure? \r\nPress [OK] to confirm deletion of report id: " + id ) )document.location = "/index.php/system/deleteReport?id=" + id;
}
</script>

<TABLE border="1" cellspacing="2" cellpadding="10"	>
<?php

function reportTypeToText( $type )
{
	switch( $type )
	{
		case flag::FLAG_TYPE_COPYRIGHT: return 'Copyright';
		case flag::FLAG_TYPE_OFFENSIVE: return 'Offensive';
		case flag::FLAG_TYPE_SPAM: return 'Spam';
		case flag::FLAG_TYPE_OTHER: return 'Other:';
		default: return 'Unknown';
	}
	
}


function subjectTypeToText( $type )
{
	switch( $type )
	{
		case flag::SUBJECT_TYPE_ENTRY: return 'Entry';
		case flag::SUBJECT_TYPE_USER: return 'User';
		case flag::SUBJECT_TYPE_COMMENT: return 'Comment';
		default: return 'Unknown';
	}
}

function retrieveSubject( $type, $id )
{
	switch( $type )
	{
		case flag::SUBJECT_TYPE_ENTRY: { $entry = entryPeer::retrieveByPK( $id ); return 'entry id:'.$id.'<br/>kshow:'.returnKshowLink($entry->getKshowId()).'<br/>Name:'.$entry->getName(); }
		case flag::SUBJECT_TYPE_USER: { $user = kuserPeer::retrieveByPK( $id ); return returnUserLink( $user->getScreenName()); }
		case flag::SUBJECT_TYPE_COMMENT: { $comment = commentPeer::retrieveByPK( $id ); return 'comment id:'.$id.'<br/>Commnet:'.$comment->getComment(); }
		default: return 'Unknown';
	}
}

function returnUserLink( $username )
{
	return "<a href='/index.php/mykaltura/viewprofile?screenname=".$username."'>".$username."</a>";
}

function returnKshowLink( $kshow_id )
{
	return "<a href='/index.php/browse?kshow_id=".$kshow_id."'>".$kshow_id."</a>";
}


?>
<a href="/index.php/system/login?exit=true">logout</a><br>
<?php	
if( !$reports ) echo '<h1>No reports found</h1>';
	else 
	{
	echo '<h1>List of user reports</h1><h2>Updated: '.date("D M j G:i:s T Y").'</h2>';
	echo '<TR><TD>ID</TD><TD>Date</TD><TD>Reporting User</TD><TD>Type</TD><TD width="40%">Comment</TD><TD>Subject type</TD><TD>Subject</TD><TD>Action</TD></TR>';
		foreach ( $reports as $report )
		{
			echo '<TR>'.	
			'<TD>'.$report->getId().'</TD>'.
			'<TD>'.$report->getCreatedAt().'</TD>'.
			'<TD>'.returnUserLink( $report->getkuser()->getScreenName()).'</TD>'.
			'<TD>'.reportTypeToText( $report->getFlagType() ).'<br/>'.$report->getOther().'</TD>'.
			'<TD>'.$report->getComment().'</TD>'.
			'<TD>'.subjectTypeToText($report->getSubjectType()).'</TD>'.
			'<TD>'.retrieveSubject( $report->getSubjectType(), $report->getSubjectId()).'</TD>'.
			'<TD><a href="javascript:deleteReport('.$report->getId().')">Delete</a></TD>'.
			
			'</TR>';
		}
	}

?>
</TABLE>