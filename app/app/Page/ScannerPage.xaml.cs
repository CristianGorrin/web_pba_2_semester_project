using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;
using ZXing.Net.Mobile.Forms;

namespace app.Page {
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class ScannerPage : ContentPage {
        public ScannerPage () {
			InitializeComponent ();

            zxsv.OnScanResult += (result) =>
                Device.BeginInvokeOnMainThread(async () => {
                    zxsv.IsAnalyzing = false;

                    await DisplayAlert("Scanned Barcode", result.Text, "OK");

                    zxsv.IsAnalyzing = true;
                });
        }

        protected override void OnAppearing() {
            base.OnAppearing();

            zxsv.IsScanning = true;
        }

        protected override void OnDisappearing() {
            zxsv.IsScanning = false;

            base.OnDisappearing();
        }
    }
}