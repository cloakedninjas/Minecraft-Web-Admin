var express = require('express')
  , partials = require('express-partials')
  , routes = require('./routes')
  , setup = require('./routes/setup')
  , http = require('http')
  , path = require('path')
  , mwa = require('./lib/mwa');

// app is our global object
app = express();

require('./lib/config.js')(app, express);

// all environments
app.set('port', process.env.PORT || 3000);
app.set('views', __dirname + '/views');
app.set('view engine', 'ejs');

//app.use(partials());
app.use(express.favicon(__dirname + '/public/images/favicon.ico'));
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(express.cookieParser('^CBN"4s-2da'));
app.use(express.session());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

//app.get('/setup', setup.index);
//app.post('/setup', setup.index);
app.get('/', routes.index);

http.createServer(app).listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});

//var RconClient = require('./models/rcon-client');
//var client = new RconClient('localhost', 25575, 'foobar');
//client.connect();
