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
using System.Threading;

namespace Kaltura
{
    class KalturaClientTester : IKalturaLogger
    {
        private const int PARTNER_ID = @YOUR_PARTNER_ID@; //enter your partner id
        private const string ADMIN_SECRET = "@YOUR_ADMIN_SECRET@"; //enter your admin secret
        private const string SERVICE_URL = "@SERVICE_URL@";
        private const string USER_ID = "testUser";

        
        private static string uniqueTag;

        public void Log(string msg)
        {
            Console.WriteLine(msg);
        }

        static void Main(string[] args)
        {
            Console.WriteLine("Starting C# Kaltura API Client Library");
            int code = 0;
            uniqueTag = Guid.NewGuid().ToString().Replace("-", "").Substring(0, 20);
            try
            {
                if(args.Length > 0 && args[0].Equals("--with-threads"))
                {
                    SampleThreadedChunkUpload();
                }
            }
            catch (KalturaAPIException e0)
            {
                Console.WriteLine("failed chunk upload: " + e0.Message);
            }
            
            try
            {
                ResponseProfileExample();
            }
            catch (KalturaAPIException e)
            {
                Console.WriteLine("Failed ResponseProfileExample: " + e.Message);
                code = -1;
            }

            try
            {
                SampleReplaceVideoFlavorAndAddCaption();
            }
            catch (KalturaAPIException e)
            {
                Console.WriteLine("Failed SampleReplaceVideoFlavorAndAddCaption: " + e.Message);
                code = -1;
            }

            try
            {
                SampleMetadataOperations();
            }
            catch (KalturaAPIException e)
            {
                Console.WriteLine("Failed SampleMetadataOperations: " + e.Message);
                code = -1;
            }

            try
            {
                AdvancedMultiRequestExample();
            }
            catch (KalturaAPIException e)
            {
                Console.WriteLine("Failed AdvancedMultiRequestExample: " + e.Message);
                code = -1;
            }
            
            try
            {
                PlaylistExecuteMultiRequestExample();
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Failed PlaylistExecuteMultiRequestExample: " + e1.Message);
                code = -1;
            }
            catch(Exception ex)
            {
                Console.WriteLine("Exception thrown: " + ex.Message);
                code = -1;
            }
			
            if (code == 0)
            {
                Console.WriteLine("Finished running client library tests");
            }

            Environment.Exit(code);
        }

        // setting chunk size to a small chunk, because demo file is 500k size.
        // in actual implementation, a chunk size of 10MB is good practice.
        const int CHUNK_SIZE = 10240;
        static void SampleThreadedChunkUpload()
        {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");

            string fname = "DemoVideo.flv";
            FileStream fileStream = new FileStream(fname, FileMode.Open, FileAccess.Read, FileShare.Read);
            KalturaUploadToken myToken = new KalturaUploadToken();
            myToken.FileName = fname;
            FileInfo f = new FileInfo(fname);
            myToken.FileSize = f.Length;

            string mediaName = "C# Media Entry Uploaded in chunks using threads";

            KalturaUploadToken uploadToken = client.UploadTokenService.Add(myToken);

            chunkThreaded(client.KS, fileStream, uploadToken.Id);
            
            KalturaUploadedFileTokenResource mediaResource = new KalturaUploadedFileTokenResource();
            mediaResource.Token = uploadToken.Id;
            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = mediaName;
            mediaEntry.MediaType = KalturaMediaType.VIDEO;
            mediaEntry = client.MediaService.Add(mediaEntry);
            mediaEntry = client.MediaService.AddContent(mediaEntry.Id, mediaResource);
        }

        static int maxUploadThreads = 4;
        static public int workingThreads = 0;

