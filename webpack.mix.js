const webpack = require('webpack');
const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */



mix.webpackConfig({
    plugins: [
        new webpack.IgnorePlugin({
            resourceRegExp: /^\.\/locale$/,
            contextRegExp: /moment$/,
        })
    ]
});

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/ws.js', 'public/js')
    .postCss('resources/css/print.css','public/css')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('autoprefixer'),
    ]).options({
        processCssUrls: false,
    })
    .sass('resources/scss/app.scss', 'public/css')
    //Web Fonts
    .copyDirectory('resources/webfonts', 'public/webfonts')
    .copyDirectory('resources/external/fontawesome-pro-6.1.1-web/webfonts', 'public/webfonts')

mix.disableNotifications().version();