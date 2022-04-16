const mix = require('laravel-mix');

mix.webpackConfig({
	watchOptions: {
		ignored: /node_modules/
	},
   resolve: {
       alias: {
           "@js": path.resolve(
               __dirname,
               "resources/js/app"
           ),
           "@admin": path.resolve(
               __dirname,
               "resources/js/admin"
           )
       }
   }
});

mix.js('resources/js/frontend/frontend.js', 'public/js')
   .js('resources/js/app/app.js', 'public/js')
   .js('resources/js/admin/admin.js', 'public/js')
   .version();

mix.sass('resources/sass/app/app.scss', 'public/css')
   .sass('resources/sass/frontend/frontend.scss', 'public/css')
   .version();
