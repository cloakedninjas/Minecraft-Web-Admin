module.exports = function(app, express){
    app.configure(function() {
        app.use(express.logger());
        
        var config = require('konphyg')(__dirname);
        
        app.set('config', config('mwa'));
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