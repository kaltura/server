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
using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Kaltura
{
    class KalturaClientTester
    {
	private const int PARTNER_ID = @YOUR_PARTNER_ID@; //enter your partner id
	private const string ADMIN_SECRET = "@YOUR_ADMIN_SECRET@"; //enter your admin secret
	private const string SERVICE_URL = "@SERVICE_URL@";
        private const string USER_ID = "testUser";

        static void Main(string[] args)
        {
            Console.WriteLine("Starting C# Kaltura API Client Library");
            int code = 0;

            try
            {
                SampleReplaceVideoFlavorAndAddCaption();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed SampleReplaceVideoFlavorAndAddCaption: " + e1.Message);
                code = -1;
            }

            try
            {
                SampleMetadataOperations();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed SampleMetadataOperations: " + e1.Message);
                code = -1;
            }

            try
            {
                AdvancedMultiRequestExample();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed AdvancedMultiRequestExample: " + e1.Message);
                code = -1;
            }

            if (code == 0)
            {
                Console.WriteLine("Finished running client library tests");
            }

            Environment.Exit(code);
        }

        static KalturaConfiguration GetConfig()
        {
            KalturaConfiguration config = new KalturaConfiguration(PARTNER_ID);
            config.ServiceUrl = SERVICE_URL;
            return config;
        }

        //this function checks if a given flavor system name exist in the account.
        static int? CheckIfFlavorExist(String name)
        {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID);
			
            //verify that the account we're testing has the new iPad flavor enabled on the default conversion profile
            KalturaConversionProfile defaultProfile = client.ConversionProfileService.GetDefault();
            KalturaConversionProfileAssetParamsFilter  flavorsListFilter = new KalturaConversionProfileAssetParamsFilter();
            flavorsListFilter.SystemNameEqual = name;
			flavorsListFilter.ConversionProfileIdEqual = defaultProfile.Id;
			
            KalturaConversionProfileAssetParamsListResponse list = client.ConversionProfileAssetParamsService.List(flavorsListFilter);
            if (list.TotalCount > 0)
                return list.Objects[0].AssetParamsId;
            else
                return null;
        }
        
        // This will guide you through uploading a video, getting a specific transcoding flavor, replacing a flavor, and uploading a caption file.
        static void SampleReplaceVideoFlavorAndAddCaption()
        {
            // Upload a file
            Console.WriteLine("1. Upload a video file");
            FileStream fileStream = new FileStream("DemoVideo.flv", FileMode.Open, FileAccess.Read);
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            KalturaUploadToken uploadToken = client.UploadTokenService.Add();
            client.UploadTokenService.Upload(uploadToken.Id, fileStream);
            KalturaUploadedFileTokenResource mediaResource = new KalturaUploadedFileTokenResource();
            mediaResource.Token = uploadToken.Id;
            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using C#.Net Client To Test Flavor Replace";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;
            mediaEntry = client.MediaService.Add(mediaEntry);
            mediaEntry = client.MediaService.AddContent(mediaEntry.Id, mediaResource);

            //verify that the account we're testing has the iPad flavor enabled
            int? flavorId = CheckIfFlavorExist("iPad");
            if (flavorId == null)
            {
                Console.WriteLine("!! Default conversion profile does NOT include the new iPad flavor");
                Console.WriteLine("!! Skipping the iPad flavor replace test, make sure account has newiPad flavor enabled.");
            }
            else
            {
                int iPadFlavorId = (int)flavorId; //C# failsafe from nullable int - we cast it to int
                Console.WriteLine("** Default conversion profile includes the new iPad flavor, id is: " + iPadFlavorId);
                
                //Detect the conversion readiness status of the iPad flavor and download the file when ready -
                Boolean statusB = false;
                KalturaFlavorAsset iPadFlavor = null;
                while (statusB == false)
                {
                    Console.WriteLine("2. Waiting for the iPad flavor to be available...");
                    System.Threading.Thread.Sleep(5000);
                    KalturaFlavorAssetFilter flavorAssetsFilter = new KalturaFlavorAssetFilter();
                    flavorAssetsFilter.EntryIdEqual = mediaEntry.Id;
                    KalturaFlavorAssetListResponse flavorAssets = client.FlavorAssetService.List(flavorAssetsFilter);
                    foreach (KalturaFlavorAsset flavor in flavorAssets.Objects)
                    {
                        if (flavor.FlavorParamsId == iPadFlavorId)
                        {
                            iPadFlavor = flavor;
                            statusB = flavor.Status == KalturaFlavorAssetStatus.READY;
                            if (flavor.Status == KalturaFlavorAssetStatus.NOT_APPLICABLE)
                            {
                                //in case the Kaltura Transcoding Decision Layer decided not to convert to this flavor, let's force it.
                                client.FlavorAssetService.Convert(mediaEntry.Id, iPadFlavor.FlavorParamsId);
                            }
                            Console.WriteLine("3. iPad flavor (" + iPadFlavor.FlavorParamsId + "). It's " + (statusB ? "Ready to ROCK!" : "being converted. Waiting..."));
                        }
                    }
                }

                //this is the download URL for the actual Video file of the iPad flavor
                string iPadFlavorUrl = client.FlavorAssetService.GetDownloadUrl(iPadFlavor.Id);
                Console.WriteLine("4. iPad Flavor URL is: " + iPadFlavorUrl);

                //Alternatively, download URL for a given flavor id can also be retrived by creating the playManifest URL -
                string playManifestURL = "http://www.kaltura.com/p/{partnerId}/sp/0/playManifest/entryId/{entryId}/format/url/flavorParamId/{flavorParamId}/ks/{ks}/{fileName}.mp4";
                playManifestURL = playManifestURL.Replace("{partnerId}", PARTNER_ID.ToString());
                playManifestURL = playManifestURL.Replace("{entryId}", mediaEntry.Id);
                playManifestURL = playManifestURL.Replace("{flavorParamId}", iPadFlavor.FlavorParamsId.ToString());
                playManifestURL = playManifestURL.Replace("{ks}", client.KS);
                playManifestURL = playManifestURL.Replace("{fileName}", mediaEntry.Name);
                Console.WriteLine("4. iPad Flavor playManifest URL is: " + playManifestURL);
                
                //now let's replace the flavor with our video file (e.g. after processing the file outside of Kaltura)
                FileStream fileStreamiPad = new FileStream("DemoVideoiPad.mp4", FileMode.Open, FileAccess.Read);
                uploadToken = client.UploadTokenService.Add();
                client.UploadTokenService.Upload(uploadToken.Id, fileStreamiPad);
                mediaResource = new KalturaUploadedFileTokenResource();
                mediaResource.Token = uploadToken.Id;
                KalturaFlavorAsset newiPadFlavor = client.FlavorAssetService.SetContent(iPadFlavor.Id, mediaResource);
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

            // The Schema file for the field
            // Currently, you must build the xsd yourself. There is no utility provided.
            string xsdFile = "MetadataSchema.xsd";
            StreamReader fileStream = File.OpenText(xsdFile);
            string xsd = fileStream.ReadToEnd();

            string fieldValue = "VobSub";
            string xmlData = "<metadata><SubtitleFormat>" + fieldValue + "</SubtitleFormat></metadata>";

            KalturaClient client = new KalturaClient(GetConfig());

            // start new session (client session is enough when we do operations in a users scope)
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID);

            // Setup a pager and search to use
            KalturaMediaEntryFilter mediaEntryFilter = new KalturaMediaEntryFilter();
            mediaEntryFilter.OrderBy = KalturaMediaEntryOrderBy.CREATED_AT_ASC;
            mediaEntryFilter.MediaTypeEqual = KalturaMediaType.VIDEO;

            KalturaFilterPager pager = new KalturaFilterPager();
            pager.PageSize = 1;
            pager.PageIndex = 1;

            KalturaMetadataProfile newMetadataProfile = new KalturaMetadataProfile();
            newMetadataProfile.MetadataObjectType = KalturaMetadataObjectType.ENTRY;
            newMetadataProfile.Name = "Test";

            Console.WriteLine("List videos, get the first one...");
            IList<KalturaMediaEntry> entries = client.MediaService.List(mediaEntryFilter, pager).Objects;
            KalturaMediaEntry entry = entries[0];
            
            KalturaMetadataProfile metadataProfile = client.MetadataProfileService.Add(newMetadataProfile, xsd);
            Console.WriteLine("1. Successfully created the custom metadata profile " + metadataProfile.Name + ".");

            KalturaMetadata metadata = client.MetadataService.Add(metadataProfile.Id, metadataProfile.MetadataObjectType, entry.Id, xmlData);
            Console.WriteLine("2. Successfully added the custom data field for entryid: " + entry.Id);

            KalturaMetadataFilter metadataFilter = new KalturaMetadataFilter();
            metadataFilter.ObjectIdEqual = entry.Id;
            metadataFilter.MetadataProfileIdEqual = metadataProfile.Id;
            IList<KalturaMetadata> metadataList = client.MetadataService.List(metadataFilter).Objects;
            if (metadataList.Count == 0) {
                throw new Exception("Failed to find metadata for entryid: " + entry.Id);
            }
        }

        // this method is deprecated and should be avoided. 
        // see above SampleReplaceVideoFlavorAndAddCaption for the current method of uploading media.
        // new method should use the Add method along with specific appropriate Resource object and Upload Token.
        static KalturaMediaEntry StartSessionAndUploadMedia(FileStream fileStream)
        {
            KalturaClient client = new KalturaClient(GetConfig());

            // start new session (client session is enough when we do operations in a users scope)
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.USER, PARTNER_ID, 86400, "");

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
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.USER, PARTNER_ID, 86400, "");

            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using .Net Client";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;

            // add the media using the upload token
            mediaEntry = client.MediaService.AddFromUrl(mediaEntry, url.ToString());

            Console.WriteLine("New media was created with the following id: " + mediaEntry.Id);
        }

        /// <summary>
        /// Shows how to start session, create a mix, add media, and append it to a mix timeline using multi request
        /// </summary>
        private static void AdvancedMultiRequestExample()
        {
            FileStream fileStream = new FileStream("DemoVideo.flv", FileMode.Open, FileAccess.Read);

            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry Using C#.Net Client To Test Flavor Replace";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;

            KalturaUploadedFileTokenResource mediaResource = new KalturaUploadedFileTokenResource();
            mediaResource.Token = "{1:result:id}";

            KalturaClient client = new KalturaClient(GetConfig());

            client.KS = client.GenerateSession(ADMIN_SECRET, "", KalturaSessionType.ADMIN, PARTNER_ID);
            client.StartMultiRequest();
            client.UploadTokenService.Add();
            client.MediaService.Add(mediaEntry);
            client.UploadTokenService.Upload("{1:result:id}", fileStream);
            client.MediaService.AddContent("{2:result:id}", mediaResource);
            
            KalturaMultiResponse response = client.DoMultiRequest();

            foreach (object obj in response)
            {
                if (obj.GetType() == typeof(KalturaAPIException))
                {
                    Console.WriteLine("Error occurred: " + ((KalturaAPIException)obj).Message);
                }
            }

            // when accessing the response object we will use an index and not the response number (response number - 1)
            if (response[3].GetType() == typeof(KalturaMediaEntry))
            {
                KalturaMediaEntry newMediaEntry = (KalturaMediaEntry)response[3];
                Console.WriteLine("Multirequest newly added entry id: " + newMediaEntry.Id + ", status: " + newMediaEntry.Status);
            }
        }
    }
}
