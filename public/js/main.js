require.config({
  paths: {
    jquery: 'libs/jquery-1.8.0.min',
    underscore: 'libs/underscore-min',
    backbone: 'libs/backbone-min'
  }

});

require(['app'], function(App){
  App.initialize();
});