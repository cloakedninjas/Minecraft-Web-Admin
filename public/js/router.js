define(['backbone', 'app', 'views/login', 'views/setup'], function(Backbone, App, LoginView, SetupView){
  return Backbone.Router.extend({
    initialize: function() {
      Backbone.history.start();
    },

    routes: {
      // Define some URL routes
      '/login': 'showLogin',
      'setup': 'showSetup',

      // Default
      '': 'showIndex'
      //'*actions': 'defaultRoute'
    },

    showIndex: function () {
      console.log('default route says hello');

      var app = require('app');

      if (app.getState('setup')) {
        // check logged in
      }
      else {
        this.navigate("/setup", {trigger: true});
      }

    },

    showSetup: function() {
      var view = new SetupView();
      view.render();
    },

    showLogin: function(){
      var view = new LoginView();
      view.render();
    }
  });

});
