require.config({
  paths: {
    jquery: 'vendor/jquery-2.0.3.min',
    underscore: 'vendor/underscore-min',
    backbone: 'vendor/backbone-min',
    text: 'vendor/text'
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
  App.initialize();
});
