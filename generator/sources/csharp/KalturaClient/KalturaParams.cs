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
        
        public void AddLongIfNotNull(string key, long value)
        {
            if (value != long.MinValue)
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

        public void AddReplace(string key, string value)
        {
            if (this.Keys.Contains(key))
                this.Remove(key);
            this.Add(key, value);
        }
    }
}