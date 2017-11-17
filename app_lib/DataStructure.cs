using System;
using System.Collections.Generic;
using System.Text;

namespace app_lib.DataStructure {
    public class Histrory : IHistroryEntity {
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
    }
}
