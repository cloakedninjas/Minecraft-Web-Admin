define(['backbone', 'logged-in-layout', 'dashboard-view', 'server-list-view'], function(Backbone, LoggedInLayout, DashboardView, ServerListView){
  'use strict';

  return Backbone.Router.extend({
    $el: null,
    currentView: null,
    layout: null,

    initialize: function(el) {
      this.$el = el;

      this.layout = new LoggedInLayout();
      this.$el.html(this.layout.render().$el);
    },

    routes: {
      // Default
      '': 'showDashboard',
      'dashboard': 'showDashboard',
      'servers': 'showServers'
    },

    switchView: function (view) {
      if (this.currentView !== null) {
        this.currentView.remove();
      }

      this.currentView = view;

      // in lieu of a proper layout manager, inject the view into our layout
      this.layout.$('#content').html(view.render().$el);
    },

    showDashboard: function () {
      this.switchView(new DashboardView());
    },

    showServers: function () {
      this.switchView(new ServerListView());
    }
  });

});
