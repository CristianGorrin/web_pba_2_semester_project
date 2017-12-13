using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.ComponentModel;
using System.Linq;
using System.Runtime.CompilerServices;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace app.Page {
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class StartMasterDetailPageMaster : ContentPage {
        public ListView m_list_view;

        public StartMasterDetailPageMaster() {
            InitializeComponent();
            
            lab_user_full_name.Text = app_lib.Session.FullName;

            BindingContext = new StartMasterDetailPageMasterViewModel();
            m_list_view    = MenuItemsListView;

            lab_logout.GestureRecognizers.Add(new TapGestureRecognizer {
                Command = new Command(async () => {
                    var result = await DisplayAlert(
                        "Logout", 
                        "Do you really want to Logout?", 
                        "Yes", "No"
                    );

                    if (result) {
                        app_lib.Session.Logout();

                        App.Current.MainPage = new LoginPage();
                    }
                })
            });

            lab_about.GestureRecognizers.Add(new TapGestureRecognizer {
                Command = new Command(async () => {
                    await Navigation.PushModalAsync(new AboutPage());
                })
            });
        }

        class StartMasterDetailPageMasterViewModel : INotifyPropertyChanged {
            public ObservableCollection<StartMasterDetailPageMenuItem> MenuItems { get; set; }

            public StartMasterDetailPageMasterViewModel() {
                MenuItems = new ObservableCollection<StartMasterDetailPageMenuItem>(new[] {
                    new StartMasterDetailPageMenuItem {
                        Id         = 0,
                        Title      = "QR scanner",
                        TargetType = typeof(ScannerPage)
                    },
                    new StartMasterDetailPageMenuItem {
                        Id = 1,
                        Title = "History",
                        TargetType = typeof(HistroryPage)
                    },
                    new StartMasterDetailPageMenuItem {
                        Id = 2,
                        Title = "Statistics",
                        TargetType = typeof(StatisticsPage)
                    }
                });
            }

            #region INotifyPropertyChanged Implementation
            public event PropertyChangedEventHandler PropertyChanged;
            void OnPropertyChanged([CallerMemberName] string propertyName = "") {
                if (PropertyChanged == null)
                    return;

                PropertyChanged.Invoke(this, new PropertyChangedEventArgs(propertyName));
            }
            #endregion
        }
    }
}