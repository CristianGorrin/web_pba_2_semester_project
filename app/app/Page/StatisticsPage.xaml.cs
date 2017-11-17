using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace app.Page
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class StatisticsPage : ContentPage {
        private List<StatiscsItem> m_list;
       
		public StatisticsPage () {
            InitializeComponent ();

            lab_total.Text   = string.Format("Total absence: {0} lectures", "34");
            lab_procent.Text = string.Format("Procent absence: {0} %", "60");

            m_list = new List<StatiscsItem>();
            m_list.Add(new StatiscsItem() {
                subject = "test_subject0",
                total   = "test_total0",
                procent = "test_procent0"
            });
            m_list.Add(new StatiscsItem() {
                subject = "test_subject1",
                total   = "test_total1",
                procent = "test_procent1"
            });

            list_view_statiscs.ItemsSource = m_list;
        }

        private void ItemSelected(object sender, SelectedItemChangedEventArgs e) {
            ((ListView)sender).SelectedItem = null;
        }
    }

    struct StatiscsItem {
        public string subject { get; set; }
        public string total { get; set; }
        public string procent { get; set; }
    }
}