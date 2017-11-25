using System;
using Plugin.Geolocator;
using Plugin.Geolocator.Abstractions;
using System.Threading.Tasks;

namespace app_lib {
    public static class Geolocator {
        public static async Task<Position> GetCurrentLocation() {
            try {
                var locator             = CrossGeolocator.Current;
                locator.DesiredAccuracy = 10;

                return await locator.GetLastKnownLocationAsync();
            } catch (Exception) {
                return null;
            }
        }
    }
}
