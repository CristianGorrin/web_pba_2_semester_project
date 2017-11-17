using app_lib;
using app_lib.Datasource;
using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.ComponentModel;
using System.Runtime.CompilerServices;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace app.Page
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class HistroryPage : ContentPage	{
        private List<IHistroryEntity> m_list_history;

        public HistroryPage () {
			InitializeComponent ();

            m_list_history                = Datasource.GetListHistrory();
            list_view_history.ItemsSource = m_list_history;

            Title = m_list_history.Count.ToString();
        }

        private void ListItemSelected(object sender, SelectedItemChangedEventArgs e) {
            ((ListView)sender).SelectedItem = null;
        }
    }
}