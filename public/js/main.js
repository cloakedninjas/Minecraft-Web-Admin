require.config({
  paths: {
    jquery: 'libs/jquery-1.8.0.min',
    underscore: 'libs/underscore-min',
    backbone: 'libs/backbone-min',
    text: 'libs/text'
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
