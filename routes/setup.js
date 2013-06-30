/*
 * setup controller (GET + POST)
 */

function createSetupSession(req, response) {
  req.session.setup = {
    step: 1
  };
  response.step = 1;
}

function processStep(req, response) {
  var step = req.session.setup.step;

  if (req.session.setup.step === 1) {
    // validate username + password

    if (req.body.username === '') {
      addError('username', 'Username cannot be blank', response);
    }
    if (req.body.password1 === '') {
      addError('password1', 'Password cannot be blank', response);
    }
    else if (req.body.password1 !== req.body.password2) {
      addError('password2', 'Passwords do not match', response);
    }
    else {
      // proceed to step 2
      step = 2;
    }
  }

  response.step = step;
  req.session.setup.step = step;
}

function addError(field, message, response) {
  if (typeof response.errors === 'undefined') {
    response.errors = {};
  }
  response.errors[field] = message;

  response.foo = '1';
}

exports.index = function(req, res) {

  // this will be our JSON response
  var response = {
    step: null
  };

  if (typeof req.session.setup === 'undefined') {
    createSetupSession(req, response);
  }
  else {
    response.step = req.session.setup.step;
  }

  if (req.method === 'POST') {
    processStep(req, response);
  }

  res.json(response);
  //res.render('setup/step-' + step, { title: 'Setup', errors: errors.errors});
};
