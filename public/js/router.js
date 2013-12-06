define(['backbone', 'app'], function(Backbone, App){
  return Backbone.Router.extend({
    initialize: function() {
      Backbone.history.start();
    },

    routes: {
      // Default
      '': 'showIndex'
    },

    showIndex: function () {
      var app = require('app');
      
      console.log(1);


    }
  });

});
