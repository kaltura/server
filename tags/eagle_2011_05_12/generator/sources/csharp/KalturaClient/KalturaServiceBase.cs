using System;
using System.Text;

namespace Kaltura
{
    public class KalturaServiceBase
    {
        #region Private Fields

        protected KalturaClient _Client;

        #endregion

        #region CTor

        public KalturaServiceBase(KalturaClient client)
        {
            this._Client = client;
        }

        #endregion
    }
}
