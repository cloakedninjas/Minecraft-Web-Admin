define(['backbone'], function(Backbone) {
  'use strict';
  return Backbone.Model.extend({

    defaults: {
      step: 1,
      errors: {}
    },

    url: function() {
      return '/setup';
    }

  });
});