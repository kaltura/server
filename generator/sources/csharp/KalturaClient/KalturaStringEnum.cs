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
using System.Reflection;

namespace Kaltura
{
    public class KalturaStringEnum
    {
        private readonly string name;

        protected KalturaStringEnum(string name)
        {
            this.name = name;
        }

        public override string ToString()
        {
            return name;
        }

        public static KalturaStringEnum Parse(Type type, string name)
        {
            FieldInfo[] fields = type.GetFields();
            foreach (FieldInfo field in fields)
            {
                object val = field.GetValue(null);
                if (val.GetType().BaseType == typeof(KalturaStringEnum))
                {
                    if (val.ToString() == name)
                        return (KalturaStringEnum)val;
                }
            }
            return null;
        }
    }
}
