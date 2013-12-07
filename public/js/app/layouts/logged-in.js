define(['base-view', 'text!app/templates/layouts/logged-in.html'], function(BaseView, loggedInTemplate){
  'use strict';

  return BaseView.extend({
    template: _.template(loggedInTemplate)


  });

});
