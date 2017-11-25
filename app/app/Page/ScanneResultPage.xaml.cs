using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;


using Datasource = app_lib.Datasource.Datasource;
using app_lib;
using app_lib.Interface;

namespace app.Page {
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class ScanneResultPage : ContentPage {
        private IQrcodeValidateResult m_result;
        private string m_qr_code;

        public ScanneResultPage(string qrcode) {
            m_qr_code = qrcode;
            InitializeComponent();
        }

        protected override void OnAppearing() {
            base.OnAppearing();
            new Task(DoWork).Start();
        }

        private async void DoWork() {
            if (m_result == null) {
                var locat = await Geolocator.GetCurrentLocation();
                if (locat != null) {
                    m_result = Datasource.Presence(m_qr_code, Session.AccId.ToString(),
                        Convert.ToString(locat.Latitude), Convert.ToString(locat.Longitude));
                } else {
                    m_result = Datasource.Presence(m_qr_code, Session.AccId.ToString(), "-1", 
                        "-1");
                }

                Device.BeginInvokeOnMainThread(() => {
                    if (m_result.Result) {
                        txt_result.Text = "You are now check in";

                    } else {
                        if (m_result.ValidateQrcode) {
                            txt_result.Text = "We are having problems check you in...";
                        } else {
                            txt_result.Text = "The qr code you used is not valid...";
                        }
                    }

                    if (m_result.ValidateQrcode) {
                        txt_details.Text = string.Format("Class: {0}\nSubject: {1}", 
                            m_result.Class, m_result.Subject);
                    }

                    sl_aci.IsVisible      = false;
                    grid_result.IsVisible = true;
                });

                if (m_result.Result) {
                    Session.AddToCacheHistrory(m_result.ClassUuid, m_result.Class, 
                        m_result.Subject);
                }
            }
        }

        private void BtnCloseClicked(object sender, EventArgs e) {
            Navigation.PopModalAsync();
        }
    }
}
