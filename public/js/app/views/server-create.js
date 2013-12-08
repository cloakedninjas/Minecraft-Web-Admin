define(['base-view', 'text!app/templates/server-create.html'], function(BaseView, createTemplate) {
  'use strict';

  return BaseView.extend({
    className: 'server-create',

    template: _.template(createTemplate),

    events: {
    },

  });

});
