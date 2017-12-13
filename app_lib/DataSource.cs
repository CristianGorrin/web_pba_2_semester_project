using System;
using System.Collections.Generic;
using app_lib.DataStructure;
using System.Net.Http;
using System.Net;
using app_lib.Interface;
using Newtonsoft.Json;
using System.IO;
using System.Text;

namespace app_lib.Datasource {
    static class AbsenceWebServic {
        private const string URL_BASE_PATH      = "http://absence.gorrin.dk/api/";
        private const string URL_LOGIN          = "acc/students/login/app";
        private const string URL_LOGOUT         = "acc/students/logout/app";
        private const string URL_CLASS_LIST     = "class/list";
        private const string URL_PRESENCE       = "logging/presence";
        private const string URL_STATISTICS     = "acc/students/info/statistics/{0}";
        private const string URL_CLASS_LOG_INFO = "class/info/log";

        private static HttpClient m_client;

        static AbsenceWebServic() {
            m_client = new HttpClient();
            m_client.BaseAddress = new Uri(URL_BASE_PATH);

            m_client.DefaultRequestHeaders.TryAddWithoutValidation("Accept", "text/html,application/json");
            m_client.DefaultRequestHeaders.Add("User-Agent", "app_lib");
            m_client.DefaultRequestHeaders.TryAddWithoutValidation("Accept-Charset", "ISO-8859-1");
        }

        private static HttpResponseMessage SendPost(string url,
            KeyValuePair<string, string>[] content) {
            var temp = m_client.PostAsync(URL_BASE_PATH + url, new FormUrlEncodedContent(content));
            temp.Wait();

            return temp.Result;
        }

        private static HttpResponseMessage SendGet(string url) {
            var temp = m_client.GetAsync(URL_BASE_PATH + url);
            temp.Wait();

            return temp.Result;
        }

        public static HttpResponseMessage Login(string email, string password) {
            return SendPost(URL_LOGIN, new KeyValuePair<string, string>[] {
                new KeyValuePair<string, string>("email", email),
                new KeyValuePair<string, string>("password", password)
            });
        }

        public static HttpResponseMessage Logout(string email, string device_uuid) {
            return SendPost(URL_LOGOUT, new KeyValuePair<string, string>[] {
                new KeyValuePair<string, string>("email", email),
                new KeyValuePair<string, string>("device_uuid", device_uuid)
            });
        }

        public static HttpResponseMessage ClassList() {
            return SendGet(URL_CLASS_LIST);
        }

        public static HttpResponseMessage Statistics(string device_uuid) {
            return SendGet(string.Format(URL_STATISTICS, device_uuid));
        }

        public static HttpResponseMessage Presence(string qr_code, string student_id,
            string latitude, string longitude) {
            return SendPost(URL_PRESENCE, new KeyValuePair<string, string>[] {
                new KeyValuePair<string, string>("qr_code", qr_code),
                new KeyValuePair<string, string>("student_id", student_id),
                new KeyValuePair<string, string>("latitude", latitude),
                new KeyValuePair<string, string>("longitude", longitude)
            });
        }

        public static HttpResponseMessage ClassInfo(string[] class_uuids) {
            string temp;

            if (class_uuids.Length < 1) {
                temp = "[]";
            } else {
                var str_build = new StringBuilder();
                str_build.Append(string.Format("[\"{0}\"", class_uuids[0]));

                for (int i = 1; i < class_uuids.Length; i++) {
                    str_build.Append(string.Format(",\"{0}\"", class_uuids[i]));
                }

                str_build.Append("]");
                temp = str_build.ToString();
            }

            return SendPost(URL_CLASS_LOG_INFO, new KeyValuePair<string, string>[] {
                new KeyValuePair<string, string>("class_uuids", temp)
            });
        }
    }

    public static class Datasource {
        public static ILoginResult Login(string email, string password) {
            var result = AbsenceWebServic.Login(email, password);

            if (result.StatusCode != HttpStatusCode.OK) {
                return null;
            }

            var task = result.Content.ReadAsStringAsync();
            task.Wait();

            return JsonConvert.DeserializeObject<LoginResult>(task.Result);
        }

