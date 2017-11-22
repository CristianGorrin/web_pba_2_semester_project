using System;
using System.Collections.Generic;
using System.Text;

namespace app_lib.Interface {
    public interface IQrcodeValidateResult {
        bool ValidateQrcode { get; }
        bool Result { get; }
        string ClassUuid { get; }
        string Class { get; }
        string Subject { get; }
    }
}
