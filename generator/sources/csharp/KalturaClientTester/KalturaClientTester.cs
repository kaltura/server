// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
﻿using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Kaltura
{
    class KalturaClientTester
    {
        private const int PARTNER_ID = 000; //enter your partner id
        private const string SECRET = "1234567890qwertyuioplkjhgfdsa"; //enter your user secret
        private const string ADMIN_SECRET = "1234567890qwertyuioplkjhgfdsa"; //enter your admin secret
        private const string SERVICE_URL = "http://www.kaltura.com";

        static void Main(string[] args)
        {
            Console.WriteLine("Starting C# Kaltura API Client Library");

            try
            {
                SampleReplaceVideoFlavorAndAddCaption();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed SampleReplaceVideoFlavorAndAddCaption: " + e1.Message);
            }

            try
            {
                SampleMetadataOperations();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed SampleMetadataOperations: " + e1.Message);
            }

            try
            {
                MultiRequestExample();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed MultiRequestExample: " + e1.Message);
            }

            try
            {
                AdvancedMultiRequestExample();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed AdvancedMultiRequestExample: " + e1.Message);
            }

            Console.WriteLine("Client testing is over, press any key to exit");	    
            Console.ReadKey();
        }

        static KalturaConfiguration GetConfig()
        {
            KalturaConfiguration config = new KalturaConfiguration(PARTNER_ID);
            config.ServiceUrl = SERVICE_URL;
            return config;
        }
        
        // This will guide you through uploading a video, getting a specific transcoding flavor, replacing a flavor, and uploading a caption file.
        static void SampleReplaceVideoFlavorAndAddCaption()
        {
            // Upload a file
            Console.WriteLine("1. Upload a video file");
            FileStream fileStream = new FileStream("DemoVideo.flv", FileMode.Open, FileAccess.Read);
            KalturaClient client = new KalturaClient(GetConfig());
            string ks = client.SessionService.Start(ADMIN_SECRET, "MY_USER_ID", KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = ks;
            KalturaUploadToken uploadToken = client.UploadTokenService.Add();
            client.UploadTokenService.Upload(uploadToken.Id, fileStream);
            KalturaUploadedFileTokenResource mediaResource = new KalturaUploadedFileTokenResource();
            mediaResource.Token = uploadToken.Id;
            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using C#.Net Client To Test Flavor Replace";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;
            mediaEntry = client.MediaService.Add(mediaEntry);
            mediaEntry = client.MediaService.AddContent(mediaEntry.Id, mediaResource);

            //verify that the account we're testing has the new iPad flavor enabled
            Boolean doFlavorReplaceTest = false;
            KalturaConversionProfile defaultConversionProfile = client.ConversionProfileService.GetDefault();
            Console.WriteLine("Default conversion include the following flavors (ids): " + defaultConversionProfile.FlavorParamsIds);
            if (defaultConversionProfile.FlavorParamsIds.Contains("301971") == true)
            {
                KalturaConversionProfileAssetParamsFilter filter = new KalturaConversionProfileAssetParamsFilter();
                filter.AssetParamsIdEqual = 301971;
                filter.SystemNameEqual = "iPad";
                KalturaConversionProfileAssetParamsListResponse listConversionProfiles = client.ConversionProfileAssetParamsService.List(filter);
                if (listConversionProfiles.TotalCount > 0)
                {
                    KalturaConversionProfileAssetParams conProfile = listConversionProfiles.Objects[0];
                    KalturaFlavorParams flavorParams = client.FlavorParamsService.Get(conProfile.AssetParamsId);
                    if (flavorParams.Tags.Contains("ipadnew"))
                    {
                        Console.WriteLine("** Default conversion includes the new iPad flavor");
                        doFlavorReplaceTest = true;
                    }
                }
            }
            else
            {
                Console.WriteLine("!! Default conversion does NOT include the new iPad flavor");
                Console.WriteLine("!! Skipping the iPad flavor replace test, make sure account has newiPad flavor enabled.");
            }

            if (doFlavorReplaceTest == true)
            {
                //Detect and Download the iPad flavor -
                Boolean statusB = false;
                KalturaFlavorAssetWithParams iPadFlavor = null;
                while (statusB == false)
                {
                    Console.WriteLine("2. Waiting for the iPad flavor");
                    System.Threading.Thread.Sleep(15000);
                    IList<KalturaFlavorAssetWithParams> flavors = client.FlavorAssetService.GetFlavorAssetsWithParams(mediaEntry.Id);
                    foreach (KalturaFlavorAssetWithParams flavor in flavors)
                    {
                        if (flavor.FlavorParams.SystemName == "iPad" && flavor.FlavorParams.Tags.Contains("ipadnew"))
                        {
                            if (flavor.FlavorAsset == null) continue;
                            iPadFlavor = flavor;
                            statusB = flavor.FlavorAsset.Status == KalturaFlavorAssetStatus.READY;
                            if (flavor.FlavorAsset.Status == KalturaFlavorAssetStatus.NOT_APPLICABLE)
                            {
                                //in case the Kaltura Transcoding Decision Layer decided not to convert to this flavor, let's force it.
                                client.FlavorAssetService.Convert(mediaEntry.Id, iPadFlavor.FlavorParams.Id);
                            }
                            Console.WriteLine("3. Found the iPad flavor (" + flavor.FlavorParams.Id + "), Status: " + (statusB ? "Ready to rock!" : "Not ready yet"));
                        }
                    }
                }
                //this is the download URL for the actual Video file of the iPad flavor
                string iPadFlavorUrl = client.FlavorAssetService.GetDownloadUrl(iPadFlavor.FlavorAsset.Id);
                Console.WriteLine("4. iPad Flavor URL is: " + iPadFlavorUrl);

                //now let's replace the flavor with our video file (e.g. after processing the file outside of Kaltura)
                FileStream fileStreamiPad = new FileStream("DemoVideoiPad.mp4", FileMode.Open, FileAccess.Read);
                uploadToken = client.UploadTokenService.Add();
                client.UploadTokenService.Upload(uploadToken.Id, fileStreamiPad);
                mediaResource = new KalturaUploadedFileTokenResource();
                mediaResource.Token = uploadToken.Id;
                KalturaFlavorAsset newiPadFlavor = client.FlavorAssetService.SetContent(iPadFlavor.FlavorAsset.Id, mediaResource);
                Console.WriteLine("5. iPad Flavor was replaced! id: " + newiPadFlavor.Id);
            }

            //now let's upload a new caption file to this entry
            FileStream fileStreamCaption = new FileStream("DemoCaptions.srt", FileMode.Open, FileAccess.Read);
            uploadToken = client.UploadTokenService.Add();
            client.UploadTokenService.Upload(uploadToken.Id, fileStreamCaption);
            KalturaCaptionAsset captionAsset = new KalturaCaptionAsset();
            captionAsset.Label = "Test C# Uploaded Caption";
            captionAsset.Language = KalturaLanguage.EN;
            captionAsset.Format = KalturaCaptionType.SRT;
            captionAsset.FileExt = "srt";
            captionAsset = client.CaptionAssetService.Add(mediaEntry.Id, captionAsset);
            Console.WriteLine("6. Added a new caption asset. Id: " + captionAsset.Id);
            KalturaUploadedFileTokenResource captionResource = new KalturaUploadedFileTokenResource();
            captionResource.Token = uploadToken.Id;
            captionAsset = client.CaptionAssetService.SetContent(captionAsset.Id, captionResource);
            Console.WriteLine("7. Uploaded a new caption file and attached to caption asset id: " + captionAsset.Id);
            string captionUrl = client.CaptionAssetService.GetUrl(captionAsset.Id);
            Console.WriteLine("7. Newly created Caption Asset URL is: " + captionUrl);
        }

        static void SampleMetadataOperations()
        {
            // The metadata field we'll add/update
            string metaDataFieldName = "SubtitleFormat";
            string fieldValue = "VobSub";

            // The Schema file for the field
            // Currently, you must build the xsd yourself. There is no utility provided.
            string xsdFile = "MetadataSchema.xsd";

            KalturaClient client = new KalturaClient(GetConfig());

            // start new session (client session is enough when we do operations in a users scope)
            string ks = client.SessionService.Start(ADMIN_SECRET, "MY_USER_ID", KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = ks;

            // Setup a pager and search to use
            KalturaFilterPager pager = new KalturaFilterPager();
            KalturaMediaEntryFilter search = new KalturaMediaEntryFilter();
            search.OrderBy = KalturaMediaEntryOrderBy.CREATED_AT_ASC;
            search.MediaTypeEqual = KalturaMediaType.VIDEO;  // Video only
            pager.PageSize = 10;
            pager.PageIndex = 1;

            Console.WriteLine("List videos, get the first one...");

            // Get 10 video entries, but we'll just use the first one returned
            IList<KalturaMediaEntry> entries = client.MediaService.List(search, pager).Objects;
            // Check if there are any custom fields defined in the KMC (Settings -> Custom Data)
            // for the first item returned by the previous listaction
            KalturaMetadataProfileFilter filter = new KalturaMetadataProfileFilter();
            IList<KalturaMetadataProfile> metadata = client.MetadataProfileService.List(filter, pager).Objects;
            int profileId = 0;
            string name = "";
            string id = "";
            
            if (metadata.Count > 0)
            {
                profileId = metadata[0].Id;
                name = entries[0].Name;
                id = entries[0].Id;
                if (metadata[0].Xsd != null)
                {
                    Console.WriteLine("1. There are custom fields for video: " + name + ", entryid: " + id);
                }
                else
                {
                    Console.WriteLine("1. There are no custom fields for video: " + name + ", entryid: " + id);
                }
            }
            else
            {
                Console.WriteLine("1. This publisher account doesn't have any custom metadata profiles enabled.");
                Console.WriteLine("Existing the metadata test (enable customer metadata in Admin Console and create a profile in KMC first).");
            }
            
            // Add a custom data entry in the KMC  (Settings -> Custom Data)
            KalturaMetadataProfile profile = new KalturaMetadataProfile();
            profile.MetadataObjectType = KalturaMetadataObjectType.ENTRY;
            string viewsData = "";

            StreamReader fileStream = File.OpenText(xsdFile);
            string xsd = fileStream.ReadToEnd();
            KalturaMetadataProfile metadataResult = client.MetadataProfileService.Update(profileId, profile, xsd, viewsData);

            if (metadataResult.Xsd != null)
            {
	            Console.WriteLine("2. Successfully created the custom data field " + metaDataFieldName + ".");
            } else {
	            Console.WriteLine("2. Failed to create the custom data field.");
            }

            // Add the custom metadata value to the first video
            KalturaMetadataFilter filter2 = new KalturaMetadataFilter();
            filter2.ObjectIdEqual = entries[0].Id;
            string xmlData = "<metadata><SubtitleFormat>" + fieldValue + "</SubtitleFormat></metadata>";
            KalturaMetadata metadata2 = client.MetadataService.Add(profileId, profile.MetadataObjectType, entries[0].Id, xmlData);

            if (metadata2.Xml != null) {
	            Console.WriteLine("3. Successfully added the custom data field for video: "+name+", entryid: "+id);
	            string xmlStr = metadata2.Xml;
	            Console.WriteLine("XML used: " + xmlStr);
            } else {
	            Console.WriteLine("3. Failed to add the custom data field.");
            }

            // Now lets change the value (update) of the custom field
            // Get the metadata for the video
            KalturaMetadataFilter filter3 = new KalturaMetadataFilter();
            filter3.ObjectIdEqual = entries[0].Id;
            IList<KalturaMetadata> metadataList = client.MetadataService.List(filter3).Objects;
            if (metadataList[0].Xml != null) {
	            Console.WriteLine("4. Current metadata for video: " + name + ", entryid: " + id);
	            string xmlquoted = metadataList[0].Xml;
	            Console.WriteLine("XML: " + xmlquoted);
	            string xml = metadataList[0].Xml;
	            // Make sure we find the old value in the current metadata
	            int pos = xml.IndexOf("<" + metaDataFieldName + ">" + fieldValue + "</" + metaDataFieldName + ">");
	            if (pos == -1) {
		            Console.WriteLine("4. Failed to find metadata STRING for video: " + name + ", entryid: " + id);
	            } else {
                    System.Text.RegularExpressions.Regex pattern = new System.Text.RegularExpressions.Regex ("@<" + metaDataFieldName + ">(.+)</" + metaDataFieldName + ">@");
                    xml = pattern.Replace(xml, "<" + metaDataFieldName + ">Ogg Writ</" + metaDataFieldName + ">");
                    KalturaMetadata rc = client.MetadataService.Update(metadataList[0].Id, xml);
		            Console.WriteLine("5. Updated metadata for video: " + name + ", entryid: " + id);
		            xmlquoted = rc.Xml;
		            Console.WriteLine("XML: " + xmlquoted);
	            }
            } else {
	            Console.WriteLine("4. Failed to find metadata for video: " + name + ", entryid: " + id);
            }
        }

        // this method is deprecated and should be avoided. 
        // see above SampleReplaceVideoFlavorAndAddCaption for the current method of uploading media.
        // new method should use the Add method along with specific appropriate Resource object and Upload Token.
        static KalturaMediaEntry StartSessionAndUploadMedia(FileStream fileStream)
        {
            KalturaClient client = new KalturaClient(GetConfig());

            // start new session (client session is enough when we do operations in a users scope)
            string ks = client.SessionService.Start(SECRET, "MY_USER_ID", KalturaSessionType.USER, PARTNER_ID, 86400, "");
            client.KS = ks;

            // upload the media
            string uploadTokenId = client.MediaService.Upload(fileStream); // synchronous proccess
            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using .Net Client";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;

            // add the media using the upload token
            mediaEntry = client.MediaService.AddFromUploadedFile(mediaEntry, uploadTokenId);

            Console.WriteLine("New media was created with the following id: " + mediaEntry.Id);

            return mediaEntry;
        }

        // this method is deprecated and should be avoided. 
        // see above SampleReplaceVideoFlavorAndAddCaption for the current method of uploading media.
        // new method should use the Add method along with specific appropriate Resource object.
        static void StartSessionAndUploadMedia(Uri url)
        {
            KalturaClient client = new KalturaClient(GetConfig());

            // start new session (client session is enough when we do operations in a users scope)
            string ks = client.SessionService.Start(SECRET, "MY_USER_ID", KalturaSessionType.USER, PARTNER_ID, 86400, "");
            client.KS = ks;

            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using .Net Client";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;

            // add the media using the upload token
            mediaEntry = client.MediaService.AddFromUrl(mediaEntry, url.ToString());

            Console.WriteLine("New media was created with the following id: " + mediaEntry.Id);
        }

        /// <summary>
        /// Simple multi request example showing how to start session and list media in a single HTTP request
        /// </summary>
        static void MultiRequestExample()
        {
            KalturaClient client = new KalturaClient(GetConfig());

            client.StartMultiRequest();

            client.SessionService.Start(ADMIN_SECRET, "", KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = "{1:result}"; // for the current multi request, the result of the first call will be used as the ks for next calls

            KalturaMediaEntryFilter filter = new KalturaMediaEntryFilter();
            filter.OrderBy = KalturaMediaEntryOrderBy.CREATED_AT_DESC;
            client.MediaService.List(filter, new KalturaFilterPager());

            KalturaMultiResponse response = client.DoMultiRequest();

            // in multi request, when there is an error, an exception is NOT thrown, so we should check manually
            if (response[1].GetType() == typeof(KalturaAPIException))
            {
                Console.WriteLine("Error listing media " + ((KalturaAPIException)response[1]).Message);

                // we can throw the exception if we want
                //throw (KalturaAPIException)response[1]; 
            }
            else
            {
                KalturaMediaListResponse mediaList = (KalturaMediaListResponse)response[1];
                Console.WriteLine("Total media entries: " + mediaList.TotalCount);
                foreach (KalturaMediaEntry mediaEntry in mediaList.Objects)
                {
                    Console.WriteLine("Media Name: " + mediaEntry.Name);
                }
            }
        }

        /// <summary>
        /// Shows how to start session, create a mix, add media, and append it to a mix timeline using multi request
        /// </summary>
        private static void AdvancedMultiRequestExample()
        {
            KalturaClient client = new KalturaClient(GetConfig());

            client.StartMultiRequest();

            // Request 1
            client.SessionService.Start(ADMIN_SECRET, "", KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = "{1:result}"; // for the current multi request, the result of the first call will be used as the ks for next calls

            FileStream fileStream = new FileStream("DemoVideo.flv", FileMode.Open, FileAccess.Read);

            // Request 2
            KalturaUploadToken uploadToken = client.UploadTokenService.Add();
            
            // Request 3
            uploadToken = client.UploadTokenService.Upload("{2:result}", fileStream);
            
            // Request 4
            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using C#.Net Client To Test Flavor Replace";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;
            mediaEntry = client.MediaService.Add(mediaEntry);

            // Request 5
            KalturaUploadedFileTokenResource mediaResource = new KalturaUploadedFileTokenResource();
            mediaResource.Token = "{2:result:id}";
            mediaEntry = client.MediaService.AddContent("{4:result}", mediaResource);
            
            // map paramters from responses to requests according to response calling order and names to request calling order and C# method parameter name
            client.MapMultiRequestParam(2, ":id", 3, "uploadTokenId");
            client.MapMultiRequestParam(4, ":id", 5, "entryId");

            KalturaMultiResponse response = client.DoMultiRequest();

            foreach (object obj in response)
            {
                if (obj.GetType() == typeof(KalturaAPIException))
                {
                    Console.WriteLine("Error occurred: " + ((KalturaAPIException)obj).Message);
                }
            }

            // when accessing the response object we will use an index and not the response number (response number - 1)
            if (response[4].GetType() == typeof(KalturaMediaEntry))
            {
                KalturaMediaEntry newMediaEntry = (KalturaMediaEntry)response[4];
                Console.WriteLine("Multirequest newly added entry id: " + newMediaEntry.Id);
            }
        }
    }
}
