exports.index = function(req, res) {

  res.render('index', { title: 'Index' });
};

exports.logout =  function(req, res){
  res.send('Logout');
};