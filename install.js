var sqlite3 = require('sqlite3').verbose();
var fs = require('fs');

var sql = fs.readFileSync(__dirname + '/data/create_database.sql').toString();
var db = new sqlite3.Database(__dirname + '/data/mwa.db');

// queries must be split into single statements
var queries = sql.split(';\n');

db.serialize(function() {
  for (var i = 0; i < queries.length; i++) {
    db.run(queries[i]);
  }

});

db.close();