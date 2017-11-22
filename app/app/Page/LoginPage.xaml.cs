using System;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

using app_lib;

namespace app.Page {
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class LoginPage : ContentPage {
        public LoginPage() {
            InitializeComponent();

            DisableGui();
            Task.Run(() => {
                if (Paths.FileExist(Paths.SessionFile)) {
                    Session.Load();
                    if (Session.Validate()) {
                        Device.BeginInvokeOnMainThread(() => {
                            App.Current.MainPage = new StartMasterDetailPage();
                        });
                    } else {
                        Session.Logout();
                    }
                }
                EnableGui();
            });
        }

        private void DisableGui() {
            Device.BeginInvokeOnMainThread(() => {
                aci_login.IsRunning    = true;
                btn_login.IsEnabled    = false;
                txt_password.IsEnabled = false;
                txt_email.IsEnabled    = false;
            });
        }

        private void EnableGui() {
            Device.BeginInvokeOnMainThread(() => {
                aci_login.IsRunning    = false;
                btn_login.IsEnabled    = true;
                txt_password.IsEnabled = true;
                txt_email.IsEnabled    = true;
            });
        }

        protected override void OnAppearing() {
            if (Session.DeviceUuid != null) {
                //TODO test if DeviceUuid is valide - if not then goto loginpage

                var day = DateTime.Now.Day;
                if (Session.LastUpdateDay > 1 || day > Session.LastUpdateDay || 
                    (Session.LastUpdateDay != 1 && day == 1)) {
                    DisableGui();

                    Session.Update();

                    app.App.Current.MainPage = new StartMasterDetailPage();
                }
            }
        }

        private void ClickLogin(object sender, EventArgs e) {
            DisableGui();
            new Task(() => {
                if (string.IsNullOrEmpty(txt_password.Text) || string.IsNullOrEmpty(txt_email.Text)) {
                    Device.BeginInvokeOnMainThread(async () => {
                        await DisplayAlert(
                            "The password and email must be filled out",
                            "Please try again.",
                            "OK");
                    });

                    txt_password.Text = "";
                    EnableGui();
                    return;
                }

                if (Session.Login(txt_email.Text, txt_password.Text)) {
                    Device.BeginInvokeOnMainThread(() => {
                        app.App.Current.MainPage = new StartMasterDetailPage();
                    });
                } else {
                    Device.BeginInvokeOnMainThread(async () => {
                        aci_login.IsRunning = false;

                        await DisplayAlert(
                            "Wrong password or email?",
                            "We can't log you in... please try again.",
                            "OK");

                        txt_password.Text = "";
                        txt_password.Focus();
                        EnableGui();
                    });
                }
            }).Start();
        }
    }
}
