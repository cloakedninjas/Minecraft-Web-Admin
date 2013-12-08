define(['base-view', 'text!app/templates/server-list.html', 'create-server-view'], function(BaseView, listTemplate, CreateServerView) {
  'use strict';

  return BaseView.extend({
    className: 'list-control server-list',

    template: _.template(listTemplate),

    events: {
      'click .actions .create': 'showCreateForm'
    },

    showCreateForm: function () {
      var createForm = new CreateServerView();
      App.showLightbox(createForm, 'Create new server');
    },

    serialize: function() {
      return {
        servers: [
          {
            name: 'Foo',
            playerCount: 2,
            playerLimit: 20
          },
          {
            name: 'Bar',
            playerCount: 0,
            playerLimit: 15
          }
        ]
      };
    }
  });

});
