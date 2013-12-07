define(['backbone'], function(Backbone){
  return Backbone.View.extend({
    template: _.template(''),

    serialize: function() {
      return {};
    },

    render: function() {
      this.$el.html(this.template(this.serialize()));

      return this;
    }
  });

});
