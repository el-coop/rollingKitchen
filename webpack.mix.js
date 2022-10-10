const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js').vue({
    compilerOptions: {
        whitespace: 'preserve',
    },
    options: {
        compilerOptions: {
            whitespace: 'preserve',
            compatConfig: {
                MODE: 2,
            },
        },
    },
}).sass('resources/sass/app.scss', 'public/css')
    .version()
    .webpackConfig(() => {
        return {
            resolve: {
                alias: {
                    vue: "@vue/compat",
                    "@vue/composition-api": "@vue/compat",
                }
            }
        }
    });
