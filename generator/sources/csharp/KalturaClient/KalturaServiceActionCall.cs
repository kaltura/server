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
using System.IO;

namespace Kaltura
{
    public class KalturaServiceActionCall
    {
        #region Private Fields

        private string _Service;
        private string _Action;
        private KalturaParams _Params;
        private KalturaFiles _Files;

        #endregion

        #region Properties

        public string Service
        {
            get { return _Service; }
        }

        public string Action
        {
            get { return _Action; }
        }

        public KalturaParams Params
        {
            get { return _Params; }
        }

        public KalturaFiles Files
        {
            get { return _Files; }
        }

        public KalturaParams GetParamsForMultiRequest(int multiRequestNumber)
        {
            KalturaParams multiRequestParams = new KalturaParams();
            multiRequestParams.Add(multiRequestNumber + ":service", this._Service);
            multiRequestParams.Add(multiRequestNumber + ":action", this._Action);
            foreach (KeyValuePair<string, string> param in this._Params)
            {
                multiRequestParams.Add(multiRequestNumber + ":" + param.Key, param.Value);
            }

            return multiRequestParams;
        }

        public KalturaFiles GetFilesForMultiRequest(int multiRequestNumber)
        {
            KalturaFiles multiRequestParams = new KalturaFiles();
            foreach (KeyValuePair<string, FileStream> param in this._Files)
            {
                multiRequestParams.Add(multiRequestNumber + ":" + param.Key, param.Value);
            }

            return multiRequestParams;
        }

        #endregion

        #region CTor

        public KalturaServiceActionCall(string service, string action, KalturaParams kparams)
            : this(service, action, kparams, new KalturaFiles())
        {

        }

        public KalturaServiceActionCall(string service, string action, KalturaParams kparams, KalturaFiles kfiles)
        {
            this._Service = service;
            this._Action = action;
            this._Params = kparams;
            this._Files = kfiles;
        }

        #endregion
    }
}
