using app_lib;
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

            m_list = new List<StatiscsItem>();
            int total = 0, absence = 0;
            foreach (var item in Session.CacheStatistics) {
                total   += item.Total;
                absence += item.Absences;

                m_list.Add(new StatiscsItem() {
                    subject = item.Subject,
                    total   = item.Total.ToString("F0"),
                    procent = item.Absences == 0 ? 
                        "0" : (100 / ((double)item.Total / item.Absences)).ToString("F2")
                });
            }

            lab_total.Text = string.Format("Total lecture: {0} units", total.ToString("F0"));

            lab_absence_total.Text = string.Format("Total absence: {0} units", 
                absence.ToString("F0"));

            lab_absence_procent.Text = string.Format("Procent absence: {0} %", 
                absence == 0 ? "0" : (100 / ((double)total / absence)).ToString("F2"));

            list_view_statiscs.ItemsSource = m_list;
        }

        private void ItemSelected(object sender, SelectedItemChangedEventArgs e) {
            ((ListView)sender).SelectedItem = null;
        }

        protected override bool OnBackButtonPressed() {
            StartMasterDetailPage.PopupMenu();
            return true;
        }
    }

    struct StatiscsItem {
        public string subject { get; set; }
        public string total { get; set; }
        public string procent { get; set; }
    }
}