        static void chunkThreaded(string ks, FileStream fileStream, string uploadTokenId)
        {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = ks;

            LinkedList<int> ranges = new LinkedList<int>();
            int chunkSize = CHUNK_SIZE;

            long fileSize = fileStream.Length - (fileStream.Length % chunkSize);
            int lastPosition = unchecked((int)fileSize);
            for (int i = chunkSize; i < fileSize; i = i + chunkSize)
            {
                LinkedListNode<int> pos = new LinkedListNode<int>(i);
                ranges.AddFirst(pos);
            }
            KalturaUploadThread uploader = new KalturaUploadThread(ks, fileStream, chunkSize, ranges, uploadTokenId);

            try
            {
                byte[] chunk = new byte[chunkSize];
                fileStream.Seek(0, SeekOrigin.Begin);
                int bytesRead = fileStream.Read(chunk, 0, chunkSize);
                Stream chunkFile = new MemoryStream(chunk);
                client.UploadTokenService.Upload(uploadTokenId, chunkFile, false, false);
                chunkFile.Close();
            }
            catch (KalturaAPIException ex)
            {
                Console.WriteLine("failed uploading first chunk " + ex.Message);
                throw ex;
            }

            int counter = 0;
            Dictionary<int, Thread> threadList = new Dictionary<int, Thread>();
            try
            {
                while (!ranges.Count.Equals(0))
                {
                    // this will open all threads allowed
                    while (workingThreads < maxUploadThreads && !ranges.Count.Equals(0))
                    {
                        int threadID = workingThreads + 1;
                        if (threadList.ContainsKey(threadID))
                        {
                            threadList.Remove(threadID);
                        }
                        threadList.Add(threadID, new Thread(new ThreadStart(uploader.upload)));
                        threadList[threadID].Start();
                        counter++;
                        workingThreads += 1;
                    }
                }

                while (workingThreads > 0)
                {
                    Thread.Sleep(100);
                }

                // threads have finished at this point. upload last chunk

            }
            catch (ThreadStateException e)
            {
                Console.WriteLine("thread exploded with " + e.Message);
                throw e;
            }

            byte[] lastChunk = new byte[chunkSize];
            long lengthLong = fileStream.Length - lastPosition;
            int length = unchecked((int)lengthLong);
            fileStream.Seek(lastPosition, SeekOrigin.Begin);
            int lastBytesRead = fileStream.Read(lastChunk, 0, length);
            Stream lastChunkFile = new MemoryStream(lastChunk);
            client.UploadTokenService.Upload(uploadTokenId, lastChunkFile, true, true, lastPosition);
            lastChunkFile.Close();
        }

        public static KalturaConfiguration GetConfig()
        {
            KalturaConfiguration config = new KalturaConfiguration();
            config.ServiceUrl = SERVICE_URL;
            config.Logger = new KalturaClientTester();
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
        
        static void PlaylistExecuteMultiRequestExample()
        {
            KalturaClient client = new KalturaClient(GetConfig());

            client.StartMultiRequest();

            // Request 1
            client.SessionService.Start(ADMIN_SECRET, "", KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = "{1:result}"; // for the current multi request, the result of the first call will be used as the ks for next calls

            // Request 2
            client.MediaService.List();

            KalturaMultiResponse response = client.DoMultiRequest();

            foreach (object obj in response)
            {
                if (obj.GetType() == typeof(KalturaAPIException))
                {
                    Console.WriteLine("Error occurred: " + ((KalturaAPIException)obj).Message);
                }
            }

            String twoEntries = "";

            if (response[1].GetType() == typeof(KalturaMediaListResponse))
            {
                KalturaMediaListResponse mediaListResponse = (KalturaMediaListResponse)response[1];
                twoEntries = mediaListResponse.Objects[0].Id + ", " + mediaListResponse.Objects[1].Id;
                Console.WriteLine("We will use the first 2 entries we got as a reponse: " + twoEntries);
            }

            if(twoEntries.Equals(""))
            {
                return;
            }

            string ks = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = ks;

            KalturaPlaylist newPlaylist = new KalturaPlaylist();
            newPlaylist.Name = "Test Playlist";
            newPlaylist.PlaylistContent = twoEntries;
            newPlaylist.PlaylistType = KalturaPlaylistType.STATIC_LIST;

            KalturaPlaylist kPlaylist = client.PlaylistService.Add(newPlaylist);

            // new multirequest
            client.StartMultiRequest();

            client.PlaylistService.Execute(kPlaylist.Id);
            client.PlaylistService.Execute(kPlaylist.Id);

            response = client.DoMultiRequest();

            foreach (object obj in response)
            {
                if (obj.GetType() == typeof(KalturaAPIException))
                {
                    Console.WriteLine("Error occurred: " + ((KalturaAPIException)obj).Message);
                }
            }

            foreach (var currentResponse in response)
            {
                if(currentResponse.GetType() != typeof(KalturaMultiResponse))
                {
                    throw new Exception("Unexpected multirequest response");
                }
            }
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
                if (obj is KalturaAPIException)
                {
                    Console.WriteLine("Error occurred: " + ((KalturaAPIException)obj).Message);
                }
            }

            // when accessing the response object we will use an index and not the response number (response number - 1)
            if (response[3] is KalturaMediaEntry)
            {
                KalturaMediaEntry newMediaEntry = (KalturaMediaEntry)response[3];
                Console.WriteLine("Multirequest newly added entry id: " + newMediaEntry.Id + ", status: " + newMediaEntry.Status);
            }
        }
        
	    private static KalturaMediaEntry createEntry()
	    {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.USER, PARTNER_ID, 86400, "");

		    KalturaMediaEntry entry = new KalturaMediaEntry();
		    entry.MediaType = KalturaMediaType.VIDEO;
            entry.Name = "test_" + Guid.NewGuid().ToString();
            entry.Tags = uniqueTag;
    		
		    return client.MediaService.Add(entry);
	    }

