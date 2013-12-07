define(['base-view', 'default-router', 'backbone'], function(BaseView, Router, Backbone){
  'use strict';

  return BaseView.extend({
    el: $('#app'),

    initialize: function () {
      this._router = new Router(this.$el);
      Backbone.history.start({ pushState: true });
    }
  });
});
