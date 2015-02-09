<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:media="http://search.yahoo.com/mrss/" xmlns:fn="http://www.w3.org/2005/xpath-functions" exclude-result-prefixes="xs fn">
  <xsl:output method="xml" encoding="UTF-8" indent="yes" />
  <xsl:variable name="distributionProfileName" select="''"/>
  
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

  <xsl:template name="rss" match="/">
    <rss xmlns:media="http://search.yahoo.com/mrss/" version="2.0">
      <xsl:for-each select="rss">
        <channel>
          <title>Fox Sports Videos</title>
          <link>http://msn.foxsports.com/video</link>
          <description>Fox Sports Videos</description>
          <language>en-us</language>
          <xsl:apply-templates name="item" select="channel/items/item" />
        </channel>
      </xsl:for-each>
    </rss>
  </xsl:template>

  <xsl:template name="item" match="item">
    <xsl:variable name="podFlvor" select="distribution[@distributionProfileName=$distributionProfileName]/flavorAssetIds/flavorAssetId" />
    <item>
      <pubDate>
        <xsl:value-of select="php:function('date', 'Y-m-d\TH:i:s\Z', sum(createdAt))" />
      </pubDate>
      <guid>
        <xsl:value-of select="entryId" />
      </guid>
      <media:content>
        <xsl:attribute name="url">
          <xsl:choose>
            <xsl:when test="string-length($podFlvor) > 0">
              <xsl:value-of select="content[@flavorAssetId=$podFlvor]/@url"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="content/@url"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:attribute>
        <media:title>
          <xsl:choose>
            <xsl:when test="string-length(customData/metadata/LongTitle) > 0">
              <xsl:value-of select="customData/metadata/LongTitle"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="title" />
            </xsl:otherwise>
          </xsl:choose>
        </media:title>
        <media:description>
          <xsl:choose>
            <xsl:when test="string-length(customData/metadata/LongDescription) > 0">
              <xsl:value-of select="customData/metadata/LongDescription"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="description" />
            </xsl:otherwise>
          </xsl:choose>
        </media:description>
        <media:keywords>
          <xsl:choose>
            <xsl:when test="count(customData/metadata/StatskeysFull/statskeys/statskey/statskeyName) > 0">
              <xsl:call-template name="implode">
                <xsl:with-param name="items" select="customData/metadata/StatskeysFull/statskeys/statskey/statskeyName" />
              </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="implode">
                <xsl:with-param name="items" select="tags/tag"/>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>
        </media:keywords>
        <media:thumbnail width="" height="">
          <xsl:attribute name="url">
            <xsl:value-of select="thumbnail/@url"/>
          </xsl:attribute>
        </media:thumbnail>
        <media:credit role="publisher">Fox Sports</media:credit>
      </media:content>
    </item>
  </xsl:template>
</xsl:stylesheet>