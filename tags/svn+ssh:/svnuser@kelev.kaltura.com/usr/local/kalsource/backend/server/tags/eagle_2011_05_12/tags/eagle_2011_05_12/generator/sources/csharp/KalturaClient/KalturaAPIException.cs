using System;
using System.Collections.Generic;
using System.Text;

namespace Kaltura
{
    public class KalturaAPIException : ApplicationException
    {
        #region Private Fields
        private string _Code;
        #endregion

        #region Properties
        public string Code
        {
            get { return this._Code; }
        }
        #endregion

        public KalturaAPIException(string code, string message): base(message)
        {
            this._Code = code;
        }
    }
}
