define(['backbone', 'server-model'], function(Backbone, Server){
  return Backbone.Collection.extend({
    url: '/api/servers',
    model: Server

  });

});
