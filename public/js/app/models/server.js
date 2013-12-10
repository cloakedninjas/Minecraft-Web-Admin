define(['backbone'], function(Backbone){
  return Backbone.Model.extend({
    urlRoot: '/api/servers',

    validate: function (attrs) {
      if (attrs.name === '') {
        return 'Name is required';
      }
    }

  });

});
