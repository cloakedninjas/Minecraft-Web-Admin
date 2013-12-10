define(['base-view', 'text!app/templates/server-list.html', 'server-collection', 'create-server-view'], function(BaseView, listTemplate, ServerCollection, CreateServerView) {
  'use strict';

  return BaseView.extend({
    className: 'list-control server-list',

    template: _.template(listTemplate),

    events: {
      'click .actions .create': 'showCreateForm'
    },

    serverCollection: null,

    initialize: function () {
      this.serverCollection = new ServerCollection();
      this.serverCollection.on('add', this.render, this);

      this.serverCollection.fetch();
    },

    showCreateForm: function () {
      var createForm = new CreateServerView();

      this.listenTo(createForm, 'post', this.handleCreateFormPost);

      App.showLightbox(createForm, {
        title: 'Create new server',
        cssClass: 'create-server-dialog'
      });
    },

    handleCreateFormPost: function (data) {
      console.log(data);
      //this.serverCollection.create(data);
    },

    serialize: function() {
      return {
        servers: this.serverCollection.toJSON()
      };

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
