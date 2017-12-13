namespace app_lib.Interface {
    public interface IHistroryEntity {
        string Class { get; }
        string Date { get;  }
        string Subject { get; }
        string Time { get; }
        string Uuid { get; }
        bool Absence { get; }
    }
}
