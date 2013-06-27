var Rcon = require('rcon');

function RconClient(host, port, password) {
  this.host = null;
  this.port = null;
  this.password = null;
  this.authed = false;

  this.conn = new Rcon(host, port, password);

  this.conn.on('auth', function() {
    this.authed = true;
  });

  this.conn.on('error', function(err) {
    console.log('Error!!!!', err);
  });

  this.conn.on('response', this.receiveResponse);

  this.conn.on('end', function() {
    console.log("Socket closed!");
    process.exit();
  });

}

RconClient.prototype.connect = function() {
  this.conn.connect();
};

RconClient.prototype.sendCommand = function(cmd) {
  console.log('this is', this);
  this.conn.send(cmd);
};

RconClient.prototype.receiveResponse = function(data) {
  console.log('Got response: ', data);
};

// singleton
module.exports = RconClient;
