/*
 * GET home page.
 */

exports.index = function(req, res) {
  // bootstrap models for front-end
  var state = {
    setup: app.get('db').setup
  };

  res.render('index', { title: 'Index', state: state });
};
