using System;
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
