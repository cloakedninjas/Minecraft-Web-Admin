function MWA() {
  this.bar = 1;
  this.baz = 'baz';
}

var config = {}; //app.get('config');

MWA.prototype.appInit = function(req, res, next) {
  // if setup has run
if (config.setup) {
    next();
  }
  else {
    res.redirect('/setup');
  }
};


// singleton
module.exports = new MWA();
