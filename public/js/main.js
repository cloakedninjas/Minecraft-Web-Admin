require.config({
  paths: {
    jquery: 'vendor/jquery-2.0.3.min',
    underscore: 'vendor/underscore-min',
    backbone: 'vendor/backbone-min',
    text: 'vendor/text',

    'app-view': 'app/views/app',
    'default-router': 'app/routers/default',

    'logged-in-layout': 'app/layouts/logged-in',

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

require(['app-view'], function(App){
  new App();
});
