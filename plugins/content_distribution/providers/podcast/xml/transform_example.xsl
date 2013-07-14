<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:media="http://search.yahoo.com/mrss/"
xmlns:xs="http://www.w3.org/2001/XMLSchema"
xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
xmlns:php="http://php.net/xsl"
exclude-result-prefixes="xs">
  <xsl:output method="xml" encoding="UTF-8" indent="yes" />
   <xsl:variable name="distributionProfileName" select="'My Hab'"/>
  
  
  <xsl:template name="rss" match="/">
	<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
		<xsl:for-each select="rss">
		   <channel>
				<title>Put here your podcast title - Maximum length for all of the channel fields 255 charcters</title>
				<link>http://fe-stage.alldigital.com/api_v3/getFeed.php?partnerId=148&amp;feedId=0_6qx8xhvp</link>
				<language>en-us</language>
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
	<xsl:variable name="podFlvor" select="distribution[@distributionProfileName=$distributionProfileName]/flavorAssetIds/flavorAssetId" />
	<xsl:variable name="server" select="'http://www.kaltura.com'" />
		<item>
			<title>
			  <xsl:value-of select="name" />		
			</title>
			<itunes:author>
			   <xsl:value-of select="customData/metadata/Author" />
			</itunes:author>
			<itunes:block>
			   <xsl:value-of select="customData/metadata/Block" />
			</itunes:block>
			<itunes:explicit>
			   <xsl:value-of select="customData/metadata/Explicit" />
			</itunes:explicit>
			<itunes:order>
			   <xsl:value-of select="customData/metadata/Order" />
			</itunes:order>
			<itunes:summary>
			  <xsl:value-of select="description" />		
			</itunes:summary>
			<itunes:image href="{thumbnailUrl/@url}/ext.jpg" />
			<enclosure url="{content[@flavorAssetId=$podFlvor]/@url}/ext.mp4" type="video/mp4" />
			<guid>
			  <xsl:value-of select="entryId" />				
			</guid>
			<pubDate>
				<xsl:value-of select="php:function('date', 'D, d M Y H:i:s \G\M\T', sum(createdAt))" />		
			</pubDate>
			<itunes:duration>
				<xsl:value-of select="round(sum(media/duration) div 1000)" />
			</itunes:duration>
			<itunes:keywords>
			   <xsl:value-of select="customData/metadata/Keywords" />
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
