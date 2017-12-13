using System;

using Android.App;
using Android.Content.PM;
using Android.Runtime;
using Android.Views;
using Android.Widget;
using Android.OS;

using app_lib;

namespace app.Droid {
    [Activity(
        Label = "app", 
        Icon = "@drawable/icon", 
        Theme = "@style/MainTheme", 
        MainLauncher = true, 
        ConfigurationChanges = ConfigChanges.ScreenSize | ConfigChanges.Orientation)]
    public class MainActivity : global::Xamarin.Forms.Platform.Android.FormsAppCompatActivity {
        protected override void OnCreate(Bundle bundle) {
            TabLayoutResource = Resource.Layout.Tabbar;
            ToolbarResource = Resource.Layout.Toolbar;
            
            ZXing.Net.Mobile.Forms.Android.Platform.Init();

            base.OnCreate(bundle);

            global::Xamarin.Forms.Forms.Init(this, bundle);

            SetupPaths();
            SetupGeolocator();

            LoadApplication(new App());
        }

        public override void OnRequestPermissionsResult(int requestCode, string[] permissions,
            Permission[] grantResults) {
            global::ZXing.Net.Mobile.Android.PermissionsHandler.OnRequestPermissionsResult(
                requestCode, permissions, grantResults
            );

            Plugin.Permissions.PermissionsImplementation.Current.OnRequestPermissionsResult(
                requestCode, permissions, grantResults
            );
        }

        private void SetupPaths() {
            Paths.GetStream = new Func<string, Paths.StreamType, System.IO.Stream>((path, type) => {
                try {
                    if (type == Paths.StreamType.Input) {
                        return BaseContext.OpenFileInput(path);
                    } else if (type == Paths.StreamType.Output) {
                        return BaseContext.OpenFileOutput(path,
                            Android.Content.FileCreationMode.Private);
                    } else {
                        return null;
                    }
                } catch (Exception) {
                    return null;
                }
            });

            Paths.FileExist = new Func<string, bool>((path) => {
                foreach (var item in BaseContext.FileList())
                    if (item == path) return true;

                return false;
            });

            Paths.DeleteFile = new Func<string, bool>((path) => {
                return BaseContext.DeleteFile(path);
            });
        }


        #region Geolocator
        private void SetupGeolocator() {
            Plugin.Geolocator.CrossGeolocator.Current.DesiredAccuracy = 10;
        }
        #endregion
    }
}
