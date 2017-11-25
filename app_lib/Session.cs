using app_lib.Interface;
using System;
using System.Collections.Generic;
using System.IO;
using Newtonsoft.Json;
using app_lib.Extensions;

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
                var reader = new JsonTextReader(sr);
                reader.Read();
                while (reader.Read()) {
                    if (reader.TokenType == JsonToken.EndObject) break;

                    switch ((string)reader.Value) {
                        case "DeviceUuid":
                            reader.Read();
                            DeviceUuid = (string)reader.Value;
                            break;
                        case "Email":
                            reader.Read();
                            Email = (string)reader.Value;
                            break;
                        case "AccId":
                            reader.Read();
                            AccId = Convert.ToInt32((long)reader.Value);
                            break;
                        case "LastUpdateDay":
                            reader.Read();
                            LastUpdateDay = Convert.ToInt32((long)reader.Value);
                            break;
                        case "FullName":
                            reader.Read();
                            FullName = (string)reader.Value;
                            break;
                        case "CacheStatistics":
                            reader.Read();
                            var buffer_statistics = new DataStructure.StatisticsEntity();
                            while (reader.Read()) {
                                if (reader.TokenType == JsonToken.EndArray) break;
                                if (reader.TokenType == JsonToken.StartObject) continue;
                                if (reader.TokenType == JsonToken.EndObject) {
                                    CacheStatistics.Add(buffer_statistics);
                                    buffer_statistics = new DataStructure.StatisticsEntity();
                                    continue;
                                }

                                switch ((string)reader.Value) {
                                    case "Subject":
                                        reader.Read();
                                        buffer_statistics.Subject = (string)reader.Value;
                                        break;
                                    case "SubjectId":
                                        reader.Read();
                                        buffer_statistics.SubjectId = Convert.ToInt32(
                                            (long)reader.Value);
                                        break;
                                    case "Total":
                                        reader.Read();
                                        buffer_statistics.Total = Convert.ToInt32(
                                            (long)reader.Value);
                                        break;
                                    case "Absences":
                                        reader.Read();
                                        buffer_statistics.Absences = Convert.ToInt32(
                                            (long)reader.Value);
                                        break;
                                    case "Class":
                                        reader.Read();
                                        reader.Read();
                                        string buffer_key = "";
                                        bool buffer_value = false;
                                        while (reader.Read()) {
                                            if (reader.TokenType == JsonToken.EndArray)
                                                break;

                                            if (reader.TokenType == JsonToken.StartObject)
                                                continue;

                                            if (reader.TokenType == JsonToken.EndObject) {
                                                buffer_statistics.Class.Add(
                                                    new KeyValuePair<string, bool>(buffer_key,
                                                        buffer_value
                                                    )
                                                );
                                                continue;
                                            }

                                            switch ((string)reader.Value) {
                                                case "Key":
                                                    reader.Read();
                                                    buffer_key = (string)reader.Value;
                                                    break;
                                                case "Value":
                                                    reader.Read();
                                                    buffer_value = (bool)reader.Value;
                                                    break;
                                                default:
                                                    break;
                                            }
                                        }
                                        break;
                                    default:
                                        ExtensionNewtonsoft.Skip(ref reader);
                                        break;
                                }
                            }
                            break;
                        case "CacheHistrory":
                            reader.Read();
                            var buffer_histrory = new DataStructure.Histrory();
                            while (reader.Read()) {
                                if (reader.TokenType == JsonToken.EndArray) break;
                                if (reader.TokenType == JsonToken.StartObject) continue;
                                if (reader.TokenType == JsonToken.EndObject) {
                                    CacheHistrory.Add(buffer_histrory);
                                    buffer_histrory = new DataStructure.Histrory();
                                    continue;
                                }

                                switch ((string)reader.Value) {
                                    case "Class":
                                        reader.Read();
                                        buffer_histrory.Class = (string)reader.Value;
                                        break;
                                    case "Date":
                                        reader.Read();
                                        buffer_histrory.Date = (string)reader.Value;
                                        break;
                                    case "Subject":
                                        reader.Read();
                                        buffer_histrory.Subject = (string)reader.Value;
                                        break;
                                    case "Time":
                                        reader.Read();
                                        buffer_histrory.Time = (string)reader.Value;
                                        break;
                                    case "Uuid":
                                        reader.Read();
                                        buffer_histrory.Uuid = (string)reader.Value;
                                        break;
                                    case "Absence":
                                        reader.Read();
                                        buffer_histrory.Absence = !(bool)reader.Value;
                                        break;
                                    case "Unixtime":
                                        reader.Read();
                                        buffer_histrory.Unixtime = Convert.ToInt32(
                                            (long)reader.Value);
                                        break;
                                    default:
                                        ExtensionNewtonsoft.Skip(ref reader);
                                        break;
                                }
                            }
                            break;
                        default:
                            ExtensionNewtonsoft.Skip(ref reader);
                            break;
                    }
                }
                reader.Close();
            }
        }

        private static void Reset() {
            DeviceUuid      = null;
            Email           = null;
            AccId           = -1;
            CacheStatistics = new List<IStatisticsEntity>();
            CacheHistrory   = new List<IHistroryEntity>();
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
            Save();

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
