let mix = require('laravel-mix');

var LiveReloadPlugin = require('webpack-livereload-plugin');

mix.js('src/resources/assets/js/app.js', 'src/public/js')
   .sass('src/resources/assets/sass/app.scss', 'src/public/css');

mix.webpackConfig({
    plugins: [
        new LiveReloadPlugin()
    ]
});
