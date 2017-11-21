using app_lib.Interface;
using System;
using System.Collections.Generic;

namespace app_lib.DataStructure {
    public class Histrory : IHistroryEntity, IComparable<Histrory> {
        public Histrory() {
            Class   = "";
            Date    = "";
            Subject = "";
            Time    = "";
            Uuid    = "";
            Absence = false;
        }

        public string Class { get; set; }
        public string Date { get; set; }
        public string Subject { get; set; }
        public string Time { get; set; }
        public string Uuid { get; set; }
        public bool Absence { get; set; }

        public int Unixtime { get; set; }

        public int CompareTo(Histrory other) => Unixtime - other.Unixtime;
    }

    public class LoginResult : ILoginResult {
        public bool result { get; set; }
        public string device_uuid { get; set; }
        public int acc_id { get; set; }
    }

    public class Class : IClassEntity {
        public int id { get; set; }
        public string name { get; set; }
    }

    public class StatisticsEntity : IStatisticsEntity {
        public StatisticsEntity() {
            Class = new List<KeyValuePair<string, bool>>();
        }
        public string Subject { get; set; }
        public int SubjectId { get; set; }
        public int Total { get; set; }
        public int Absences { get; set; }
        public List<KeyValuePair<string, bool>> Class { get; set; }
    }
}
