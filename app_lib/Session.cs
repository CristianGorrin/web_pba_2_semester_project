using app_lib.Interface;
using System;
using System.Collections.Generic;
using System.IO;
using Newtonsoft.Json;

namespace app_lib {
    public static class Session {
        public static string DeviceUuid { get; set; }
        public static string Email { get; set; }
        public static int AccId { get; set; }
        public static List<IStatisticsEntity> Cache { get; set; }
        public static int LastUpdateDay { get; set; }
        public static string FullName { get; set; }
        
        static Session() {
            Reset();
        }

        public static bool Login(string email, string password) {
            var result_login = Datasource.Datasource.Login(email, password);

            if (!result_login.result) return false;

            AccId         = result_login.acc_id;
            DeviceUuid    = result_login.device_uuid;
            Cache         = Datasource.Datasource.GetStatistics(DeviceUuid);
            LastUpdateDay = DateTime.Now.Day;
            Email         = email;
            FullName      = result_login.full_name;

            Save();

            return true;
        }

        public static void Save() {
            if (Paths.FileExist(Paths.SessionFile))
                Paths.DeleteFile(Paths.SessionFile);

            using (var sw = new StreamWriter(Paths.GetStream(Paths.SessionFile, 
                Paths.StreamType.Output))) {

                var json = JsonConvert.SerializeObject(SerializeSession.ThisSession);
                sw.Write(json);
                sw.Flush();
            }
        }

        public static void Logout() {
            Reset();

            if (Paths.FileExist(Paths.SessionFile)) Paths.DeleteFile(Paths.SessionFile);
        }

        public static void Load() {
            using (var sr = new StreamReader(Paths.GetStream(Paths.SessionFile, 
                Paths.StreamType.Input))) {
                var result = sr.ReadToEnd();
                if (result.Length > 0)
                    SerializeSession.ThisSession = 
                        JsonConvert.DeserializeObject<SerializeSession>(result);
            }
        }

        private static void Reset() {
            DeviceUuid    = null;
            Email         = null;
            AccId         = -1;
            Cache         = null;
            LastUpdateDay = -1;
            FullName      = null;
        }

        public static void Update() {
            Cache         = Datasource.Datasource.GetStatistics(DeviceUuid);
            LastUpdateDay = DateTime.Now.Day;

            Save();
        }

        public static bool Validate() {
            //TODO The login validate in session
            return true;
        }
    }

    class SerializeSession {
        public string DeviceUuid { get; set; }
        public string Email { get; set; }
        public int AccId { get; set; }
        public List<IStatisticsEntity> Cache { get; set; }
        public int LastUpdateDay { get; set; }
        public string FullName { get; set; }

        public static SerializeSession ThisSession {
            get {
                return new SerializeSession() {
                    DeviceUuid    = Session.DeviceUuid,
                    Email         = Session.Email,
                    AccId         = Session.AccId,
                    Cache         = Session.Cache,
                    LastUpdateDay = Session.LastUpdateDay,
                    FullName      = Session.FullName
                };
            }
            set {
                Session.DeviceUuid    = value.DeviceUuid;
                Session.Email         = value.Email;
                Session.AccId         = value.AccId;
                Session.Cache         = value.Cache;
                Session.LastUpdateDay = value.LastUpdateDay;
                Session.FullName      = value.FullName;
            }
        }
    }
}
