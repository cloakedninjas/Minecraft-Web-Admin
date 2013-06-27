/*
 * setup controller (GET + POST)
 */

function createSetupSession(req) {
  req.session.setup = {
    step: 1
  };
}

function processStep(step, req, res) {
  if (step === 1) {
    // validate username + password

    // set step to 2
    step = 2;

  }

  req.session.setup.step = step;
  return step;
}

exports.index = function(req, res){

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

    //

  }
  res.render('setup/step-' + step, { title: 'Setup' });
};
