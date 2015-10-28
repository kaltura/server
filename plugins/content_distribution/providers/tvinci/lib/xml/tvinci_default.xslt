<!-- This xslt is used to take a feed representing kaltura entry (mrss - xml structure) and convert it to a OTT fitting feed -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:exsl="http://exslt.org/common"
                extension-element-prefixes="exsl"
                xmlns:php="http://php.net/xsl"
                version="1.0" >

    <xsl:output omit-xml-declaration="no" indent="yes"/>
    <xsl:strip-space elements="*"/>
    <!-- tag constants -->
    <xsl:variable name="ISM_TAG" select="'ism'"/>
    <xsl:variable name="IPAD_TAG" select="'ipadnew'"/>
    <xsl:variable name="IPHONE_TAG" select="'iphonenew'"/>
    <xsl:variable name="MBR_TAG" select="'mbr'"/>
    <xsl:variable name="DASH_TAG" select="'dash'"/>
    <xsl:variable name="WIDEVINE_TAG" select="'widevine'"/>
    <xsl:variable name="WIDEVINE_MBR_TAG" select="'widevine_mbr'"/>
    <!-- constants -->
    <xsl:variable name="CONST_LANG" select="'eng'"/>
    <xsl:variable name="CONST_RATIO" select="'3:4'"/>
    <xsl:variable name="CONST_QUALITY" select="'HIGH'"/>
    <xsl:variable name="CONST_BILLING_TYPE" select="'Tvinci'"/>
    <xsl:variable name="CONST_HANDLING_TYPE" select="'Clip'"/>
    <xsl:variable name="CONST_CDN_NAME" select="'Akamai'"/>
    <xsl:variable name="CONST_ACTION" select="'insert'"/>
    <xsl:variable name="CONST_IS_ACTIVE" select="'true'"/>
    <xsl:variable name="CONST_ERASE" select="'false'"/>
    <xsl:variable name="CONST_END_OF_TIME" select="'5233709533'"/>
    <xsl:variable name="CONST_TRAILER_NAME" select="'Trailer'"/>
    <xsl:variable name="CONST_TVM_DATE_FORMAT" select="'d/m/Y H:i:s'"/>
    <xsl:variable name="CONST_ISM_MANIFEST_SUFFIX" select="'/format/sl/tags/ism/protocol/http/f/a.ism'"/>
    <xsl:variable name="CONST_IPHONENEW_MANIFEST_SUFFIX" select="'/format/applehttp/tags/iphonenew/protocol/http/f/a.m3u8'"/>
    <xsl:variable name="CONST_IPADNEW_MANIFEST_SUFFIX" select="'/format/applehttp/tags/ipadnew/protocol/http/f/a.m3u8'"/>
    <xsl:variable name="CONST_MBR_MANIFEST_SUFFIX" select="'/format/hdnetworkmanifest/tags/mbr/protocol/http/f/a.a4m'"/>
    <xsl:variable name="CONST_DASH_MANIFEST_SUFFIX" select="'/format/mpegdash/tags/dash/protocol/http/f/a.mpd'"/>
    <xsl:variable name="CONST_WIDEVINE_MANIFEST_SUFFIX" select="'/format/url/tags/widevine/protocol/http/f/a.wvm'"/>
    <xsl:variable name="CONST_WIDEVINE_MBR_MANIFEST_SUFFIX" select="'/format/url/tags/widevine_mbr/protocol/http/f/a.wvm'"/>
    <!-- media element -->
    <xsl:variable name="refernceID" select="item/referenceID"/>
    <xsl:variable name="entryID" select="item/entryId"/>
    <!-- basic element -->
    <xsl:variable name="mediaType" select="item/customData/metadata/MediaType"/>
    <xsl:variable name="epgIdentifier" select="''"/>
    <xsl:variable name="title" select="item/title"/>
    <xsl:variable name="description" select="item/description"/>
    <!-- rules -->
    <xsl:variable name="geoBlockRule" select="item/customData/metadata/GEOBlockRule"/>
    <xsl:variable name="watchPermissionRule" select="item/customData/metadata/WatchPermissionRule"/>
    <!--tags as a nodeset -->
    <xsl:variable name="tags">
        <xsl:element name="tag">
            <xsl:value-of select="$MBR_TAG"/>
        </xsl:element>
        <xsl:element name="tag">
            <xsl:value-of select="$ISM_TAG"/>
        </xsl:element>
        <xsl:element name="tag">
            <xsl:value-of select="$IPHONE_TAG"/>
        </xsl:element>
        <xsl:element name="tag">
            <xsl:value-of select="$IPAD_TAG"/>
        </xsl:element>
        <xsl:element name="tag">
            <xsl:value-of select="$DASH_TAG"/>
        </xsl:element>
        <xsl:element name="tag">
            <xsl:value-of select="$WIDEVINE_TAG"/>
        </xsl:element>
        <xsl:element name="tag">
            <xsl:value-of select="$WIDEVINE_MBR_TAG"/>
        </xsl:element>
    </xsl:variable>


    <!-- Parameters for this xslt file -->
    <xsl:param name="playManifestPrefix"/>
    <xsl:param name="distributionProfileId"/>

    <!--ipadnew -->
    <xsl:param name="ipadnewPpvModule" select="''"/>
    <xsl:param name="ipadnewTypeName" select="'iPad Main'"/>
    <!--ism-->
    <xsl:param name="ismPpvModule" select="''"/>
    <xsl:param name="ismTypeName" select="'ism Main'"/>
    <!--iphonenew-->
    <xsl:param name="iphonenewPpvModule" select="''"/>
    <xsl:param name="iphonenewTypeName" select="'iPhone Main'"/>
    <!--mbr-->
    <xsl:param name="mbrPpvModule" select="''"/>
    <xsl:param name="mbrTypeName" select="'flash Main'"/>
    <!--dash-->
    <xsl:param name="dashPpvModule" select="''"/>
    <xsl:param name="dashTypeName" select="'dash Main'"/>
    <!--widevine-->
    <xsl:param name="widevinePpvModule" select="''"/>
    <xsl:param name="widevineTypeName" select="'widevine Main'"/>
    <!--widevine mbr-->
    <xsl:param name="widevineMbrPpvModule" select="''"/>
    <xsl:param name="widevineMbrTypeName" select="'widevine_mbr Main'"/>
    <!--main logic -->
    <xsl:template match="/">
        <xsl:element name="feed">
            <xsl:element name="export">
                <xsl:element name="media">
                    <!--media attributes -->
                    <xsl:attribute name="co_guid">
                        <xsl:value-of select="$refernceID"/>
                    </xsl:attribute>
                    <xsl:attribute name="entry_id">
                        <xsl:value-of select="$entryID"/>
                    </xsl:attribute>
                    <xsl:attribute name="action">
                        <xsl:value-of select="$CONST_ACTION"/>
                    </xsl:attribute>
                    <xsl:attribute name="is_active">
                        <xsl:value-of select="$CONST_IS_ACTIVE"/>
                    </xsl:attribute>
                    <xsl:attribute name="erase">
                        <xsl:value-of select="$CONST_ERASE"/>
                    </xsl:attribute>
                    <!--basic elements -->
                    <xsl:element name="basic">
                        <xsl:call-template name="create-basic-elements"/>
                    </xsl:element>
                    <!--structure elements-->
                    <xsl:element name="structure">
                        <xsl:call-template name="create-inner-structure">
                            <xsl:with-param name="metadatas" select="item/customData/metadata/*"/>
                        </xsl:call-template>
                    </xsl:element>
                    <!--files elements-->
                    <xsl:element name="files">
                        <xsl:variable name="parentContents" select="item/content"/>
                        <xsl:variable name="distributionFlavorParamIds">
                            <xsl:for-each select="item/distribution">
                                <xsl:if test="./@distributionProfileId = $distributionProfileId">
                                    <xsl:call-template name="get-distribution-flavor-param-ids">
                                        <xsl:with-param name="distributionIds" select="./flavorAssetIds/flavorAssetId"/>
                                        <xsl:with-param name="parentContents" select="$parentContents"/>
                                    </xsl:call-template>
                                </xsl:if>
                            </xsl:for-each>
                        </xsl:variable>
                        <xsl:variable name="children" select="item/children/item"/>
                        <xsl:call-template name="create-files-elements">
                            <xsl:with-param name="distributionFlavorParamIds" select="$distributionFlavorParamIds"/>
                            <xsl:with-param name="parentContents" select="$parentContents"/>
                            <xsl:with-param name="children" select="$children"/>
                        </xsl:call-template>
                    </xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <!-- util function -->
    <xsl:template name="create-element-with-value">
        <xsl:param name="elementName"/>
        <xsl:param name="elementValue"/>
        <xsl:element name="{$elementName}">
            <xsl:element name="value">
                <xsl:attribute name="lang">
                    <xsl:value-of select="$CONST_LANG"/>
                </xsl:attribute>
                <xsl:value-of select="$elementValue"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    <!-- create all the 'basic' needed elements -->
    <xsl:template name="create-basic-elements">
        <xsl:element name="media_type">
            <xsl:value-of select="$mediaType"/>
        </xsl:element>
        <xsl:element name="epg_identifier">
            <xsl:value-of select="$epgIdentifier"/>
        </xsl:element>
        <xsl:call-template name="create-element-with-value">
            <xsl:with-param name="elementName" select="'name'"/>
            <xsl:with-param name="elementValue" select="$title"/>
        </xsl:call-template>
        <xsl:call-template name="create-element-with-value">
            <xsl:with-param name="elementName" select="'description'"/>
            <xsl:with-param name="elementValue" select="$description"/>
        </xsl:call-template>
        <xsl:element name="thumb">
            <xsl:attribute name="url">
                <xsl:choose>
                    <xsl:when test="item/thumbnail/@isDefault='true'">
                        <!-- if there is default thumb we'll take it -->
                        <xsl:for-each select="item/thumbnail">
                            <xsl:if test="./@isDefault='true'">
                                <xsl:value-of select="concat(./@url,'/image.jpg')"/>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:for-each select="item/thumbnail">
                            <xsl:sort select="./@fileSize" data-type="number" order="descending"/>
                            <!-- we only care about the largest one -->
                            <xsl:if test="position()=1">
                                <xsl:value-of select="concat(./@url,'/image.jpg')"/>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
        </xsl:element>
        <xsl:element name="pic_ratios">
            <!--<xsl:variable name="thumbnails" select="item/thumbnail"/>-->
            <!--<xsl:for-each select="$thumbnails">-->
            <!--<xsl:variable name="url" select="./@url"/>-->
            <!--<xsl:variable name="height" select="./@height"/>-->
            <!--<xsl:variable name="width" select="./@width"/>-->
            <!--<xsl:element name="ratio">-->
            <!--<xsl:attribute name="thumb">-->
            <!--<xsl:value-of select="$url"/>-->
            <!--</xsl:attribute>-->
            <!--<xsl:attribute name="ratio">-->
            <!--<xsl:value-of select="$CONST_RATIO"/>-->
            <!--</xsl:attribute>-->
            <!--</xsl:element>-->
            <!--</xsl:for-each>-->
        </xsl:element>
        <xsl:element name="rules">
            <xsl:element name="watch_per_rule">
                <xsl:value-of select="$watchPermissionRule"/>
            </xsl:element>
            <xsl:element name="geo_block_rule">
                <xsl:value-of select="$geoBlockRule"/>
            </xsl:element>
        </xsl:element>
        <xsl:element name="dates">
            <xsl:variable name="sunrise" select="item/distribution/sunrise"/>
            <xsl:variable name="sunriseFormatted" select="php:function('date' ,string($CONST_TVM_DATE_FORMAT),string($sunrise))"/>
            <xsl:element name="catalog_start">
                <xsl:value-of select="$sunriseFormatted"/>
            </xsl:element>
            <xsl:element name="start">
                <xsl:value-of select="$sunriseFormatted"/>
            </xsl:element>
            <xsl:variable name="sunset">
                <xsl:choose>
                    <xsl:when test="item/distribution/sunset">
                        <xsl:value-of select="item/distribution/sunset"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="$CONST_END_OF_TIME"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:variable name="sunsetFormatted" select="php:function('date', string($CONST_TVM_DATE_FORMAT), string($sunset))"/>
            <xsl:element name="catalog_end">
                <xsl:value-of select="$sunsetFormatted"/>
            </xsl:element>
            <xsl:element name="end">
                <xsl:value-of select="$sunsetFormatted"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <!--function to handle the known cases such as : BOOL , NUM, STRING -->
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

    <!--the ott tags can have several values and thus require special treatment-->
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
                        <xsl:value-of select="./@name"/>
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
                    <xsl:for-each select="exsl:node-set($tagsWithAttributes)">
                        <xsl:variable name="tagWithAttrName">
                            <xsl:value-of select="@name"/>
                        </xsl:variable>
                        <xsl:if test="$tagWithAttrName=$uniqueTagName">
                            <xsl:element name="container">
                                <xsl:element name="value">
                                    <xsl:attribute name="lang">eng</xsl:attribute>
                                    <xsl:value-of select="."/>
                                </xsl:element>
                            </xsl:element>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:element>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>

    <!--create the 'structure' element tree -->
    <xsl:template name="create-inner-structure">
        <xsl:param name="metadatas"/>
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

    <xsl:template name="create-files-elements">
        <xsl:param name="distributionFlavorParamIds"/>
        <xsl:param name="parentContents"/>
        <xsl:param name="children"/>
        <xsl:variable name="parentDuration" select="item/media/duration"/>
        <!-- First build the parent files -->
        <xsl:for-each select="exsl:node-set($tags)/tag">
            <xsl:variable name="tag" select="."/>
            <xsl:call-template name="create-tag-relevant-files">
                <xsl:with-param name="contents" select="$parentContents"/>
                <xsl:with-param name="duration" select="$parentDuration"/>
                <xsl:with-param name="distributionFlavorParamIds" select="$distributionFlavorParamIds"/>
                <xsl:with-param name="tag" select="$tag"/>
                <xsl:with-param name="relevantEntryId" select="$entryID"/>
            </xsl:call-template>
            <!--now get the children -->
            <xsl:for-each select="exsl:node-set($children)">
                <xsl:variable name="childEntryId" select="./entryId"/>
                <xsl:variable name="childContents" select="./content"/>
                <xsl:variable name="childDuration" select="./media/duration"/>
                <xsl:call-template name="create-tag-relevant-files">
                    <xsl:with-param name="contents" select="$childContents"/>
                    <xsl:with-param name="duration" select="$childDuration"/>
                    <xsl:with-param name="distributionFlavorParamIds" select="$distributionFlavorParamIds"/>
                    <xsl:with-param name="tag" select="$tag"/>
                    <xsl:with-param name="relevantEntryId" select="$childEntryId"/>
                    <xsl:with-param name="isChild" select="true()"/>
                    <xsl:with-param name="childIdx" select="position()"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>

    <!-- logic for file to be included:  -->
    <!-- 1. search for the tag in all the contents given -->
    <!-- 2. filter results that are not included by the distribution profile  -->
    <xsl:template name="create-tag-relevant-files">
        <xsl:param name="tag"/>
        <xsl:param name="relevantEntryId"/>
        <xsl:param name="duration"/>
        <xsl:param name="contents"/>
        <xsl:param name="distributionFlavorParamIds"/>
        <xsl:param name="isChild" select="false()"/>
        <xsl:param name="childIdx" select="0"/>
        <!--find all the content elements that have hte specific tag -->
        <xsl:variable name="tagMatchingContents">
            <xsl:call-template name="get-matching-contents">
                <xsl:with-param name="tag" select="$tag"/>
                <xsl:with-param name="contents" select="$contents"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:if test="not(count(exsl:node-set($tagMatchingContents)/contentItem) = 0)">
            <xsl:variable name="filteredItems">
                <xsl:call-template name="filter-non-distribution-item">
                    <xsl:with-param name="distributionFlavorParamIds" select="$distributionFlavorParamIds"/>
                    <xsl:with-param name="matchingContents" select="$tagMatchingContents"/>
                </xsl:call-template>
            </xsl:variable>
            <xsl:if test="not(count(exsl:node-set($filteredItems)/contentItem) = 0)">
                <xsl:variable name="coGuid">
                    <xsl:for-each select="exsl:node-set($filteredItems)/contentItem">
                        <xsl:variable name="flavorParamId" select="./@flavorParamsId"/>
                        <xsl:value-of select="concat($relevantEntryId, '_' , $flavorParamId)"/>
                        <xsl:if test="not(position() = last())">,</xsl:if>
                    </xsl:for-each>
                </xsl:variable>

                <xsl:variable name="suffix">
                    <xsl:call-template name="suffix-matching-tag">
                        <xsl:with-param name="tag" select="$tag"/>
                    </xsl:call-template>
                </xsl:variable>

                <xsl:variable name="typeName">
                    <xsl:call-template name="type-name-matching-tag">
                        <xsl:with-param name="tag" select="$tag"/>
                        <xsl:with-param name="isChild" select="$isChild"/>
                        <xsl:with-param name="childNumber" select="$childIdx"/>
                    </xsl:call-template>
                </xsl:variable>

                <xsl:variable name="ppvModule">
                    <xsl:call-template name="ppvModule-matching-tag">
                        <xsl:with-param name="tag" select="$tag"/>
                    </xsl:call-template>
                </xsl:variable>

                <xsl:call-template name="create-file-element">
                    <xsl:with-param name="cdnCode" select="concat($playManifestPrefix, $relevantEntryId, $suffix)"/>
                    <xsl:with-param name="coGuid" select="$coGuid"/>
                    <xsl:with-param name="duration" select="$duration"/>
                    <xsl:with-param name="ppvModule" select="$ppvModule"/>
                    <xsl:with-param name="type" select="$typeName"/>
                </xsl:call-template>
            </xsl:if>
        </xsl:if>
    </xsl:template>
    <!-- given a tag and contents return only those contents that include the tag -->
    <!-- construct a nodeset of the structure <contentItem flavorAssetId='@' flavorParamsId='@'/> -->
    <xsl:template name="get-matching-contents">
        <xsl:param name="tag"/>
        <xsl:param name="contents"/>
        <xsl:for-each select="$contents">
            <xsl:variable name="contentTags" select="./tags/tag"/>
            <xsl:variable name="contentFlavorAssetId" select="./@flavorAssetId"/>
            <xsl:variable name="contentFlavorParamsId" select="./@flavorParamsId"/>
            <xsl:for-each select="$contentTags">
                <xsl:variable name="currTag" select="."/>
                <xsl:if test="$currTag = $tag">
                    <xsl:element name="contentItem">
                        <xsl:attribute name="flavorAssetId">
                            <xsl:value-of select="$contentFlavorAssetId"/>
                        </xsl:attribute>
                        <xsl:attribute name="flavorParamsId">
                            <xsl:value-of select="$contentFlavorParamsId"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    <!-- filter given nodeset for only elements that have the relevant flavorParamId-->
    <xsl:template name="filter-non-distribution-item">
        <xsl:param name="distributionFlavorParamIds"/>
        <xsl:param name="matchingContents"/>
        <xsl:for-each select="exsl:node-set($matchingContents)/contentItem">
            <xsl:variable name="itemContent" select="."/>
            <xsl:variable name="itemContentFlavorId" select="./@flavorParamsId"/>
            <xsl:for-each select="exsl:node-set($distributionFlavorParamIds)/paramId">
                <xsl:variable name="distributionFlavorId">
                    <xsl:value-of select="."/>
                </xsl:variable>
                <xsl:if test="$distributionFlavorId = $itemContentFlavorId">
                    <xsl:copy-of select="$itemContent"/>
                </xsl:if>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    <!-- given a nodeset of distribution ids and the real content construct a nodeset to include the flavorParamIds -->
    <xsl:template name="get-distribution-flavor-param-ids">
        <xsl:param name="distributionIds"/>
        <xsl:param name="parentContents"/>
        <xsl:for-each select="exsl:node-set($parentContents)">
            <xsl:variable name="itemContentFlavorAssetId" select="./@flavorAssetId"/>
            <xsl:variable name="itemContentFlavorParamsId" select="./@flavorParamsId"/>
            <xsl:for-each select="$distributionIds">
                <xsl:variable name="distributionFlavorId">
                    <xsl:value-of select="."/>
                </xsl:variable>
                <xsl:if test="$distributionFlavorId = $itemContentFlavorAssetId">
                    <xsl:element name="paramId">
                        <xsl:value-of select="$itemContentFlavorParamsId"/>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    <!-- create the file element -->
    <xsl:template name="create-file-element">
        <xsl:param name="type"/>
        <xsl:param name="ppvModule"/>
        <xsl:param name="coGuid"/>
        <xsl:param name="cdnCode"/>
        <xsl:param name="duration"/>
        <xsl:element name="file">
            <xsl:attribute name="cdn_code">
                <xsl:value-of select="$cdnCode"/>
            </xsl:attribute>
            <xsl:attribute name="type">
                <xsl:value-of select="$type"/>
            </xsl:attribute>
            <xsl:attribute name="PPV_Module">
                <xsl:value-of select="$ppvModule"/>
            </xsl:attribute>
            <xsl:attribute name="co_guid">
                <xsl:value-of select="$coGuid"/>
            </xsl:attribute>
            <xsl:attribute name="assetDuration">
                <xsl:value-of select="$duration"/>
            </xsl:attribute>
            <xsl:attribute name="quality">
                <xsl:value-of select="$CONST_QUALITY"/>
            </xsl:attribute>
            <xsl:attribute name="handling_type">
                <xsl:value-of select="$CONST_HANDLING_TYPE"/>
            </xsl:attribute>
            <xsl:attribute name="cdn_name">
                <xsl:value-of select="$CONST_CDN_NAME"/>
            </xsl:attribute>
            <xsl:attribute name="billing_type">
                <xsl:value-of select="$CONST_BILLING_TYPE"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    <!-- get the matching tag suffix - which is given as argument to this code -->
    <xsl:template name="suffix-matching-tag">
        <xsl:param name="tag"/>
        <xsl:choose>
            <xsl:when test="$tag = $ISM_TAG">
                <xsl:value-of select="$CONST_ISM_MANIFEST_SUFFIX"/>
            </xsl:when>
            <xsl:when test="$tag = $MBR_TAG">
                <xsl:value-of select="$CONST_MBR_MANIFEST_SUFFIX"/>
            </xsl:when>
            <xsl:when test="$tag = $IPHONE_TAG">
                <xsl:value-of select="$CONST_IPHONENEW_MANIFEST_SUFFIX"/>
            </xsl:when>
            <xsl:when test="$tag = $IPAD_TAG">
                <xsl:value-of select="$CONST_IPADNEW_MANIFEST_SUFFIX"/>
            </xsl:when>
            <xsl:when test="$tag = $DASH_TAG">
                <xsl:value-of select="$CONST_DASH_MANIFEST_SUFFIX"/>
            </xsl:when>
            <xsl:when test="$tag = $WIDEVINE_TAG">
                <xsl:value-of select="$CONST_WIDEVINE_MANIFEST_SUFFIX"/>
            </xsl:when>
            <xsl:when test="$tag = $WIDEVINE_MBR_TAG">
                <xsl:value-of select="$CONST_WIDEVINE_MBR_MANIFEST_SUFFIX"/>
            </xsl:when>
        </xsl:choose>
    </xsl:template>
    <!-- get the matching tag ppvModule - which is given as argument to this code -->
    <xsl:template name="ppvModule-matching-tag">
        <xsl:param name="tag"/>
        <xsl:choose>
            <xsl:when test="$tag = $ISM_TAG">
                <xsl:value-of select="$ismPpvModule"/>
            </xsl:when>
            <xsl:when test="$tag = $MBR_TAG">
                <xsl:value-of select="$mbrPpvModule"/>
            </xsl:when>
            <xsl:when test="$tag = $IPHONE_TAG">
                <xsl:value-of select="$iphonenewPpvModule"/>
            </xsl:when>
            <xsl:when test="$tag = $IPAD_TAG">
                <xsl:value-of select="$ipadnewPpvModule"/>
            </xsl:when>
            <xsl:when test="$tag = $DASH_TAG">
                <xsl:value-of select="$dashPpvModule"/>
            </xsl:when>
            <xsl:when test="$tag = $WIDEVINE_TAG">
                <xsl:value-of select="$widevinePpvModule"/>
            </xsl:when>
            <xsl:when test="$tag = $WIDEVINE_MBR_TAG">
                <xsl:value-of select="$widevineMbrPpvModule"/>
            </xsl:when>
        </xsl:choose>
    </xsl:template>
    <!-- get the matching tag file name - which is given as argument to this code -->
    <xsl:template name="type-name-matching-tag">
        <xsl:param name="tag"/>
        <xsl:param name="isChild"/>
        <xsl:param name="childNumber"/>
        <xsl:variable name="configuredName">
            <xsl:choose>
                <xsl:when test="$tag = $ISM_TAG">
                    <xsl:value-of select="$ismTypeName"/>
                </xsl:when>
                <xsl:when test="$tag = $MBR_TAG">
                    <xsl:value-of select="$mbrTypeName"/>
                </xsl:when>
                <xsl:when test="$tag = $IPHONE_TAG">
                    <xsl:value-of select="$iphonenewTypeName"/>
                </xsl:when>
                <xsl:when test="$tag = $IPAD_TAG">
                    <xsl:value-of select="$ipadnewTypeName"/>
                </xsl:when>
                <xsl:when test="$tag = $DASH_TAG">
                    <xsl:value-of select="$dashTypeName"/>
                </xsl:when>
                <xsl:when test="$tag = $WIDEVINE_TAG">
                    <xsl:value-of select="$widevineTypeName"/>
                </xsl:when>
                <xsl:when test="$tag = $WIDEVINE_MBR_TAG">
                    <xsl:value-of select="$widevineMbrTypeName"/>
                </xsl:when>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="nameSuffix">
            <xsl:if test="$isChild">
                <xsl:variable name="trailerSuffix">
                    <xsl:value-of select="concat(' ',$CONST_TRAILER_NAME)"/>
                </xsl:variable>
                <xsl:variable name="trailerIdxSuffix">
                    <xsl:if test="(($childNumber > 1))">
                        <xsl:value-of select="concat(' ',$childNumber)"/>
                    </xsl:if>
                </xsl:variable>
                <xsl:value-of select="concat($trailerSuffix, $trailerIdxSuffix)"/>
            </xsl:if>
        </xsl:variable>
        <xsl:value-of select="concat($configuredName, $nameSuffix)"/>
    </xsl:template>

</xsl:stylesheet>