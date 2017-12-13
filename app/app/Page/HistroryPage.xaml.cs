using app_lib;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace app.Page
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class HistroryPage : ContentPage	{
        public HistroryPage () {
			InitializeComponent();
            list_view_history.ItemsSource = Session.CacheHistrory;
        }

        private void ListItemSelected(object sender, SelectedItemChangedEventArgs e) {
            ((ListView)sender).SelectedItem = null;
        }

        protected override bool OnBackButtonPressed() {
            StartMasterDetailPage.PopupMenu();
            return true;
        }   
    }
}