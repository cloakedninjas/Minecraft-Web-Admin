define(['underscore', 'base-view', 'default-router', 'backbone'], function(_, BaseView, Router, Backbone){
  'use strict';

  return BaseView.extend({
    el: $('#app'),

    initialize: function () {
      this._router = new Router(this.$el);
      Backbone.history.start({ pushState: true });
    },

    /**
     * TODO: refactor into its own View
     */
    showLightbox: function (content, options) {
      options = _.extend({
        title: null,
        cssClass: '',
        closeButton: true
      }, options);

      $('body').append('<div class="lightbox"><div class="lb-blackout"></div><div class="lb-content ' + options.cssClass + '"><h2>' + options.title + '</h2></div></div>');
      $('.lb-content').append(content.render().$el);

      if (options.closeButton) {
        $('.lb-content h2').append('<a class="close"></a>');
        $('.lb-content h2 .close').on('click', function () {
          App.hideLightbox();
        })
      }
    },

    hideLightbox: function () {
      $('.lightbox').remove();
    }
  });
});
