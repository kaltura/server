
Overview
=========
The Kaltura CLI client is a bundle of command line utilities that can be used to interface with the 
Kaltura API. The client is intended mostly for experimentation / small tasks, not for full-fledged
applications.

The following utilities are included in the package:
1. kalcli - responsible for issuing Kaltura API calls, it builds the request URL and parses the 
	response to a format that can be easily processed by command line utilities such as grep / awk.
	The client library contains an additional script (kalcliAutoComplete) that provides bash-autocompletion 
	functionality to the kalcli utility, for example:
```
	kalcli med[TAB]
	kalcli media l[TAB]
	kalcli media list f[TAB]
	kalcli media list filter:objectType=KalturaM[TAB]
	kalcli media list filter:objectType=KalturaMediaEntryFilter
	...
```
2. extractKs - parses a Kaltura session (KS) to its different fields.
3. generateKs - generates a KS.
4. renewKs - useful to renew expired Kaltura sessions, generates a KS identical to the input KS with a 1 day expiry.
5. logToCli - Parse an API log entry array of params into a kalcli command
6. genIpHeader - generates a signed HTTP header that can be used to simulate access to the Kaltura API from a different source IP.

*NOTE: when executing without arguments, all utilities display usage information including all available flags.*

Installation instructions
==========================
Linux
------
The CLI libs require PHP CLI from version 5.3.3 and above, with the CURL PHP extension.

Extract the package contents and run the setup.sh script with:
```
$ ./setup.sh $BASEDIR $SERVICE_URL $PARTNER_ID $ADMIN_SECRET
```
Where:

```
$BASEDIR is the prefix in which you wish to install the clientlibs

$SERVICE_URL is the Kaltura API edge point, for instance www.kaltura.com if working against Kaltura's SaaS

$PARTNER_ID is your Kaltura partner ID

$ADMIN_SECRET is your partner ID's admin_secret which can be found by going to:

KMC->Settings->Integration Settings

or by making the following DB query:

mysql> select admin_secret from partner where id=$PARTNER_ID
```
You can then run this sanity test to ensure all is working:
```
$ ./tests/sanity.sh $BASE_DIR $PARTNER_ID
```
Alternatively, you can follow these manual steps:

* Replace the @BASEDIR@ token with the path to kalcliAutoComplete.php in:
```	
	kalcliAliases.sh
	kalcliAutoComplete
	logToCli
```
e.g. if kalcliAutoComplete was copied to /a/b/kalcliAutoComplete @BASEDIR@ should be set to /a/b

If you have root privileges on the machine in question, you can also do the following to enjoy BASH's auto completion features:
* Create a link to kalcliAutoComplete in /etc/bash_completion.d/
	ln -s @BASEDIR@/kalcliAutoComplete /etc/bash_completion.d/kalcliAutoComplete
* Register the auto completion: 
	source /etc/bash_completion.d/kalcliAutoComplete
* Create a link to kalcliAliases.sh in /etc/profile.d/
	ln -s @BASEDIR@/kalcliAliases.sh /etc/profile.d/kalcliAliases.sh
* Enable the aliases
	source /etc/profile.d/kalcliAliases.sh

*NOTE: If you do not have root privileges, you can still source kalcliAliases.sh in your user's ~/bashrc*

* Copy config/config.template.ini to config/config.ini and fill out the parameters:
	- Secret repositories - required for the extractKs / generateKs / renewKs utilities.
		Two types of repositories can be configured:
		- Preset repositories - contain a fixed list of (partner id, admin secret) pairs
		- Database repositories - contain the connection details for a Kaltura server database, that can be used to pull the secrets of partner accounts.

		*NOTE: The second option is only possible if you are hosting your own Kaltura ENV. For SaaS, only the first one is viable.*
	- IP address salt - required for the genIpHeader utility.
		The salt has to match the parameter 'remote_addr_header_salt' that is configured in configuration/local.ini on the Kaltura server.
		
		*NOTE: this is only relevant when hosting your own Kaltura ENV, otherwise, leave empty.*
	- API Host - The default is www.kaltura.com if not defined
		May be uncommented and modified in order to globally point to a different api host
		The -u parameter may be used to override the api host via command line
	- Log Dir - The log directory that contains kaltura_api_v3.log (typically /var/log or /opt/kaltura/log)
		
		This is relevant to the --log flag, that makes kalcli print the API log instead of the API output. 
		
		*Note: this works only when kalcli is executed against a Kaltura server running on the same machine*

Windows
--------
To use kalcli on Windows you'll need to install:

