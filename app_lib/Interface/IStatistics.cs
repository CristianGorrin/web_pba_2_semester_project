using System;
using System.Collections.Generic;
using System.Text;

namespace app_lib.Interface {
    public interface IStatisticsEntity {
        string Subject { get; }
        int SubjectId { get; }
        int Total { get; }
        int Absences { get; }
        List<KeyValuePair<string, bool>> Class { get; }
    }
}