	    private static KalturaMetadata createMetadata(int metadataProfileId, KalturaMetadataObjectType objectType, string objectId, string xmlData)
	    {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.USER, PARTNER_ID, 86400, "");

		    KalturaMetadata metadata = new KalturaMetadata();
		    metadata.MetadataObjectType = objectType;
		    metadata.ObjectId = objectId;
    		
		    return client.MetadataService.Add(metadataProfileId, objectType, objectId, xmlData);
	    }
        
	    private static KalturaMetadataProfile createMetadataProfile(KalturaMetadataObjectType objectType, string xsdData)
	    {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");

		    KalturaMetadataProfile metadataProfile = new KalturaMetadataProfile();
            metadataProfile.MetadataObjectType = objectType;
            metadataProfile.Name = "test_" + Guid.NewGuid().ToString();
    		
		    return client.MetadataProfileService.Add(metadataProfile, xsdData);
	    }

        private static IList<KalturaMediaEntry> createEntriesWithMetadataObjects(int entriesCount)
	    {
            return createEntriesWithMetadataObjects(entriesCount, 2);
        }

        private static IList<KalturaMediaEntry> createEntriesWithMetadataObjects(int entriesCount, int metadataProfileCount)
	    {
		    IList<KalturaMediaEntry> entries = new List<KalturaMediaEntry>(2);
            IDictionary<string, KalturaMetadataProfile> metadataProfiles = new Dictionary<string, KalturaMetadataProfile>(3);

            string xsd;
            for(int i = 1; i <= metadataProfileCount; i++)
            {
                xsd = @"<xsd:schema xmlns:xsd=""http://www.w3.org/2001/XMLSchema"">
        <xsd:element name=""metadata"">
            <xsd:complexType>
                <xsd:sequence>
                    <xsd:element name=""Choice" + i + @""" minOccurs=""0"" maxOccurs=""1"">
                        <xsd:annotation>
                            <xsd:documentation></xsd:documentation>
                            <xsd:appinfo>
                                <label>Example choice " + i + @"</label>
                                <key>choice" + i + @"</key>
                                <searchable>true</searchable>
                                <description>Example choice " + i + @"</description>
                            </xsd:appinfo>
                        </xsd:annotation>
                        <xsd:simpleType>
                            <xsd:restriction base=""listType"">
                                <xsd:enumeration value=""on"" />
                                <xsd:enumeration value=""off"" />
                            </xsd:restriction>
                        </xsd:simpleType>
                    </xsd:element>
                    <xsd:element name=""FreeText" + i + @""" minOccurs=""0"" maxOccurs=""1"" type=""textType"">
                        <xsd:annotation>
                            <xsd:documentation></xsd:documentation>
                            <xsd:appinfo>
                                <label>Free text " + i + @"</label>
                                <key>freeText" + i + @"</key>
                                <searchable>true</searchable>
                                <description>Free text " + i + @"</description>
                            </xsd:appinfo>
                        </xsd:annotation>
                    </xsd:element>
                </xsd:sequence>
            </xsd:complexType>
        </xsd:element>
        <xsd:complexType name=""textType"">
            <xsd:simpleContent>
                <xsd:extension base=""xsd:string"" />
            </xsd:simpleContent>
        </xsd:complexType>
        <xsd:complexType name=""objectType"">
            <xsd:simpleContent>
                <xsd:extension base=""xsd:string"" />
            </xsd:simpleContent>
        </xsd:complexType>
        <xsd:simpleType name=""listType"">
            <xsd:restriction base=""xsd:string"" />
        </xsd:simpleType>
    </xsd:schema>";
    				
                metadataProfiles.Add(i.ToString(), createMetadataProfile(KalturaMetadataObjectType.ENTRY, xsd));
            }
    		
            string xml;
            for(int i = 0; i < entriesCount; i++)
            {
                KalturaMediaEntry entry = createEntry();
                entries.Add(entry);

                foreach (string index in metadataProfiles.Keys)
                {
                    xml = @"<metadata>
        <Choice" + index + ">on</Choice" + index + @">
        <FreeText" + index + ">example text " + index + "</FreeText" + index + @">
    </metadata>";

                    createMetadata(metadataProfiles[index].Id, KalturaMetadataObjectType.ENTRY, entry.Id, xml);
                }
            }
    		
            return entries;
	    }
        // Show how to use response-profile
        private static void ResponseProfileExample()
        {
            KalturaClient client = new KalturaClient(GetConfig());
            client.KS = client.GenerateSession(ADMIN_SECRET, USER_ID, KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");

		    int entriesTotalCount = 4;
		    int metadataProfileTotalCount = 2;
            int metadataPageSize = 2;

            IList<KalturaMediaEntry> entries = createEntriesWithMetadataObjects(entriesTotalCount, metadataProfileTotalCount);
    		
            KalturaMediaEntryFilter entriesFilter = new KalturaMediaEntryFilter();
            entriesFilter.StatusIn = KalturaEntryStatus.PENDING.ToString() + "," + KalturaEntryStatus.NO_CONTENT.ToString();
            entriesFilter.TagsLike = uniqueTag;
    		
            KalturaFilterPager entriesPager = new KalturaFilterPager();
            entriesPager.PageSize = entriesTotalCount;
    		
            KalturaMetadataFilter metadataFilter = new KalturaMetadataFilter();
            metadataFilter.MetadataObjectTypeEqual = KalturaMetadataObjectType.ENTRY;
    		
            KalturaResponseProfileMapping metadataMapping = new KalturaResponseProfileMapping();
            metadataMapping.FilterProperty = "objectIdEqual";
            metadataMapping.ParentProperty = "id";

            IList<KalturaResponseProfileMapping> metadataMappings = new List<KalturaResponseProfileMapping>();
            metadataMappings.Add(metadataMapping);
    		
            KalturaFilterPager metadataPager = new KalturaFilterPager();
            metadataPager.PageSize = metadataPageSize;
    		
            KalturaDetachedResponseProfile metadataResponseProfile = new KalturaDetachedResponseProfile();
            metadataResponseProfile.Name = "metadata_" + uniqueTag;
            metadataResponseProfile.Type = KalturaResponseProfileType.INCLUDE_FIELDS;
            metadataResponseProfile.Fields = "id,objectId,createdAt, xml";
            metadataResponseProfile.Filter = metadataFilter;
            metadataResponseProfile.Pager = metadataPager;
            metadataResponseProfile.Mappings = metadataMappings;
    		
            IList<KalturaDetachedResponseProfile> metadataResponseProfiles = new List<KalturaDetachedResponseProfile>();
            metadataResponseProfiles.Add(metadataResponseProfile);

            KalturaResponseProfile responseProfile = new KalturaResponseProfile();
            responseProfile.Name = "test_" + uniqueTag;
            responseProfile.SystemName = "test_" + uniqueTag;
            responseProfile.Type = KalturaResponseProfileType.INCLUDE_FIELDS;
            responseProfile.Fields = "id,name,createdAt";
            responseProfile.RelatedProfiles = metadataResponseProfiles;

            responseProfile = client.ResponseProfileService.Add(responseProfile);
    		    		
            KalturaResponseProfileHolder nestedResponseProfile = new KalturaResponseProfileHolder();
            nestedResponseProfile.Id = responseProfile.Id;
    		
            client.ResponseProfile = nestedResponseProfile;
            IList<KalturaBaseEntry> list = client.BaseEntryService.List(entriesFilter, entriesPager).Objects;
    		
            if(entriesTotalCount != list.Count)
            {
                throw new Exception("entriesTotalCount[" + entriesTotalCount + "] != list.Count[" + list.Count + "]");
            }
    		
            foreach(KalturaBaseEntry entry in list)
            {	
                if(entry.RelatedObjects == null)
                {
                    throw new Exception("Related objects are missing");
                }

                if(!entry.RelatedObjects.ContainsKey(metadataResponseProfile.Name))
                {
                    throw new Exception("Related object [" + metadataResponseProfile.Name + "] is missing");
                }

                if (!(entry.RelatedObjects[metadataResponseProfile.Name] is KalturaMetadataListResponse))
                {
                    throw new Exception("Related object [" + metadataResponseProfile.Name + "] has wrong type [" + entry.RelatedObjects[metadataResponseProfile.Name].GetType() + "]");
                }
                KalturaMetadataListResponse metadataListResponse = (KalturaMetadataListResponse)entry.RelatedObjects[metadataResponseProfile.Name];

                if(metadataListResponse.Objects.Count != metadataProfileTotalCount)
                {
                    throw new Exception("Related object [" + metadataResponseProfile.Name + "] has wrong number of objects");
                }

                foreach(KalturaMetadata metadata in metadataListResponse.Objects)
                {
                    if (metadata.ObjectId != entry.Id)
                    {
                        throw new Exception("Related object [" + metadataResponseProfile.Name + "] metadata [" + metadata.Id + "] related to wrong object [" + metadata.ObjectId + "]");
                    }
                }
            }
        }
    }
}
