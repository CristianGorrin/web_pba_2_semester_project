using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace app.Page {
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class LoginPage : ContentPage {
        public LoginPage() {
            InitializeComponent();
        }

        private async void ClickLogin(object sender, EventArgs e) {
            ((Button)sender).IsEnabled = false;
            txt_password.IsEnabled     = false;
            txt_email.IsEnabled        = false;

            aci_login.IsRunning = true;

            //TODO remove await
            await Task.Delay(2000);

            //TODO login

            app.App.Current.MainPage = new StartMasterDetailPage();
        }
    }
}