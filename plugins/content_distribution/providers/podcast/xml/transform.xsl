<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:media="http://search.yahoo.com/mrss/"
xmlns:xs="http://www.w3.org/2001/XMLSchema"
xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
xmlns:php="http://php.net/xsl"
exclude-result-prefixes="xs">
  <xsl:output method="xml" encoding="UTF-8" indent="yes" />
   <xsl:variable name="distributionProfileId" select="'[my distribution profileId]'"/>
  
  
  <xsl:template name="rss" match="/">
	<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" 
	xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" 
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" 
	xmlns:dc="http://purl.org/dc/elements/1.1/" 
	version="2.0">
		<xsl:for-each select="rss">
		   <channel>
				<title>Put here your podcast title - Maximum length for all of the channel fields 255 charcters</title>
				<link>link to the feed</link>
				<language>en-us</language>
				<managingEditor>podcasts@hbo.com</managingEditor>
				<generator>Kaltura - Open Source Video</generator>
				<ttl>60</ttl>
				<itunes:explicit>yes</itunes:explicit>
				<itunes:keywords>This tag allows users to search on a maximum of 12 text keywords. Use commas to separate keywords</itunes:keywords>
				<itunes:subtitle>The contents of this tag are shown in the Description column in iTunes. The subtitle displays best if it is only a few words long</itunes:subtitle>
				<copyright>Any copyright you want</copyright>
				<itunes:subtitle></itunes:subtitle>
				<itunes:author>Author name</itunes:author>
				<itunes:summary></itunes:summary>
				<itunes:owner>
				<itunes:name>John Doe</itunes:name>
				<itunes:email>john.doe@example.com</itunes:email>
				</itunes:owner>
				<itunes:image href="http://corp.kaltura.com/images/header/kalturaLogo.png" />
				<itunes:category text="TV &amp; Film">
				</itunes:category>
				<xsl:apply-templates name="item"
				select="channel/items/item" />
		   </channel>
		</xsl:for-each>
	</rss>
  </xsl:template>
  <xsl:template name="item" match="item">
	<xsl:variable name="podFlvor" select="distribution[@distributionProfileId=$distributionProfileId]/flavorAssetIds/flavorAssetId" />
	<xsl:variable name="server" select="'http://www.kaltura.com'" />
		<item>
			<title>
				<xsl:choose>
					<xsl:when test="string-length(customData/metadata/PodcastTitle) > 0">
						<xsl:value-of select="customData/metadata/PodcastTitle" />		
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="name" />		
					</xsl:otherwise>
				</xsl:choose>
			</title>
			<link>http://www.hbo.com/</link>
			<itunes:author>
			   <xsl:value-of select="customData/metadata/PodcastAuthor" />
			</itunes:author>
			<itunes:block>
				<xsl:value-of select="customData/metadata/PodcastBlock" />
			</itunes:block>
			<itunes:explicit>
			   <xsl:value-of select="customData/metadata/PodcastExplicit" />
			</itunes:explicit>
			<itunes:order>
				<xsl:value-of select="customData/metadata/PodcastOrder" />
			</itunes:order>
			<itunes:summary>
			  <xsl:value-of select="customData/metadata/PodcastSummary" />		
			</itunes:summary>
			<description>
				<xsl:choose>
					<xsl:when test="string-length(customData/metadata/PodcastDescription) > 0">
						<xsl:value-of select="customData/metadata/PodcastDescription" />		
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="description" />		
					</xsl:otherwise>
				</xsl:choose>
			</description>
			<itunes:image href="{thumbnailUrl/@url}/ext.jpg" />
			<enclosure url="{content[@flavorAssetId=$podFlvor]/@url}/ext.mp4" length="{media/duration}" type="video/mp4" />
			<itunes:subtitle>
				<xsl:value-of select="customData/metadata/PodcastTitle" />
			</itunes:subtitle>
			<guid isPermaLink="false">
				<xsl:value-of select="entryId" />				
			</guid>
			<pubDate>
				<xsl:value-of select="php:function('date', 'D, d M Y H:i:s \G\M\T', sum(createdAt))" />		
			</pubDate>
			<itunes:duration>
				<xsl:if test="string-length(floor(sum(media/duration) div (1000*60*60)))=1">0</xsl:if><xsl:value-of select="floor(sum(media/duration) div (1000*60*60))" />:<xsl:if test="string-length(floor((sum(media/duration) div (1000*60)) mod 60))=1">0</xsl:if><xsl:value-of select="floor((sum(media/duration) div (1000*60)) mod 60)" />:<xsl:if test="string-length(floor((sum(media/duration) div 1000) mod 60))=1">0</xsl:if><xsl:value-of select="floor((sum(media/duration) div 1000) mod 60)" />
			</itunes:duration>
			<itunes:keywords>
				<xsl:choose>
					<xsl:when test="string-length(customData/metadata/PodcastKeywords) > 0">
						<xsl:value-of select="customData/metadata/PodcastKeywords" />		
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="implode">
							<xsl:with-param name="items" select="tags/tag" />
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</itunes:keywords>
		</item>
  </xsl:template>
  <xsl:template name="implode">
    <xsl:param name="items" />
    <xsl:param name="separator" select="','" />
    <xsl:for-each select="$items">
      <xsl:if test="position() &gt; 1">
        <xsl:value-of select="$separator" />
      </xsl:if>
      <xsl:value-of select="." />
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>