        public static bool Logout(string email, string device_uuid) {
            var result = AbsenceWebServic.Logout(email, device_uuid);

            if (result.StatusCode != HttpStatusCode.OK) {
                return false;
            }

            return true;
        }

        public static List<IClassEntity> GetClassList() {
            var result = new List<IClassEntity>();

            var temp = AbsenceWebServic.ClassList();

            var task = temp.Content.ReadAsStringAsync();
            task.Wait();

            JsonTextReader reader = new JsonTextReader(new StringReader(task.Result));

            string buffer = null;
            while (reader.Read())
                if (reader.Value != null) {
                    if (buffer is null) {
                        buffer = (string)reader.Value;
                    } else {
                        result.Add(new Class() {
                            id   = int.Parse(buffer),
                            name = (string)reader.Value
                        });

                        buffer = null;
                    }
                }

            return result;
        }

        public static List<IStatisticsEntity> GetStatistics(string device_uuid) {
            var result = new List<IStatisticsEntity>();

            var temp = AbsenceWebServic.Statistics(device_uuid);

            var task = temp.Content.ReadAsStringAsync();
            task.Wait();

            if (task.Result == "" || task.Result == "{\"subjects\":[]}") return result;

            JsonTextReader reader = new JsonTextReader(new StringReader(task.Result));
            while (reader.Read()) {
                //subjects
                if (reader.TokenType == JsonToken.PropertyName && 
                    (string)reader.Value == "subjects") {
                    var buffer_entity = new StatisticsEntity();

                    //subjects entity
                    while (reader.Read()) {
                        if (reader.TokenType == JsonToken.EndObject) break;

                        reader.Read();
                        buffer_entity.Subject = (string)reader.Value;
                        reader.Read();

                        string buffer_key = null;
                        while (reader.Read()) {
                            if (reader.TokenType == JsonToken.EndObject) break;

                            //class info
                            switch (reader.Value) {
                                case "_id":
                                    reader.Read();
                                    buffer_entity.SubjectId = Convert.ToInt32((long)reader.Value);
                                    break;
                                case "_stats":
                                    while (reader.Read()) {
                                        if (reader.TokenType == JsonToken.EndObject) break;

                                        if (reader.TokenType == JsonToken.PropertyName &&
                                                (string)reader.Value == "total") {
                                            reader.Read();
                                            buffer_entity.Total = Convert.ToInt32(
                                                (long)reader.Value);
                                        } else if (reader.TokenType == JsonToken.PropertyName &&
                                                (string)reader.Value == "absences") {
                                            reader.Read();
                                            buffer_entity.Absences = Convert.ToInt32(
                                                (long)reader.Value);
                                        }
                                    }
                                    break;
                                default:
                                    if (buffer_key is null) {
                                        buffer_key = (string)reader.Value;
                                    } else {
                                        buffer_entity.Class.Add(new KeyValuePair<string, bool>(
                                            buffer_key, (bool)reader.Value));
                                        buffer_key = null;
                                    }
                                    break;
                            }
                        }

                        result.Add(buffer_entity);
                    }
                }
            }
            reader.Close();

            return result;
        }

