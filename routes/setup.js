/*
 * GET setup
 */

exports.index = function(req, res){
  res.render('setup/step-1', { title: 'Setup' });
};
