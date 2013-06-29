define(['underscore', 'backbone', 'text!templates/setup/step1.html'], function(_, Backbone, Step1Template) {
  return Backbone.View.extend({

    el: $('#content'),
    template: Step1Template,

    initialize: function() {
      console.log('setup view init');
    },

    render: function(eventName) {
      this.$el.html(_.template( Step1Template, {} ));
      return this;
    }

  });
});
