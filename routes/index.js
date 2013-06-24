
/*
 * GET home page.
 */

exports.index = function(req, res){
  console.log(app.get('config'));
  res.render('index', { title: 'Express' });
};