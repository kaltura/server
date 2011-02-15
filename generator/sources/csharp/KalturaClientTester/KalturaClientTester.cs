using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Kaltura
{
    class KalturaClientTester
    {
        private const int PARTNER_ID = 0000; //enter your partner id
        private const string SECRET = "71asdac58a5a609a6bc628177bcasdad"; //enter your user secret
        private const string ADMIN_SECRET = "94f7fc20b92510c31e7emansdmn280b55"; //enter your admin secret
        private const string SERVICE_URL = "http://www.kaltura.com";

        static void Main(string[] args)
        {
            Console.WriteLine("Starting C# Kaltura API Client Library");

            Console.WriteLine("Test local video file upload");
            FileStream fileStream = new FileStream("DemoVideo.flv", FileMode.Open, FileAccess.Read);
            try
            {
                StartSessionAndUploadMedia(fileStream);
            }
            catch (KalturaAPIException e1)
            {
                Console.WriteLine("Upload media failed for local file [DemoVideo.flv] " + e1.Message);
            }

            Console.WriteLine("Test remote video file import");
            try
            {
                StartSessionAndUploadMedia(new Uri("http://www.kaltura.org/kalorg/kaltura-api-clients-generators/trunk/sources/csharp/KalturaClientTester/DemoVideo.flv"));
            }
            catch (KalturaAPIException e2)
            {
                Console.WriteLine("Upload media failed for url [http://www.kaltura.org/kalorg/kaltura-api-clients-generators/trunk/sources/csharp/KalturaClientTester/DemoVideo.flv] " + e2.Message);
            }

            Console.WriteLine("Test Multirequest");
            try
            {
                MultiRequestExample();
            }
            catch (KalturaAPIException e3)
            {
                Console.WriteLine("Multi request failed " + e3.Message);
            }
            Console.WriteLine("Test Multirequest with upload");
            try
            {
                AdvancedMultiRequestExample(fileStream);
            }
            catch (KalturaAPIException e4)
            {
                Console.WriteLine("Advanced multi request failed " + e4.Message);
            }

            Console.WriteLine("Test MetaData operations");
            try
            {
                SampleMetadataOperations();
            }
            catch (KalturaAPIException e5)
            {
                Console.WriteLine("Metadata operations request failed " + e5.Message);
            }

            Console.WriteLine("Client testing is over, press any key to exit");	    
            Console.ReadKey();
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
            int profileId = metadata[0].Id;
            string name = entries[0].Name;
            string id = entries[0].Id;
            if (metadata[0].Xsd != null) {
                Console.WriteLine("1. There are custom fields for video: " + name + ", entryid: " + id);
            } else {
	            Console.WriteLine("1. There are no custom fields for video: " + name + ", entryid: " + id);
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

        /// <summary>
        /// Shows how to start session and upload media from a local file server
        /// </summary>
        /// <param name="fileStream"></param>
        static void StartSessionAndUploadMedia(FileStream fileStream)
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
        }

        /// <summary>
        /// Shows how to start session and upload media from a web accessible server
        /// </summary>
        /// <param name="fileStream"></param>
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
        private static void AdvancedMultiRequestExample(FileStream fileStream)
        {
            KalturaClient client = new KalturaClient(GetConfig());

            client.StartMultiRequest();

            // Request 1
            client.SessionService.Start(ADMIN_SECRET, "", KalturaSessionType.ADMIN, PARTNER_ID, 86400, "");
            client.KS = "{1:result}"; // for the current multi request, the result of the first call will be used as the ks for next calls

            KalturaMixEntry mixEntry = new KalturaMixEntry();
            mixEntry.Name = ".Net Mix";
            mixEntry.EditorType = KalturaEditorType.SIMPLE;

            // Request 2
            client.MixingService.Add(mixEntry);

            // Request 3
            client.MediaService.Upload(fileStream);

            KalturaMediaEntry mediaEntry = new KalturaMediaEntry();
            mediaEntry.Name = "Media Entry For Mix";
            mediaEntry.MediaType = KalturaMediaType.VIDEO;

            // Request 4
            client.MediaService.AddFromUploadedFile(mediaEntry, "");

            // Request 5
            client.MixingService.AppendMediaEntry("", "");

            // Map request 3 result to request 4 uploadTokeId param
            client.MapMultiRequestParam(3, 4, "uploadTokenId");

            // Map request 2 result.id to request 5 mixEntryId
            client.MapMultiRequestParam(2, "id", 5, "mixEntryId");

            // Map request 4 result.id to request 5 mediaEntryId
            client.MapMultiRequestParam(4, "id", 5, "mediaEntryId");

            KalturaMultiResponse response = client.DoMultiRequest();

            foreach (object obj in response)
            {
                if (obj.GetType() == typeof(KalturaAPIException))
                {
                    Console.WriteLine("Error occurred: " + ((KalturaAPIException)obj).Message);
                }
            }

            // when accessing the response object we will use an index and not the response number (response number - 1)
            if (response[1].GetType() == typeof(KalturaMixEntry))
            {
                mixEntry = (KalturaMixEntry)response[1];
                Console.WriteLine("The new mix entry id is: " + mixEntry.Id);
            }
        }

        static KalturaConfiguration GetConfig()
        {
            KalturaConfiguration config = new KalturaConfiguration(PARTNER_ID);
            config.ServiceUrl = SERVICE_URL;
            return config;
        }
    }
}
