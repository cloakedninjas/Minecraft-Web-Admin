require.config({
  paths: {
    jquery: 'vendor/jquery-2.0.3.min',
    underscore: 'vendor/underscore-min',
    backbone: 'vendor/backbone-min',
    text: 'vendor/text',

    'app': 'app/views/app',
    'default-router': 'app/routers/default',

    'base-view': 'app/views/base',
    'dashboard-view': 'app/views/dashboard'
  },
  'shim': {
    'backbone': {
      deps: ['jquery', 'underscore'],
      exports: 'Backbone'
    },
    'underscore': {
      exports: '_'
    }
  }

});

require(['app'], function(App){
  new App();
});
