define(['backbone'], function(Backbone) {
  return Backbone.Model.extend({
    url: '/setup',
    defaults: {
      title: 'empty model'
    },

    initialize: function() {

    }
  });
});