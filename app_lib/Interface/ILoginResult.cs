using System;
using System.Collections.Generic;
using System.Text;

namespace app_lib.Interface {
    public interface ILoginResult {
#pragma warning disable IDE1006 // Naming Styles
        bool result { get; }
        string device_uuid { get; }
        int acc_id { get; }
#pragma warning restore IDE1006 // Naming Styles
    }
}
