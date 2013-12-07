define(['base-view', 'text!app/templates/layouts/logged-in.html'], function(BaseView, loggedInTemplate){
  'use strict';

  return BaseView.extend({
    className: 'logged-in',
    template: _.template(loggedInTemplate),

    events: {
      'click header h1': 'showDashboard',
      'click .dashboard': 'showDashboard',
      'click .servers': 'showServers',
      'click .settings': 'showSettings',
      'click header .logout': 'logout'
    },

    showDashboard: function () {
      Backbone.history.navigate('dashboard');
    },

    showServers: function () {
      Backbone.history.navigate('servers');
    },

    showSettings: function () {
      Backbone.history.navigate('settings');
    },

    logout: function () {
      document.location.href = '/logout';
    }


  });

});
