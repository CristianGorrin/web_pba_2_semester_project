using app_lib;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Xamarin.Forms;

namespace app {
    public partial class App : Application {
        public App() {
            InitializeComponent();
            MainPage = new Page.LoginPage();
        }

        protected override void OnStart() {
            // Handle when your app starts
        }

        protected override void OnSleep() {
            // Handle when your app sleeps
        }

        protected override void OnResume() {
            //Testing if the Session.Cache needs to be update
            var day = DateTime.Now.Day;
            
            if (Session.LastUpdateDay > 1 || day > Session.LastUpdateDay) {
                MainPage = new Page.LoginPage();
            } else if (Session.LastUpdateDay != 1 && day == 1) {
                MainPage = new Page.LoginPage();
            } else if (Session.Cache == null) {
                MainPage = new Page.LoginPage();
            } else if (Session.DeviceUuid == null) {
                MainPage = new Page.LoginPage();
            }
        }
    }
}