        public static List<IHistroryEntity> GetListHistrory(List<IStatisticsEntity> list) {
            var result               = new List<Histrory>();
            var temp_list            = new List<string>();
            //int[]: [0] => class, [1] => subject
            var class_subject_lookup = new List<KeyValuePair<int, int[]>>();
            var class_lookup         = new List<KeyValuePair<int, string>>();
            var subject_lookup       = new List<KeyValuePair<int, string>>();

            foreach (var meta in list) {
                foreach (var uuid in meta.Class) {
                    temp_list.Add(uuid.Key);
                }
            }

            var class_info = AbsenceWebServic.ClassInfo(temp_list.ToArray());
            if (class_info.StatusCode != HttpStatusCode.OK) {
                return null;
            }

            var task = class_info.Content.ReadAsStringAsync();
            task.Wait();

            JsonTextReader reader = new JsonTextReader(new StringReader(task.Result));
            while (reader.Read()) {
                if (reader.TokenType == JsonToken.EndObject) break;

                if (reader.TokenType == JsonToken.PropertyName) {
                    switch ((string)reader.Value) {
                        case "tbl_subject_class":
                            reader.Read();
                            while (reader.Read()) {
                                if (reader.TokenType == JsonToken.EndObject) break;
                                if (reader.TokenType == JsonToken.EndArray) break;
                                if (reader.TokenType == JsonToken.StartObject) continue;

                                var temp_class_subject_lookup = new KeyValuePair<int, int[]>(
                                    Convert.ToInt32(reader.Value), new int[2]
                                );
                                reader.Read();

                                while (reader.Read()) {
                                    if (reader.TokenType == JsonToken.EndObject) break;

                                    if ((string)reader.Value == "class") {
                                        reader.Read();
                                        temp_class_subject_lookup.Value[0] = Convert.ToInt32(
                                            (long)reader.Value
                                        );
                                    } else if ((string)reader.Value == "subject") {
                                        reader.Read();
                                        temp_class_subject_lookup.Value[1] = Convert.ToInt32(
                                            (long)reader.Value
                                        );
                                    }
                                }

                                class_subject_lookup.Add(temp_class_subject_lookup);
                            }
                            break;
                        case "tbl_class":
                            reader.Read();
                            while (reader.Read()) {
                                if (reader.TokenType == JsonToken.EndObject) break;
                                if (reader.TokenType == JsonToken.EndArray) break;

                                var temp_id = Convert.ToInt32((string)reader.Value);
                                reader.Read();
                                reader.Read();
                                reader.Read();
                                class_lookup.Add(new KeyValuePair<int, string>(temp_id, 
                                    (string)reader.Value));
                                reader.Read();
                            };
                            break;
                        case "tbl_subject":
                            reader.Read();
                            while (reader.Read()) {
                                if (reader.TokenType == JsonToken.EndObject) break;
                                if (reader.TokenType == JsonToken.EndArray) break;

                                var temp_id = Convert.ToInt32((string)reader.Value);
                                reader.Read();
                                reader.Read();
                                reader.Read();
                                subject_lookup.Add(new KeyValuePair<int, string>(temp_id,
                                    (string)reader.Value));
                                reader.Read();
                            };
                            break;
                        //case "teacher_by":
                        case "tbl_class_log":
                            //Class logs
                            reader.Read();

                            while (reader.Read()) {
                                if (reader.TokenType == JsonToken.EndObject) break;
                                if (reader.TokenType == JsonToken.EndArray) break;

                                //A class log
                                var temp = new Histrory {
                                    Uuid = (string)reader.Value
                                };
                                while (reader.Read()) {
                                    if (reader.TokenType == JsonToken.EndObject) break;
                                    if (reader.TokenType == JsonToken.StartObject) continue;

                                    switch ((string)reader.Value) {
                                        case "subject_class":
                                            reader.Read();
                                            temp.Class = reader.Value.ToString();
                                            break;
                                        case "unix_time":
                                            reader.Read();
                                            temp.Unixtime = Convert.ToInt32((long)reader.Value);
                                            break;
                                        //case "weight":
                                        //case "teacher_by":
                                        default:
                                            reader.Read();
                                            break;
                                    }
                                }

                                result.Add(temp);
                            }
                            break;
                        default:
                            var count = 0;
                            while (reader.Read()) {
                                switch (reader.TokenType) {
                                    case JsonToken.StartObject:
                                    case JsonToken.StartArray:
                                    case JsonToken.StartConstructor:
                                        count++;
                                        break;
                                    case JsonToken.EndObject:
                                    case JsonToken.EndArray:
                                    case JsonToken.EndConstructor:
                                    case JsonToken.Date:
                                        count--;
                                        break;
                                    default:
                                        break;
                                }

                                if (count < 1) {
                                    break;
                                }
                            }
                            break;
                    }
                }
            }
            reader.Close();

            foreach (var item in result) {
                var done = false;
                foreach (var meta in list) {
                    if (done) break;

                    foreach (var uuid in meta.Class) {
                        if (done) break;
                        if (item.Uuid == uuid.Key) {
                            done = true;

                            item.Subject = meta.Subject;
                            item.Absence = uuid.Value;

                            DateTime dt = new DateTime(1970, 1, 1, 0, 0, 0, 0, DateTimeKind.Utc);
                            dt = dt.AddSeconds(item.Unixtime).ToLocalTime();

                            item.Time = dt.ToString("HH:mm");
                            item.Date = dt.ToString(@"dd/MM-yyyy");

                            var temp_date = int.Parse(item.Class);
                            foreach (var at_class_subject in class_subject_lookup) {
                                if (temp_date == at_class_subject.Key) {
                                    foreach (var at_class in class_lookup) {
                                        if (at_class_subject.Value[0] == at_class.Key) {
                                            item.Class = at_class.Value;
                                            break;
                                        }
                                    }

                                    foreach (var at_subject in subject_lookup) {
                                        if (at_class_subject.Value[1] == at_subject.Key) {
                                            item.Subject = at_subject.Value;
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            result.Sort();

            return new List<IHistroryEntity>(result);
        }

        public static IQrcodeValidateResult Presence(string qr_code, string student_id, 
            string latitude, string longitude) {
            var result = new QrcodeValidateResult();

            var temp = AbsenceWebServic.Presence(qr_code, student_id, latitude, longitude);

            if (!temp.IsSuccessStatusCode) {
                return null;
            }

            var task = temp.Content.ReadAsStringAsync();
            task.Wait();

            JsonTextReader reader = new JsonTextReader(new StringReader(task.Result));
            while (reader.Read()) {
                if (reader.TokenType == JsonToken.EndObject) break;
                if (reader.TokenType == JsonToken.StartObject) continue;

                var value_type = (string)reader.Value;
                reader.Read();
                if (value_type == "validate_qrcode") {
                    result.ValidateQrcode = (bool)reader.Value;
                } else if (value_type == "result") {
                    result.Result = (bool)reader.Value;
                } else if (value_type == "class_uuid") {
                    result.ClassUuid = (string)reader.Value;
                }
            }

            temp = AbsenceWebServic.ClassInfo(new string[] { result.ClassUuid });

            if (!temp.IsSuccessStatusCode) {
                result.Subject = "n/a";
                result.Class   = "n/a";
                return result;
            }

            task = temp.Content.ReadAsStringAsync();
            task.Wait();

            reader = new JsonTextReader(new StringReader(task.Result));
            while (reader.Read()) {
                if (reader.TokenType == JsonToken.EndObject) break;
                if (reader.TokenType == JsonToken.StartObject) continue;

                switch ((string)reader.Value) {
                    case "tbl_class":
                        for (int i = 0; i < 7; i++) {
                            reader.Read();
                            if (i == 4) result.Class = (string)reader.Value;
                        }
                        break;
                    case "tbl_subject":
                        for (int i = 0; i < 7; i++) {
                            reader.Read();
                            if (i == 4) result.Subject = (string)reader.Value;
                        }
                        break;
                    default:
                        var count = 0;
                        while (reader.Read()) {
                            switch (reader.TokenType) {
                                case JsonToken.StartObject:
                                case JsonToken.StartArray:
                                case JsonToken.StartConstructor:
                                    count++;
                                    break;
                                case JsonToken.EndObject:
                                case JsonToken.EndArray:
                                case JsonToken.EndConstructor:
                                case JsonToken.Date:
                                    count--;
                                    break;
                                default:
                                    break;
                            }

                            if (count < 1) {
                                break;
                            }
                        }
                        break;
                }
            }

            return result;
        }
    }
}
