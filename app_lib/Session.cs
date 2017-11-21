using app_lib.Interface;
using System;
using System.Collections.Generic;
using System.Text;

namespace app_lib {
    public static class Session {
        public static string DeviceUuid;
        public static string Email;
        public static int AccId;
        public static List<IStatisticsEntity> Cache;
    }
}
