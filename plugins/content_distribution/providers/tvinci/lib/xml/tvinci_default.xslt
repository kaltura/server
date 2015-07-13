<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:exsl="http://exslt.org/common"
                extension-element-prefixes="exsl"
                version="1.0">

    <xsl:output omit-xml-declaration="no" indent="yes" />

    <xsl:strip-space elements="*"/>

    <xsl:template match="node()|@*">
        <xsl:copy>
            <xsl:apply-templates select="node()|@*"/>
        </xsl:copy>
    </xsl:template>

    <xsl:template name="convert-known-meta-element">
        <xsl:param name="prefix"/>
        <xsl:param name="asValue"/>
        <xsl:variable name="prefix-length" select="string-length($prefix)+1"/>
        <xsl:variable name="currNodeName" select="local-name(.)"/>
        <xsl:variable name="suffix" select="substring($currNodeName,$prefix-length)"/>
        <xsl:variable name="normalized-suffix" select="normalize-space(translate($suffix,'_',' '))"/>
        <xsl:if test="starts-with($currNodeName, $prefix) and (string-length($suffix) >0)">
            <xsl:element name="meta">
                <xsl:attribute name="name">
                    <xsl:value-of select="$normalized-suffix"/>
                </xsl:attribute>
                <xsl:attribute name="ml_handling">unique</xsl:attribute>
                <xsl:choose>
                    <xsl:when test="$asValue">
                        <xsl:element name="value">
                            <xsl:attribute name="lang">eng</xsl:attribute>
                            <xsl:value-of select="."/>
                        </xsl:element>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="."/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:element>
        </xsl:if>
    </xsl:template>


    <xsl:template name="create-ott-tags">
        <xsl:param name="metadatas"/>
        <xsl:variable name="prefix" select="'OTTTAG'"/>
        <!--list here should include a list like this -->
        <!--<OTTTAGActor>A</OTTTAGActor>-->
        <!--<OTTTAGActor>B</OTTTAGActor>-->
        <xsl:variable name="OTTTagsWithAttributes">
            <xsl:for-each select="$metadatas">
                <xsl:variable name="prefix-length" select="string-length($prefix)+1"/>
                <xsl:variable name="currNodeName" select="local-name(.)"/>
                <xsl:variable name="suffix" select="substring($currNodeName,$prefix-length)"/>
                <xsl:variable name="normalized-suffix" select="normalize-space(translate($suffix,'_',' '))"/>
                <xsl:if test="starts-with($currNodeName, $prefix)  and (string-length($suffix) >0)">
                    <xsl:element name="otttag">
                        <xsl:attribute name="name">
                            <xsl:value-of select="$normalized-suffix"/>
                        </xsl:attribute>
                        <xsl:value-of select="."/>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:variable name="tagsWithAttributes" select="exsl:node-set($OTTTagsWithAttributes)/*"/>
        <!--at this point the OTTTagsWithAttributes should be as follow:-->
        <!--<meta name="Actor" value="A"/>-->
        <!--<meta name="Actor" value="B"/>-->
        <xsl:variable name="OTTTagUniques">
            <xsl:for-each select="exsl:node-set($tagsWithAttributes)">
                <xsl:if test="not(./@name = preceding-sibling::otttag/@name)">
                    <xsl:element name="OTTTagUnique">
                        <xsl:value-of select="./@name" />
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:variable name="uniqueTags" select="exsl:node-set($OTTTagUniques)/*"/>
        <!--at this point the OTTTagUniques should be as follow:-->
        <!--<meta name="Actor" value="A"/>-->
        <!-- now construct the actual result -->
        <xsl:element name="metas">
            <xsl:for-each select="exsl:node-set($uniqueTags)">
                <xsl:variable name="uniqueTagName" select="."/>
                <xsl:element name="meta">
                    <xsl:attribute name="name">
                        <xsl:value-of select="$uniqueTagName"/>
                    </xsl:attribute>
                    <xsl:attribute name="ml_handling">unique</xsl:attribute>
                    <xsl:element name="container">
                        <xsl:for-each select="exsl:node-set($tagsWithAttributes)">
                            <xsl:variable name="tagWithAttrName" >
                                <xsl:value-of select="@name" />
                            </xsl:variable>
                            <xsl:if test="$tagWithAttrName=$uniqueTagName">
                                <xsl:element name="value">
                                    <xsl:attribute name="lang">eng</xsl:attribute>
                                    <xsl:value-of select="."/>
                                </xsl:element>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:element>
                </xsl:element>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
            
    <xsl:template match="feed/export/media/structure/metas" >
        <xsl:variable name="metadatas" select="metadata/*"/>
        <xsl:element name="strings">
            <xsl:for-each select="$metadatas">
                <xsl:call-template name="convert-known-meta-element">
                    <xsl:with-param name="prefix" select="'STRING'"/>
                    <xsl:with-param name="asValue" select="true()"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:element>
        <xsl:element name="booleans">
            <xsl:for-each select="$metadatas">
                <xsl:call-template name="convert-known-meta-element">
                    <xsl:with-param name="prefix" select="'BOOL'"/>
                    <xsl:with-param name="asValue" select="false()"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:element>
        <xsl:element name="doubles">
            <xsl:for-each select="$metadatas">
                <xsl:call-template name="convert-known-meta-element">
                    <xsl:with-param name="prefix" select="'NUM'"/>
                    <xsl:with-param name="asValue" select="false()"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:element>
        <xsl:call-template name="create-ott-tags">
            <xsl:with-param name="metadatas" select="$metadatas"/>
        </xsl:call-template>
    </xsl:template>


    <xsl:variable name="mediaType" select="feed/export/media/structure/metas/metadata/MediaType"/>
    <xsl:variable name="geoBlockRule" select="feed/export/media/structure/metas/metadata/GEOBlockRule"/>
    <xsl:variable name="watchPermissionRule" select="feed/export/media/structure/metas/metadata/WatchPermissionRule"/>

    <xsl:template match="feed/export/media/basic">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
            <xsl:element name="media_type">
                <xsl:value-of select="$mediaType"/>
            </xsl:element>
            <xsl:element name="rules">
                <xsl:element name="geo_block_rule">
                    <xsl:value-of select="$geoBlockRule"/>
                </xsl:element>
                <xsl:element name="watch_per_rule">
                    <xsl:value-of select="$watchPermissionRule"/>
                </xsl:element>
            </xsl:element>
        </xsl:copy>
    </xsl:template>

</xsl:stylesheet>