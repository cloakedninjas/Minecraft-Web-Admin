exports.index = function(req, res) {
  res.set('Content-Type', 'application/json');
  res.send('[{"id": 1, "name": "Test Server"}]');
};
