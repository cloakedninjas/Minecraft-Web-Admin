define(['backbone'], function(Backbone){
  return Backbone.View.extend({
    render: function () {
      this.$el.html(this.template());
      return this;
    },

    template: function () {
      // overwrite in child views
    }
  });

});
