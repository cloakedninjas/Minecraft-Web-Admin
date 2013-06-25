function MWA() {
  this.bar = 1;
  this.baz = 'baz';
}


MWA.prototype.appInit = function(req, res, next) {
  // if setup has run
  next();

  // else
  //res.redirect(412, '/setup');
};

// singleton
module.exports = new MWA();
