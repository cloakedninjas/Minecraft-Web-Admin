module.exports = function(app, express){
    app.configure(function() {
        app.use(express.logger('tiny'));

        var config = require('konphyg')(__dirname + '/../config');

        app.set('config', config('mwa'));
        app.set('db', config('db'));
    });

    app.configure('development', function() {
        app.use(express.errorHandler({
            dumpExceptions: true,
            showStack: false
        }));
    });

    app.configure('production', function() {
        app.use(express.errorHandler());
    });
};
