using Xamarin.Forms;
using Xamarin.Forms.Xaml;

using ZXing.Net.Mobile.Forms;

namespace app.Page {
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class ScannerPage : ContentPage {
        private ZXingScannerView m_zxing;

        public ScannerPage() {
			InitializeComponent();
        }

        private void DoResult(ZXing.Result result) => Device.BeginInvokeOnMainThread(async () => {
            await Navigation.PushModalAsync(new ScanneResultPage(result.Text));
        });

        private void CreateQrScanner() {
            m_zxing = new ZXingScannerView {
                HorizontalOptions = LayoutOptions.FillAndExpand,
                VerticalOptions   = LayoutOptions.FillAndExpand
            };

            m_zxing.OnScanResult += DoResult;

            var grid = new Grid {
                VerticalOptions   = LayoutOptions.FillAndExpand,
                HorizontalOptions = LayoutOptions.FillAndExpand
            };
            grid.Children.Add(m_zxing);

            Content = grid;
        }

        protected override void OnAppearing() {
            base.OnAppearing();
            CreateQrScanner();
            m_zxing.IsScanning = true;
        }

        protected override void OnDisappearing() {
            m_zxing.IsScanning = false;
            base.OnDisappearing();
        }
    }
}
