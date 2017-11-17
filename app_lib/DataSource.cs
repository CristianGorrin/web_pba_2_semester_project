using System;
using System.Collections.Generic;
using app_lib.DataStructure;

namespace app_lib.Datasource {
    static class AbsenceWebServic {

    }

   public static class Datasource {
        public static List<IHistroryEntity> GetListHistrory() {
            var result = new List<IHistroryEntity>();

            for (int i = 0; i < 10; i++) {
                result.Add(new Histrory() {
                    Class   = "_class_" + i,
                    Subject = "_subject_" + i,
                    Date    = "_date_" + i,
                    Time    = "_time_" + i,
                    Uuid    = "_uuid_" + i,
                    Absence = i % 2 == 0
                });
            }

            return result;
        }
   }
}
