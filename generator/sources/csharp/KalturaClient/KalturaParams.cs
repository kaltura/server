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
using System.Web;

namespace Kaltura
{
    public class KalturaParams : SortedList<string, IKalturaSerializable>, IKalturaSerializable
    {
        private bool isArray;

        public string ToJson()
        {
            string[] values = new string[this.Count];
            int index = 0;
            foreach (KeyValuePair<string, IKalturaSerializable> item in this)
            {
                if (isArray)
                    values[index++] = item.Value.ToJson();
                else
                    values[index++] = "\"" + item.Key + "\":" + item.Value.ToJson();
            }

            if (isArray)
                return string.Format("[{0}]", string.Join(",", values));
            else
                return string.Format("{{{0}}}", string.Join(",", values));
        }

        public string ToQueryString()
        {
            string str = "";
            foreach (KeyValuePair<string, IKalturaSerializable> item in this)
                str += (item.Key + "=" + HttpUtility.UrlEncode(item.Value.ToQueryString()) + "&");

            if (str.EndsWith("&"))
                str = str.Substring(0, str.Length - 1);

            return str;
        }

        public void Add(KalturaParams properties)
        {
            foreach (string key in properties.Keys)
                Add(key, properties[key]);
        }

        public void Add(string key, KalturaObjectBase kalturaObject)
        {
            KalturaParams objectProperties = kalturaObject.ToParams();
            Add(key, objectProperties);
        }

        public void AddIfNotNull(string key, KalturaObjectBase kalturaObject)
        {
            if (kalturaObject != null)
            {
                Add(key, kalturaObject);
            }
        }

        public void AddIfNotNull<T>(string key, IList<T> array) where T : KalturaObjectBase
        {
            if (array == null)
                return;

            KalturaParams arrayParams = new KalturaParams() { isArray = true };
            if (array.Count == 0)
            {
                arrayParams.Add("-", "");
            }
            else
            {
                int i = 0;
                foreach (KalturaObjectBase item in array)
                {
                    arrayParams.Add(i.ToString(), item);
                    i++;
                }
            }
            this.Add(key, arrayParams);
        }

        public void AddIfNotNull<T>(string key, IDictionary<string, T> map) where T : KalturaObjectBase
        {
            if (map == null)
                return;

            KalturaParams arrayParams = new KalturaParams();
            if (map.Count == 0)
            {
                arrayParams.Add("-", "");
            }
            else
            {
                foreach (string itemKey in map.Keys)
                {
                    arrayParams.Add(itemKey, map[itemKey]);
                }
            }
            this.Add(key, arrayParams);
        }

        public void Add(string key, string value)
        {
            this.Add(key, new KalturaParam(value));
        }

        public void AddIfNotNull(string key, string value)
        {
            if (value != null)
                this.Add(key, new KalturaParam(value));
        }

        public void AddIfNotNull(string key, int value)
        {
            if (value != int.MinValue)
                this.Add(key, value.ToString());
        }


        public void AddIfNotNull(string key, float value)
        {
            if (value != Single.MinValue)
                this.Add(key, value.ToString());
        }

        public void AddIfNotNull(string key, long value)
        {
            if (value != long.MinValue)
                this.Add(key, value.ToString());
        }

        public void AddIfNotNull(string key, Enum value)
        {
            this.AddIfNotNull(key, value.GetHashCode());
        }

        public void AddIfNotNull(string key, KalturaStringEnum value)
        {
            if (value != null)
                this.Add(key, value.ToString());
        }

        public void AddIfNotNull(string key, bool? value)
        {
            if (value.HasValue)
                this.Add(key, (value.Value) ? "true" : "false");
        }

        public void AddReplace(string key, string value)
        {
            if (this.Keys.Contains(key))
                this.Remove(key);
            this.Add(key, value);
        }
    }
}