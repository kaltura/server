<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:php="http://php.net/xsl" exclude-result-prefixes="xs">
    <xsl:output method="xml" media-type="application/rss+xml" cdata-section-elements="title description media:keywords"/>
    <xsl:param name="partnerId" select="'partnerId'" />

    <xsl:template match="*">
        <xsl:element name="{local-name()}">
            <xsl:apply-templates />
        </xsl:element>
    </xsl:template>
    <xsl:template name="rss" match="/">
        <rss xmlns:media="http://search.yahoo.com/mrss/">
            <xsl:for-each select="rss">
                <xsl:variable name="var1_channel" select="channel" />
                <xsl:attribute name="version">
                    <xsl:value-of select="string(@version)" />
                </xsl:attribute>
                <channel>
                    <title>
                        <xsl:value-of select="string($var1_channel/title)" />
                    </title>
                    <description>This is a dummy description which is manually set in XSLT file. The playlist can't retain a description so each channel must have a manually added description of their content.</description>
                    <link>http://corp.kaltura.com/</link>
                    <xsl:apply-templates name="item" select="channel/items/item" />
                </channel>
            </xsl:for-each>
        </rss>
    </xsl:template>
    <xsl:template name="item" match="item">
        <xsl:variable name="var1_content" select="content" />
        <xsl:variable name="var1_media" select="media" />
        <xsl:variable name="title" select="string(title)" />
        <xsl:variable name="thumbnail" select="thumbnailUrl" />
        <xsl:variable name="var2_updatedAt" select="updatedAt" />
        <xsl:variable name="stamp" select="createdAt" />
        <xsl:variable name="tags" select="tags" />
        <xsl:variable name="entryId" select="string(entryId)" />
        <item>
            <guid isPermalink="false">
                <xsl:value-of select="string(entryId)" />
            </guid>
            <title>
                <xsl:value-of select="$title" disable-output-escaping="no"/>
            </title>
            <description>
                <xsl:value-of select="string(description)" disable-output-escaping="no" />
            </description>
            <pubdate>
                <xsl:value-of select="php:function('date', 'c', sum($stamp))"/>
            </pubdate>
            <!--<xsl:for-each select="$var1_content">-->
            <media:content>
                <xsl:attribute name="url">
                    <xsl:value-of select="concat ('http://cdnapi.kaltura.com/p/', $partnerId, '/sp/', $partnerId, '00/playManifest/entryId/', $entryId, '/format/applehttp/protocol/http/a/a.m3u8')" />
                </xsl:attribute>
                <xsl:attribute name="duration">
                    <xsl:value-of select="string($var1_media/duration) div 1000" />
                </xsl:attribute>
            </media:content>
            <!--</xsl:for-each>-->
            <xsl:element name="media:thumbnail">
                <xsl:attribute name="url">
                    <xsl:value-of select="concat(thumbnailUrl/@url, '/width/800/height/450')" />
                </xsl:attribute>
            </xsl:element>
            <xsl:element name="media:keywords">
                <xsl:call-template name="implodeTags">
                    <xsl:with-param name="items" select="tags/tag" />
                </xsl:call-template>, <xsl:call-template name="implode"><xsl:with-param name="items" select="category/@name"  /></xsl:call-template></xsl:element>
        </item>
    </xsl:template>
    <xsl:template name="implode">
        <xsl:param name="items" />
        <xsl:param name="separator" select="','" />
        <xsl:for-each select="$items">
            <xsl:if test="position() &gt; 1">
                <xsl:value-of select="$separator" />
            </xsl:if>
            <xsl:if test="not (starts-with(., ' ')) and position() &gt; 1">
                <xsl:value-of select="' '" />
            </xsl:if>
            <xsl:value-of select="." />
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="implodeTags">
        <xsl:param name="items" />
        <xsl:param name="separator" select="','" />
        <xsl:for-each select="$items">
            <xsl:if test="position() &gt; 1">
                <xsl:value-of select="$separator" />
            </xsl:if>
            <xsl:if test="not (starts-with(., ' ')) and position() &gt; 1">
                <xsl:value-of select="' '" />
            </xsl:if>
            <xsl:value-of select="." disable-output-escaping="no"/>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>