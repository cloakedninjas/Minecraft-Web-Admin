define(['underscore', 'backbone', 'models/setup', 'text!templates/setup/step-1.html', 'text!templates/setup/step-2.html'],
  function(_, Backbone, SetupModel, Step1Template, Step2Template) {
  return Backbone.View.extend({

    el: $('#content'),

    events: {
      'submit form' : '_handleFormSubmission'

    },

    initialize: function() {

      this.model = new SetupModel();

      var view = this;
      this.model.on('sync', function(model, response) {
        view._handleSync(model, response);
      });

      this.model.fetch();
    },

    render: function() {
      var template;

      switch (this.model.get('step')) {
        case 2:
          template = Step2Template;
          break;

        case 1:
        default:
          template = Step1Template;
          break;

      }

      this.$el.html(_.template( template, {} ));
      return this;
    },

    _handleSync: function (model, response) {
      if (typeof response.errors !== 'undefined') {
        var errors = this.model.get('errors');

        for(var e in errors) {
          this._addError(e, errors[e]);
        }
      }
      else {
        this.render();
      }

    },

    _handleFormSubmission: function (e) {
      e.preventDefault();

      this._clearErrors();

      var values = this.$('form').serializeArray();
      var postValues = {};

      for (var i = 0; i < values.length; i++) {
        postValues[values[i].name] = values[i].value;
      }

      this.model.save(postValues);
      return false;
    },

    _addError: function (field, message) {
      this.$('p.' + field).addClass('error').append('<span class="error-message">' + message + '</span>');
    },

    _clearErrors: function () {
      this.$('p.error .error-message').remove();
      this.$('p.error').removeClass('error');
    }

  });
});
