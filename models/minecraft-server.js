function MinecraftServer() {
  this.rcon = null;
  this.baz = 'baz';
}

MinecraftServer.prototype.create = function() {
};

MinecraftServer.prototype.build = function() {
};

MinecraftServer.prototype.isRunning = function() {
};

// singleton
module.exports = MinecraftServer;
