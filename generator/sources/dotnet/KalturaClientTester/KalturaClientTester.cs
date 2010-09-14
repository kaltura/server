using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Kaltura
{
    class KalturaClientTester
    {
        private const int PARTNER_ID = 1;
        private const string SECRET = "111";
        private const string ADMIN_SECRET = "222";
        private const string SERVICE_URL = "http://localhost/";

        static void Main(string[] args)
        {
            FileStream fileStream = new FileStream("DemoVideo.flv", FileMode.Open, FileAccess.Read);
            StartSessionAndUploadMedia(fileStream);

            StartSessionAndUploadMedia(new Uri("http://localhost/DemoVideo.flv")); // localhost will only work when running Kaltura CE on you local machine

            MultiRequestExample();

            AdvancedMultiRequestExample(fileStream);

            Console.ReadKey();
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
