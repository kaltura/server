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
using System.Text;
using System.ComponentModel;

namespace Kaltura
{
    public class KalturaObjectBase : INotifyPropertyChanged
    {
        #region Methods
        public virtual KalturaParams ToParams()
        {
            return new KalturaParams();
        }

        protected int ParseInt(string s)
        {
            int i = int.MinValue;
            int.TryParse(s, out i);
            return i;
        }

        protected Single ParseFloat(string s)
        {
            Single i = Single.MinValue;
            Single.TryParse(s, out i);
            return i;
        }
        
        protected long ParseLong(string s)
        {
            long l = long.MinValue;
            long.TryParse(s, out l);
            return l;
        }

        protected Enum ParseEnum(Type type, string s)
        {
            int i = this.ParseInt(s);
            return (Enum)Enum.Parse(type, i.ToString());
        }

        protected bool ParseBool(string s)
        {
            if (s == "1")
                return true;
            return false;
        }

        protected virtual void OnPropertyChanged(string propertyName)
        {
            if (PropertyChanged != null)
                PropertyChanged(this, new PropertyChangedEventArgs(propertyName));
        }
        #endregion

        #region INotifyPropertyChanged Members

        public event PropertyChangedEventHandler PropertyChanged;

        #endregion
    }
}