require.config({
  paths: {
    jquery: 'libs/jquery-1.8.0.min',
    underscore: 'libs/underscore-min',
    backbone: 'libs/backbone-min'
  },
  'shim': {
    'backbone': {
      deps: ['jquery', 'underscore'],
      exports: 'Backbone'
    }
  }

});

require(['app'], function(App){
  App.initialize();
});
