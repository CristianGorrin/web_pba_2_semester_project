using System;
using System.Collections.Generic;
using System.Text;

namespace app_lib.Interface {
    public interface IClassEntity {
#pragma warning disable IDE1006 // Naming Styles
        int id { get; }
        string name { get; }
#pragma warning restore IDE1006 // Naming Styles
    }
}
