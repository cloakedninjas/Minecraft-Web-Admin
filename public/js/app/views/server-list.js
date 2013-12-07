define(['base-view', 'text!app/templates/server-list.html'], function(BaseView, listTemplate) {
  'use strict';

  return BaseView.extend({
    className: 'list-control server-list',

    template: _.template(listTemplate),

    initialize: function () {
      //this.servers = new Backbone.Collection({})
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
