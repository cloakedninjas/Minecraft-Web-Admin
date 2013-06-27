/*
 * setup controller (GET + POST)
 */

var errors = {
  count: 0,
  errors: {}
};

function createSetupSession(req) {
  req.session.setup = {
    step: 1
  };
}

function processStep(step, req, res) {
  if (step === 1) {
    // validate username + password
    if (req.body.username === '') {
      addError('username', 'Username cannot be blank');
    }
    else if (req.body.password1 === '') {
      addError('password', 'Password cannot be blank');
    }
    else if (req.body.password1 !== req.body.password2) {
      addError('password', 'Passwords do not match');
    }
    else {
      step = 2;
    }

  }

  req.session.setup.step = step;
  return step;
}

function addError(field, message) {
  errors.count++;
  errors.errors[field] = message;
}

exports.index = function(req, res) {

  var step = 1;

  if (typeof req.session.setup === 'undefined') {
    createSetupSession(req);
  }
  else {
    step = req.session.setup.step;
  }

  if (req.method === 'POST') {
    // get next step
    step = processStep(step, req, res);

    console.log(errors);

    //

  }
  res.render('setup/step-' + step, { title: 'Setup', errors: errors.errors});
};
