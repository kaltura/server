using System;
using System.Collections.Generic;
using System.Text;
using System.Web;

namespace Kaltura
{
    public class KalturaParams : SortedList<string, string>
    {
        public string ToQueryString()
        {
            string str = "";
            foreach (KeyValuePair<string, string> item in this)
                str += (item.Key + "=" + HttpUtility.UrlEncode(item.Value) + "&");

            if (str.EndsWith("&"))
                str = str.Substring(0, str.Length - 1);

            return str;
        }

        public void Add(string objectName, KalturaParams objectProperties)
        {
            foreach (KeyValuePair<string, string> item in objectProperties)
            {
                this.Add(objectName + ":" + item.Key, item.Value);
            }
        }

        public void Add(KalturaParams objectProperties)
        {
            foreach (KeyValuePair<string, string> item in objectProperties)
            {
                this.Add(item.Key, item.Value);
            }
        }

        public void AddStringIfNotNull(string key, string value)
        {
            if (value != null)
                this.Add(key, value);
        }

        public void AddIntIfNotNull(string key, int value)
        {
            if (value != int.MinValue)
                this.Add(key, value.ToString());
        }


        public void AddFloatIfNotNull(string key, float value)
        {
            if (value != Single.MinValue)
                this.Add(key, value.ToString());
        }

        public void AddEnumIfNotNull(string key, Enum value)
        {
            this.AddIntIfNotNull(key, value.GetHashCode());
        }

        public void AddStringEnumIfNotNull(string key, KalturaStringEnum value)
        {
            if (value != null)
                this.AddStringIfNotNull(key, value.ToString());
        }

        public void AddBoolIfNotNull(string key, bool? value)
        {
            if (value.HasValue)
                this.Add(key, (value.Value) ? "1" : "0");
        }
    }
}
