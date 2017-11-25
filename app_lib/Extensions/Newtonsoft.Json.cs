using Newtonsoft.Json;

namespace app_lib.Extensions {
    public static class ExtensionNewtonsoft {
        public static void Skip(ref JsonTextReader reader) {
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
                        count--;
                        break;
                    default:
                        break;
                }

                if (count < 0) break;
            }
        }
    }
}
