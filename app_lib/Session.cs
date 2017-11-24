using app_lib.Interface;
using System;
using System.Collections.Generic;
using System.IO;
using Newtonsoft.Json;

namespace app_lib {
    public static class Session {
        private static SerializeSession ThisSession {
            get {
                return new SerializeSession() {
                    DeviceUuid      = DeviceUuid,
                    Email           = Email,
                    AccId           = AccId,
                    CacheStatistics = CacheStatistics,
                    CacheHistrory   = CacheHistrory,
                    LastUpdateDay   = LastUpdateDay,
                    FullName        = FullName
                };
            }
            set {
                DeviceUuid = value.DeviceUuid;
                Email = value.Email;
                AccId = value.AccId;
                CacheHistrory = value.CacheHistrory;
                CacheStatistics = value.CacheStatistics;
                LastUpdateDay = value.LastUpdateDay;
                FullName = value.FullName;
            }
        }
        private static DateTime UnixTimeSart { get; set; }
        public static string DeviceUuid { get; set; }
        public static string Email { get; set; }
        public static int AccId { get; set; }
        public static List<IStatisticsEntity> CacheStatistics { get; set; }
        public static List<IHistroryEntity> CacheHistrory { get; set; }
        public static int LastUpdateDay { get; set; }
        public static string FullName { get; set; }
        
        static Session() {
            Reset();
            UnixTimeSart = new DateTime(1970, 1, 1);
        }

        public static bool Login(string email, string password) {
            var result_login = Datasource.Datasource.Login(email, password);

            if (!result_login.result) return false;

            AccId           = result_login.acc_id;
            DeviceUuid      = result_login.device_uuid;
            CacheStatistics = Datasource.Datasource.GetStatistics(DeviceUuid);
            CacheHistrory   = Datasource.Datasource.GetListHistrory(CacheStatistics);
            LastUpdateDay   = DateTime.Now.Day;
            Email           = email;
            FullName        = result_login.full_name;

            Save();

            return true;
        }

        public static void Save() {
            if (Paths.FileExist(Paths.SessionFile))
                Paths.DeleteFile(Paths.SessionFile);

            using (var sw = new StreamWriter(Paths.GetStream(Paths.SessionFile, 
                Paths.StreamType.Output))) {

                var json = JsonConvert.SerializeObject(ThisSession);
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
                    ThisSession = JsonConvert.DeserializeObject<SerializeSession>(result);
            }
        }

        private static void Reset() {
            DeviceUuid      = null;
            Email           = null;
            AccId           = -1;
            CacheStatistics = null;
            CacheHistrory   = null;
            LastUpdateDay   = -1;
            FullName        = null;
        }

        public static void Update() {
            CacheStatistics = Datasource.Datasource.GetStatistics(DeviceUuid);
            CacheHistrory   = Datasource.Datasource.GetListHistrory(CacheStatistics);
            LastUpdateDay   = DateTime.Now.Day;

            Save();
        }

        public static bool Validate() {
            //TODO The login validate in session
            return true;
        }

        public static bool AddToCacheHistrory(string uuid, string _class, string subject) {
            if (CacheHistrory.Exists((x) => x.Uuid == uuid)) return false;
            DateTime dt = DateTime.Now;

            CacheHistrory.Add(new DataStructure.Histrory() {
                Absence  = false,
                Class    = _class,
                Date     = dt.ToString(@"dd/MM-yyyy"),
                Subject  = subject,
                Time     = dt.ToString("HH:mm"),
                Unixtime = (int)(DateTime.UtcNow.Subtract(UnixTimeSart)).TotalSeconds,
                Uuid     = uuid
            });
            return true;
        }
    }

    class SerializeSession {
        public string DeviceUuid { get; set; }
        public string Email { get; set; }
        public int AccId { get; set; }
        public List<IStatisticsEntity> CacheStatistics { get; set; }
        public List<IHistroryEntity> CacheHistrory { get; set; }
        public int LastUpdateDay { get; set; }
        public string FullName { get; set; }
    }
}
