define(['base-view', 'default-router'], function(BaseView, Router){
  'use strict';

  return BaseView.extend({
    el: $('#app'),

    initialize: function () {
      this._router = new Router(this.$el);
    }
  });
});
