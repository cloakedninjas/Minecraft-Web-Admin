define(['backbone', 'dashboard-view'], function(Backbone, DashboardView){
  'use strict';

  return Backbone.Router.extend({
    $el: null,
    currentView: null,

    initialize: function(el) {
      this.$el = el;
      Backbone.history.start();
    },

    routes: {
      // Default
      '': 'showDashboard'
    },

    switchView: function (view) {
      if (this.currentView !== null) {
        this.currentView.remove();
      }

      this.$el.html(view.render().$el);

      this.currentView = view;
    },

    showIndex: function () {
      console.log('index page');
    },

    showDashboard: function () {
      this.switchView(new DashboardView());
    }
  });

});
