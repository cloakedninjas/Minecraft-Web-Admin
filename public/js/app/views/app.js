define(['base-view', 'default-router', 'backbone'], function(BaseView, Router, Backbone){
  'use strict';

  return BaseView.extend({
    el: $('#app'),

    initialize: function () {
      this._router = new Router(this.$el);
      Backbone.history.start({ pushState: true });
    },

    showLightbox: function (content, title) {
      $('body').append('<div class="lb-blackout"></div><div class="lb-content"><h2>' + title + '</h2></div>');
      $('.lb-content').append(content.render().$el);
    },

    hideLightbox: function () {
      $('.lb-blackout,.lb-content').remove();
    }
  });
});
