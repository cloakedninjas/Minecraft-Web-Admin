define(['base-view', 'text!app/templates/server-create.html'], function(BaseView, createTemplate) {
  'use strict';

  return BaseView.extend({
    tagName: 'form',
    className: 'server-create',

    template: _.template(createTemplate),

    events: {
      'click .submit input': 'postForm'
    },

    postForm: function () {
      this.trigger('post', {
        name: $.trim(this.$('.name input').val())
      });
    }

  });

});
