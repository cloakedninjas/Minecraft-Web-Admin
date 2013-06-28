define(['router'], function(Router) {

  return {
    _state: {},

    initialize: function() {
      this._state = state;
      this._router = new Router();
    },

    getState: function (field) {
      return (typeof this._state[field] !== 'undefined') ? this._state[field] : null;
    }
  };

});

