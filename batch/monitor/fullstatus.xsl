<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<xsl:output omit-xml-declaration="no" method="html" />
	<xsl:variable name="minute" select="60"/><!-- 60 seconds -->
	
	<xsl:variable name="alert-Scheduler-Status-Time" select="10 * $minute"/>
	
	<xsl:variable name="alert-Queue-Size-Convert" select="100"/>
	<xsl:variable name="alert-Queue-Size-Import" select="200"/>
	<xsl:variable name="alert-Queue-Size-Delete" select="100"/>
	<xsl:variable name="alert-Queue-Size-Flatten" select="5"/>
	<xsl:variable name="alert-Queue-Size-Bulk-Upload" select="5"/>
	<xsl:variable name="alert-Queue-Size-DVD-Creator" select="100"/>
	<xsl:variable name="alert-Queue-Size-Download" select="100"/>
	<xsl:variable name="alert-Queue-Size-Convert-Profile" select="100"/>
	<xsl:variable name="alert-Queue-Size-Post-Convert" select="100"/>
	<xsl:variable name="alert-Queue-Size-Pull" select="100"/>
	<xsl:variable name="alert-Queue-Size-Remote-Convert" select="100"/>
	<xsl:variable name="alert-Queue-Size-Extract-Media" select="500"/>
	<xsl:variable name="alert-Queue-Size-Mail" select="100"/>
	<xsl:variable name="alert-Queue-Size-Notification" select="200"/>
	<xsl:variable name="alert-Queue-Size-Bulk-Download" select="150"/>
		
	<xsl:template match="result">
		<xsl:variable name="now" select="@timestamp"/>
		
		<xsl:variable name="Convert-Batches" select="sum(schedulers/item/workers/item[typeName='Convert']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Import-Batches" select="sum(schedulers/item/workers/item[typeName='Import']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Delete-Batches" select="sum(schedulers/item/workers/item[typeName='Delete']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Flatten-Batches" select="sum(schedulers/item/workers/item[typeName='Flatten']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Bulk-Upload-Batches" select="sum(schedulers/item/workers/item[typeName='Bulk Upload']/statuses/item[type=1]/value)"/>
		<xsl:variable name="DVD-Creator-Batches" select="sum(schedulers/item/workers/item[typeName='DVD Creator']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Download-Batches" select="sum(schedulers/item/workers/item[typeName='Download']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Convert-Profile-Batches" select="sum(schedulers/item/workers/item[typeName='Convert Profile']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Post-Convert-Batches" select="sum(schedulers/item/workers/item[typeName='Post Convert']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Pull-Batches" select="sum(schedulers/item/workers/item[typeName='Pull']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Remote-Convert-Batches" select="sum(schedulers/item/workers/item[typeName='Remote Convert']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Extract-Media-Batches" select="sum(schedulers/item/workers/item[typeName='Extract Media']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Mail-Batches" select="sum(schedulers/item/workers/item[typeName='Mail']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Notification-Batches" select="sum(schedulers/item/workers/item[typeName='Notification']/statuses/item[type=1]/value)"/>
		<xsl:variable name="Bulk-Download-Batches" select="sum(schedulers/item/workers/item[typeName='Bulk Download']/statuses/item[type=1]/value)"/>
		
		
		
		
		
		
		
		
						
		<xsl:variable name="message">
						
			<xsl:for-each select="queuesStatus/item">
			
				<xsl:choose>
					<!-- 
					<xsl:when test="typeName = 'Delete'">
						<xsl:if test="size &gt; $alert-Queue-Size-Delete">
							Delete queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Download'">
						<xsl:if test="size &gt; $alert-Queue-Size-Download">
							Download queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					-->
					
					<xsl:when test="typeName = 'Flatten'">
						<xsl:if test="size &gt; $alert-Queue-Size-Flatten">
							Flatten queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Convert'">
						<xsl:if test="size &gt; $alert-Queue-Size-Convert">
							Convert queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Import'">
						<xsl:if test="size &gt; $alert-Queue-Size-Import">
							Import queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Bulk Download'">
						<xsl:if test="size &gt; $alert-Queue-Size-Bulk-Download">
							Bulk-Download queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Bulk Upload'">
						<xsl:if test="size &gt; $alert-Queue-Size-Bulk-Upload">
							Bulk-Upload queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'DVD Creator'">
						<xsl:if test="size &gt; $alert-Queue-Size-DVD-Creator">
							DVD-Creator queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Convert Profile'">
						<xsl:if test="size &gt; $alert-Queue-Size-Convert-Profile">
							Convert-Profile queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Post Convert'">
						<xsl:if test="size &gt; $alert-Queue-Size-Post-Convert">
							Post-Convert queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Pull'">
						<xsl:if test="size &gt; $alert-Queue-Size-Pull">
							Pull queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Remote Convert'">
						<xsl:if test="size &gt; $alert-Queue-Size-Remote-Convert">
							Remote-Convert queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Extract Media'">
						<xsl:if test="size &gt; $alert-Queue-Size-Extract-Media">
							Extract-Media queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Mail'">
						<xsl:if test="size &gt; $alert-Queue-Size-Mail">
							Mail queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Notification'">
						<xsl:if test="size &gt; $alert-Queue-Size-Notification">
							Notification queue is <xsl:value-of select="size"/>
						</xsl:if>
					</xsl:when>
				</xsl:choose>
					
				<!-- 
				<xsl:choose>
					<xsl:when test="typeName = 'Delete'">
						<xsl:if test="$Delete-Batches &lt; (size div 2)">
							Delete queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Convert-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Flatten'">
						<xsl:if test="$Flatten-Batches &lt; (size div 2)">
							Flatten queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Convert-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Download'">
						<xsl:if test="$Download-Batches &lt; (size div 2)">
							Download queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Download-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Convert'">
						<xsl:if test="$Convert-Batches &lt; (size div 2)">
							Convert queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Convert-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Import'">
						<xsl:if test="$Import-Batches &lt; (size div 2)">
							Import queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Import-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Bulk Download'">
						<xsl:if test="$Bulk-Download-Batches &lt; (size div 2)">
							Bulk-Download queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Bulk-Download-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Bulk Upload'">
						<xsl:if test="$Bulk-Upload-Batches &lt; (size div 2)">
							Bulk-Upload queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Bulk-Upload-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'DVD Creator'">
						<xsl:if test="$DVD-Creator-Batches &lt; (size div 2)">
							DVD-Creator queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$DVD-Creator-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Convert Profile'">
						<xsl:if test="$Convert-Profile-Batches &lt; (size div 2)">
							Convert-Profile queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Convert-Profile-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Post Convert'">
						<xsl:if test="$Post-Convert-Batches &lt; (size div 2)">
							Post-Convert queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Post-Convert-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Pull'">
						<xsl:if test="$Pull-Batches &lt; (size div 2)">
							Pull queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Pull-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Remote Convert'">
						<xsl:if test="$Remote-Convert-Batches &lt; (size div 2)">
							Remote-Convert queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Remote-Convert-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Extract Media'">
						<xsl:if test="$Extract-Media-Batches &lt; (size div 2)">
							Extract-Media queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Extract-Media-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Mail'">
						<xsl:if test="$Mail-Batches &lt; (size div 2)">
							Mail queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Mail-Batches"/> batches are running</xsl:if>
					</xsl:when>
					<xsl:when test="typeName = 'Notification'">
						<xsl:if test="$Notification-Batches &lt; (size div 2)">
							Notification queue is <xsl:value-of select="size"/> and only <xsl:value-of select="$Notification-Batches"/> batches are running</xsl:if>
					</xsl:when>
				</xsl:choose>
				-->
				
			</xsl:for-each>
			
			<xsl:for-each select="schedulers/item">
			<!--	<xsl:if test="configuredId != 30 and configuredId != 11"> -->
				<xsl:choose>
					<xsl:when test="($now - $alert-Scheduler-Status-Time) &gt; lastStatus">
						Scheduler '<xsl:value-of select="name"/>' [<xsl:value-of select="configuredId"/>] is dead
					</xsl:when>
				</xsl:choose>
			<!--	</xsl:if>  -->
			</xsl:for-each>
			
		</xsl:variable>
		
		
		<xsl:variable name="color">
			<xsl:choose>
				<xsl:when test="$message = ''">green</xsl:when>
				<xsl:otherwise>red</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:copy-of select="$color"/> `date`


		<font color="red">
			<xsl:copy-of select="$message"/>
		</font>
		
		<table border="1" bordercolor="green" cellspacing="0">
			<tr>
				<th>Type</th>
				<th>Queue Size</th>
				<th>AVG Wait</th>
			</tr>
			<xsl:for-each select="queuesStatus/item">
				<tr>
					<td><xsl:value-of select="typeName"/></td>
					<td><xsl:value-of select="size"/></td>
					<td><xsl:value-of select="round(waitTime div 3600)"/> Minutes</td>
				</tr>		
			</xsl:for-each>
		</table>
		<br/><br/>
		
		<xsl:for-each select="schedulers/item">
		
			<b><u><xsl:value-of select="name"/></u></b><br/>
			<xsl:text>ID: </xsl:text><xsl:value-of select="configuredId"/><br/>
			<xsl:text>Last Status: </xsl:text><xsl:value-of select="lastStatusStr"/><br/><br/>
			
			<table border="1" bordercolor="green" cellspacing="0">
				<tr>
					<th></th>
					<th>ID</th>
					<th>Name</th>
					<th>Type</th>
					<th>Last Status</th>
					<th>Batches</th>
					<th>Max</th>
					<th>Locked</th>
					<th>AVG Wait</th>
					<th>AVG Work</th>
				</tr>
				<xsl:for-each select="workers/item">
					<tr>
						<td>
							<xsl:choose>
								<xsl:when test="statuses/item[type=7]/value=1"><img src="/xymon/gifs/green-recent.gif"/></xsl:when>
								<xsl:otherwise><img src="/xymon/gifs/clear-recent.gif"/></xsl:otherwise>
							</xsl:choose>
						</td>
						<td><xsl:value-of select="configuredId"/></td>
						<td><xsl:value-of select="name"/></td>
						<td><xsl:value-of select="typeName"/></td>
						<td><xsl:value-of select="lastStatusStr"/></td>
						<td><xsl:value-of select="statuses/item[type=1]/value"/></td>
						<td><xsl:value-of select="configs/item[variable='maxInstances']/value"/></td>
						<td><xsl:value-of select="count(lockedJobs/item)"/></td>
						<td><xsl:value-of select="avgWait"/></td>
						<td><xsl:value-of select="avgWork"/></td>
					</tr>
				</xsl:for-each>
			</table>
			
		</xsl:for-each>
		
		batches are ok
		<xsl:for-each select="queuesStatus/item">
			<xsl:text>
			</xsl:text>
			<xsl:value-of select="typeName"/> queue: <xsl:value-of select="size"/>
			<xsl:text>
			</xsl:text>
			<xsl:value-of select="typeName"/> avg wait: <xsl:value-of select="round(waitTime div 3600)"/>	
		</xsl:for-each>
		
		<xsl:text>
		</xsl:text>
		
		<xsl:for-each select="schedulers/item">
			schd <xsl:value-of select="configuredId"/> name: <xsl:value-of select="name"/>
			schd <xsl:value-of select="configuredId"/> status time: <xsl:value-of select="lastStatus"/>
		<!--	<xsl:for-each select="workers/item">
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> name: <xsl:value-of select="name"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> type: <xsl:value-of select="type"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> type name: <xsl:value-of select="typeName"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> running: <xsl:value-of select="statuses/item[type=7]/value=1"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> batches: <xsl:value-of select="statuses/item[type=1]/value"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> max: <xsl:value-of select="configs/item[variable='maxInstances']/value"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> locked: <xsl:value-of select="count(lockedJobs/item)"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> avg wait: <xsl:value-of select="avgWait"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> avg work: <xsl:value-of select="avgWork"/>
				schd <xsl:value-of select="configuredId"/> wrk <xsl:value-of select="configuredId"/> status time: <xsl:value-of select="lastStatus"/>
			</xsl:for-each> -->
		</xsl:for-each>
		
		XSL time: 
	</xsl:template>
</xsl:stylesheet>