- [Cygwin] (https://cygwin.com/install.html)- make sure to include the bash-completion package (not included by default)
- [Xampp] (https://www.apachefriends.org/index.html) or any other platform that provides PHP CLI 5.3 and above with the CURL extension.


Installation is the same as for Linux, but note the following:

- Perform the steps from the Cygwin bash
- The meaning of BASEDIR in step 2 is different than steps 3 & 5 - in step 2 you need to use a
	path relative to the drive root while in steps 3 & 5 you need to use a path relative to Cygwin root. 
	For example:
	- If you install kalcli in C:\Cygwin\cli, you'll need to use /cygwin/cli in step 2, and use /cli in 3 & 5.
	- If you install kalcli in C:\cli, you'll need to use /cli in step 2, and use /cygdrive/c/cli in 3 & 5.

Examples
=========
1. Getting the ids of 30 entries:
``` 
$ genks $PARTNER_ID | kalcli media list | awk '$1 == "id"'
```
Sample output:
```
    id      0_bvnwwuiw
    id      0_dmuzyn77
    id      0_ii3bbdq9
    id      0_nv9zbm2b
    id      0_nz4oy27t
```

2. Diffing access control profiles:
```
$ (genks $PARTNER_ID | kalcli accesscontrol get id=7003 > /tmp/a1) ; (genks $PARTNER_ID | kalcli accesscontrol get id=8005 > /tmp/a2) ; diff /tmp/a1 /tmp/a2
```

3. Getting the number of distinct entries in a playlist:
```
$ genks $PARTNER_ID | kalcli playlist execute id=1_1a2b3c | grep -P 'id\t' | sort | uniq | wc -l
```
Sample output:
```
2
```

4. Using a precreated session:
```
$ kalcli -x media list ks=MDQ2ZThjOTI0MTJmZGIxYTVlMWVhNDJlZDZhNDAyMDkyMWJhNzE0OXw0Mzc0ODE7NDM3NDgxOzEzNjI0OTI3Njc7MDsxMzYyNDA2MzY3Ljc3NzM7MDt3aWRnZXQ6MSx2aWV3Oio7Ow==
```
Sample output:
```
KalturaMediaListResponse
        objects array
                0       KalturaMediaEntry
                        mediaType       1
                        conversionQuality       6
                        sourceType      1
                        dataUrl http://54.160.105.103:80/p/101/sp/10100/playManifest/entryId/0_9b791llw/format/url/protocol/http
                        flavorParamsIds 0,2,3,4,5,6
                        plays   0
                        views   0
                        duration        33
                        msDuration      33097
                        id      0_9b791llw
                        name    Sample Big Buck Bunny Trailer (HD)
                        description     Sample Big Buck Bunny Trailer (HD)
                        partnerId       101
                        userId  template
                        creatorId       template
                        tags    hd content, video, bunny
                        categories      video,hd content
                        categoriesIds   7,8
                        status  2
                        moderationStatus        6
                        moderationCount 0
                        type    1
                        createdAt       1434218441      (2015-06-13 21:00:41)
                        updatedAt       1434218444      (2015-06-13 21:00:44)
                        rank    0
                        totalRank       0
                        votes   0
                        downloadUrl     http://54.160.105.103/p/101/sp/10100/raw/entry_id/0_9b791llw/version/0
                        searchText      _PAR_ONLY_ _101_ _MEDIA_TYPE_1|  Sample Big Buck Bunny Trailer (HD) hd content, video, bunny Sample Big Buck Bunny Trailer (HD) 
                        licenseType     -1
                        version 0
                        thumbnailUrl    http://54.160.105.103/p/101/sp/10100/thumbnail/entry_id/0_9b791llw/version/100000/acv/92
                        accessControlId 2
                        replacementStatus       0
                        partnerSortValue        0
                        conversionProfileId     6
                        rootEntryId     0_9b791llw
                        operationAttributes     array
                        entitledUsersEdit
                        entitledUsersPublish
                1       KalturaMediaEntry
                        mediaType       1
                        conversionQuality       6
                        sourceType      1
                        dataUrl http://54.160.105.103:80/p/101/sp/10100/playManifest/entryId/0_i2xs97r8/format/url/protocol/http
                        flavorParamsIds 0,3,4
                        plays   0
                        views   0
                        duration        30
                        msDuration      29853
                        id      0_i2xs97r8
                        name    Normal web quality video (400kbps)
                        description     Normal web quality video
                        partnerId       101
                        userId  template
                        creatorId       template
                        tags    fish
                        categories      fish
                        categoriesIds   5
                        status  2
                        moderationStatus        6
                        moderationCount 0
                        type    1
                        createdAt       1434218437      (2015-06-13 21:00:37)
                        updatedAt       1434218439      (2015-06-13 21:00:39)
                        rank    0
                        totalRank       0
                        votes   0
                        downloadUrl     http://54.160.105.103/p/101/sp/10100/raw/entry_id/0_i2xs97r8/version/0
                        searchText      _PAR_ONLY_ _101_ _MEDIA_TYPE_1|  Normal web quality video (400kbps) fish Normal web quality video 
                        licenseType     -1
                        version 0
                        thumbnailUrl    http://54.160.105.103/p/101/sp/10100/thumbnail/entry_id/0_i2xs97r8/version/100000/acv/62
                        accessControlId 2
                        replacementStatus       0
                        partnerSortValue        0
                        conversionProfileId     6
                        rootEntryId     0_i2xs97r8
                        operationAttributes     array
                        entitledUsersEdit
                        entitledUsersPublish
```

5. Parsing an array of params from the API log:
```
$ ./logToCli 
Paste the log portion here

[Array
(
    [service] => batch
    [action] => cleanExclusiveJobs
    [format] => 3
    [ignoreNull] => 1
    [clientTag] => batch: ip-10-154-241-19 KAsyncDbCleanup index: 0 sessionId: 1978407734
    [apiVersion] => 3.2.0
    [partnerId] => -1
    [ks] => MzZjYjRlOWM5ODcxNGZhMzY3MTNmODU2NDYyMWE1MmE0ZjViNGM5Y3wtMTs7MTQzNjgwNzg5MDsyOzE0MzQyMTU4OTAuMjQzO2JhdGNoVXNlcjtkaXNhYmxlZW5
0aXRsZW1lbnQ7LTE7
    [kalsig] => 2ec6af9816b5172dbe0c36c6c30a5ac8
)
]

```

Sample output:
```
Command lines:
kalcli -x batch cleanExclusiveJobs apiVersion=3.2.0 'clientTag=batch: ip-10-154-241-19 KAsyncDbCleanup index: 0 sessionId: 1978407734' format=3 ignoreNull=1 kalsig=2ec6af9816b5172dbe0c36c6c30a5ac8 'ks=MzZjYjRlOWM5ODcxNGZhMzY3MTNmODU2NDYyMWE1MmE0ZjViNGM5Y3wtMTs7MTQzNjgwNzg5MDsyOzE0MzQyMTU4OTAuMjQzO2JhdGNoVXNlcjtkaXNhYmxlZW5
0aXRsZW1lbnQ7LTE7' partnerId=-1
```

Parsing a KS:
```
$ extks MDQ2ZThjOTI0MTJmZGIxYTVlMWVhNDJlZDZhNDAyMDkyMWJhNzE0OXw0Mzc0ODE7NDM3NDgxOzEzNjI0OTI3Njc7MDsxMzYyNDA2MzY3Ljc3NzM7MDt3aWRnZXQ6MSx2aWV3Oio7Ow==
```
Sample output:
```
extks ZTc4ZmUxN2I5N2I5OWVjOGUwYWEyNzVlMzRhNjVkZWJiM2I1MDgxM3wxMDE7MTAxOzE0MzQzMDUxMDk7MjsxNDM0MjE4NzA5LjQzODc7YWRtaW47ZGlzYWJsZWVudGl0bGVtZW50Ozs=
Sig                 e78fe17b97b99ec8e0aa275e34a65debb3b50813
Fields              101;101;1434305109;2;1434218709.4387;admin;disableentitlement;;
---
partner_id          101
partner_pattern     101
valid_until         1434305109
type                2
rand                1434218709.4387
user                admin
privileges          disableentitlement
master_partner_id   
additional_data 

```

7. Using a serve action:
```
$ genks $PARTNER_ID | kalcli -R document_documents serve entryId=0_abcdef > /path/to/output/doc
```

8. Nesting requests:
```
$ kalcli -x session start partnerId=$PARTNER_ID secret=abcdef type=2 | awk '{print "ks "$1}' | kalcli media list
```

9. Uploading files:
```
$ KS=`genks -b $PARTNER_ID`

# gen token
$ TOKEN=`kalcli -x uploadtoken add uploadToken:objectType=KalturaUploadToken uploadToken:fileName=$TEST_FLV  ks=$KS|awk '$1 == "id" {print $2}'`

# upload token
$ kalcli -x uploadtoken upload fileData=@$TEST_FLV uploadTokenId=$TOKEN ks=$KS

# upload entry using $TOKEN
$ ENTRY_ID=`kalcli -x baseentry addFromUploadedFile uploadTokenId=$TOKEN partnerId=$PARTNER_ID ks=$KS entry:objectType=KalturaBaseEntry |awk '$1 == "id" {print $2}'`

```
Sample output:
```
KalturaUploadToken
        id      0_55909e3ed8b7c32d4bfd622884c5ae49
        partnerId       101
        userId  admin
        status  2
        fileName        video.mp4
        uploadedFileSize        5296812
        createdAt       1434218959      (2015-06-13 21:09:19)
        updatedAt       1434218962      (2015-06-13 21:09:22)
```

10. Sending the contents of a file on a string parameter:
```
$ genks $PARTNER_ID | kalcli caption_captionasset setContent id=0_abcd56 contentResource:objectType=KalturaStringResource contentResource:content=@@/tmp/caption.srt
```

11. Executing an API with a different source IP address:
```
$ ip=`genipheader 9.8.7.6` ; genks $PARTNER_ID | kalcli -H$ip baseentry getContextData entryId=0_abcd56
```
