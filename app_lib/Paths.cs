using System;
using System.IO;
using Xamarin.Forms;

namespace app_lib {
    public static class Paths {
        public static string SessionFile { get; set; }
        public static Func<string, StreamType, Stream> GetStream { get; set; }
        public static Func<string, bool> FileExist { get; set; }
        public static Func<string, bool> DeleteFile { get; set; }

        static Paths() {
            switch (Device.RuntimePlatform) {
                case Device.Android:
                    SessionFile = "session.json";
                    break;
                default:
                    throw new NotImplementedException();
            }
        }

        public enum StreamType { Input, Output }
    }
}
