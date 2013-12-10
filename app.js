var express = require('express'),
  path = require('path'),
  http = require('http'),
  routes = require('./app/routes'),
  apiRoutes = require('./app/routes/api')
  ;

// app is our global object
app = express();

// all environments
app.set('port', process.env.PORT || 3000);
app.set('views', __dirname + '/app/views');
app.set('view engine', 'ejs');

app.use(express.favicon(__dirname + '/public/favicon.ico'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(express.cookieParser('^CBN"4s-2da'));
app.use(express.session());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));


/*app.get('*', function(request, response, next) {
  response.sendfile(__dirname + '/index.html');
});*/
app.get('/', routes.index);
app.get('/dashboard', routes.index);
app.get('/servers', routes.index);
app.get('/settings', routes.index);

app.get('/api/:resource', apiRoutes.index);

app.get('/logout', routes.logout);


http.createServer(app).listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});

//var RconClient = require('./models/rcon-client');
//var client = new RconClient('localhost', 25575, 'foobar');
//client.connect();
