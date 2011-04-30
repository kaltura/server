using System;

namespace Kaltura
{
    public class KalturaPublishEventArgs : EventArgs
    {
        public long BytesPublished
        {
            get;
            set;
        }

        public KalturaPublishEventArgs(long bytesPublished)
        {
            BytesPublished = bytesPublished;
        }
    }
}