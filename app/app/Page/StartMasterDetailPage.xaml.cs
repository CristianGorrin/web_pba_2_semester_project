﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace app.Page {
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class StartMasterDetailPage : MasterDetailPage {
        private static StartMasterDetailPage m_current;

        public StartMasterDetailPage() {
            InitializeComponent();
            MasterPage.m_list_view.ItemSelected += ListView_ItemSelected;
            m_current = this;
        }

        public static void PopupMenu() {
            m_current.IsPresented = true;
        }

        private void ListView_ItemSelected(object sender, SelectedItemChangedEventArgs e) {
            var item = e.SelectedItem as StartMasterDetailPageMenuItem;
            if (item == null)
                return;

            var page = (Xamarin.Forms.Page)Activator.CreateInstance(item.TargetType);
            page.Title = item.Title;

            Detail = new NavigationPage(page);
            IsPresented = false;

            MasterPage.m_list_view.SelectedItem = null;
        }
    }
}