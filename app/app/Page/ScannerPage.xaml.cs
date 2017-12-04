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

        protected override bool OnBackButtonPressed() {
            StartMasterDetailPage.PopupMenu();
            return true;
        }

        private void CreateQrScanner() {
            m_zxing = new ZXingScannerView {
                HorizontalOptions = LayoutOptions.FillAndExpand,
                VerticalOptions   = LayoutOptions.FillAndExpand
            };

            var lab      = new Label();
            var grid_lab = new Grid() {
                VerticalOptions   = LayoutOptions.FillAndExpand,
                HorizontalOptions = LayoutOptions.FillAndExpand,
                RowDefinitions    = new RowDefinitionCollection() {
                    new RowDefinition() { Height = new GridLength(1, GridUnitType.Star) },
                    new RowDefinition() { Height = new GridLength(40, GridUnitType.Absolute) }
                }
            };

            lab.Text                    = "Scan the qr code to check in";
            lab.HorizontalTextAlignment = TextAlignment.Center;
            lab.VerticalTextAlignment   = TextAlignment.Center;
            lab.BackgroundColor         = new Color(255, 255, 255, 0.3);
            lab.FontSize                = 24;
            lab.SetValue(Grid.RowProperty, 1);

            grid_lab.Children.Add(lab);

            m_zxing.OnScanResult += DoResult;

            var grid = new Grid {
                VerticalOptions   = LayoutOptions.FillAndExpand,
                HorizontalOptions = LayoutOptions.FillAndExpand
            };
            grid.Children.Add(m_zxing);
            grid.Children.Add(grid_lab);

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
