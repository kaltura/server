<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output omit-xml-declaration="no" indent="yes" />

    <xsl:strip-space elements="*"/>

    <xsl:template match="node()|@*">
        <xsl:copy>
            <xsl:apply-templates select="node()|@*"/>
        </xsl:copy>
    </xsl:template>

    <xsl:template name="convert-known-meta-element">
        <xsl:param name="prefix"/>
        <xsl:variable name="prefix-length" select="string-length($prefix)+1"/>
        <xsl:variable name="currNodeName" select="local-name(.)"/>
        <xsl:variable name="suffix" select="substring($currNodeName,$prefix-length)"/>
        <xsl:if test="starts-with($currNodeName, $prefix)">
            <xsl:element name="meta">
                <xsl:attribute name="name">
                    <xsl:value-of select="$suffix"/>
                </xsl:attribute>
                <xsl:attribute name="ml_handling">unique</xsl:attribute>
                <xsl:element name="value">
                    <xsl:attribute name="lang">eng</xsl:attribute>
                    <xsl:value-of select="."/>
                </xsl:element>
            </xsl:element>
        </xsl:if>
    </xsl:template>

    <!--<xsl:template name="group-ott-tags">-->
        <!--<xsl:for-each-group select="metas/meta" group-by="@name">-->
            <!--<xsl:element name="meta">-->
                <!--<xsl:attribute name="name">-->
                    <!--<xsl:value-of select="current-grouping-key()"/>-->
                <!--</xsl:attribute>-->
                <!--<xsl:attribute name="ml_handling">unique</xsl:attribute>-->
                <!--<xsl:element name="container">-->
                    <!--<xsl:for-each select="current-group()">-->
                        <!--<xsl:element name="value">-->
                            <!--<xsl:attribute name="lang">eng</xsl:attribute>-->
                            <!--<xsl:value-of select="."/>-->
                        <!--</xsl:element>-->
                    <!--</xsl:for-each>-->
                <!--</xsl:element>-->
            <!--</xsl:element>-->
        <!--</xsl:for-each-group>-->
    <!--</xsl:template>-->


    <!--<xsl:template name="convert-ott-tags">-->
        <!--<xsl:param name="otttags"/>-->
        <!--&lt;!&ndash;<xsl:value-of select="$otttags"/>&ndash;&gt;-->
        <!--<xsl:for-each select="$otttags">-->
            <!--<xsl:call-template name="group-ott-tags"/>-->
        <!--</xsl:for-each>-->
    <!--</xsl:template>-->



    <xsl:template match="feed/export/media/structure/metas" >
        <xsl:variable name="metadatas" select="metadata/*"/>
        <xsl:element name="strings">
            <xsl:for-each select="$metadatas">
                <xsl:call-template name="convert-known-meta-element">
                    <xsl:with-param name="prefix" select="'STRING'"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:element>
        <xsl:element name="booleans">
            <xsl:for-each select="$metadatas">
                <xsl:call-template name="convert-known-meta-element">
                    <xsl:with-param name="prefix" select="'BOOL'"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:element>
        <xsl:element name="doubles">
            <xsl:for-each select="$metadatas">
                <xsl:call-template name="convert-known-meta-element">
                    <xsl:with-param name="prefix" select="'NUM'"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:element>
        <!--<xsl:variable name="tags">-->
            <!--<xsl:element name="metas" >-->
                <!--<xsl:for-each select="$metadatas">-->
                    <!--<xsl:call-template name="convert-known-meta-element">-->
                        <!--<xsl:with-param name="prefix" select="'OTTTAG'"/>-->
                    <!--</xsl:call-template>-->
                <!--</xsl:for-each>-->
            <!--</xsl:element>-->
        <!--</xsl:variable>-->
        <!--<xsl:element name="metas" >-->
            <!--<xsl:call-template name="convert-ott-tags">-->
                <!--<xsl:with-param name="otttags" select="$tags"/>-->
            <!--</xsl:call-template>-->
        <!--</xsl:element>-->
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