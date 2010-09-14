using System;
using System.Collections.Generic;
using System.Text;

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
