using System;
using System.Collections.Generic;
using System.Text;
using System.Web;
using System.IO;

namespace Kaltura
{
    public class KalturaFiles : SortedList<string, FileStream>
    {
        public void Add(KalturaFiles files)
        {
            foreach (KeyValuePair<string, FileStream> item in files)
            {
                this.Add(item.Key, item.Value);
            }
        }
    }
}
