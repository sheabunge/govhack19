'use strict';

import {task, src, dest, parallel, series, watch} from 'gulp';
import sourcemaps from 'gulp-sourcemaps';
import clean from 'gulp-clean';
import flatten from 'gulp-flatten';
import rename from 'gulp-rename';

import postcss from 'gulp-postcss';
import sass from 'gulp-sass';

import browserify from 'browserify';
import babelify from 'babelify';
import source from 'vinyl-source-stream';
import buffer from 'vinyl-buffer';
import uglify from 'gulp-uglify';
import eslint from 'gulp-eslint';

const browsersync = require('browser-sync').create();

task('css', () =>
	src('src/scss/style.scss')
		.pipe(rename({extname: '.css'}))
		.pipe(sourcemaps.init())
		.pipe(sass({
			importer: require('node-sass-package-importer')()
		}).on('error', sass.logError))
		.pipe(postcss([
			require('autoprefixer')(),
			require('cssnano')()]
		))
		.pipe(sourcemaps.write('.'))
		.pipe(dest('dist'))
		.pipe(browsersync.stream({match: '*.css'}))
);

task('test-js', () => {

	const options = {
		parserOptions: {
			ecmaVersion: 6,
			sourceType: 'module'
		},
		extends: 'eslint:recommended',
		rules: {
			'quotes': ['error', 'single'],
			'linebreak-style': ['error', 'unix'],
			'eqeqeq': ['warn', 'always'],
			'indent': ['error', 'tab']
		}
	};

	return src('src/js/**/*.js')
		.pipe(eslint(options))
		.pipe(eslint.format())
		.pipe(eslint.failAfterError())
});

task('js', series('test-js', () =>
	browserify({debug: true, entries: 'src/js/app.js'})
		.transform('babelify', {presets: ['@babel/preset-env'], sourceMaps: true})
		.bundle()
		.pipe(source('src/js/app.js'))
		.pipe(buffer())
		.pipe(sourcemaps.init())
		.pipe(uglify())
		.pipe(sourcemaps.write('.'))
		.pipe(flatten())
		.pipe(dest('dist'))
		.pipe(browsersync.stream({match: '*.js'}))
));

task('vendor', () =>
	src('node_modules/bootstrap/dist/css/bootstrap.min.css*')
		.pipe(dest('dist'))
);

task('fonts', () =>
	src(['solid', 'regular'].map((style) => `node_modules/@fortawesome/fontawesome-pro/webfonts/fa-${style}-*`))
		.pipe(rename({dirname: ''}))
		.pipe(dest('dist/fonts'))
);

task('clean', () =>
	src('dist', {read: false, allowEmpty: true})
		.pipe(clean())
);

task('default', series('clean', parallel('vendor', 'fonts', 'css', 'js')));

task('watch', series('default', (done) => {
	watch('src/css/**/*.scss', series('css'));
	watch('src/js/*.js', series('js'));
	done();
}));

task('browsersync', parallel('watch', (done) => {

	browsersync.init({
		proxy: 'http://localhost/projects/govhack19',
	});

	watch('src/templates/**/*.html').on('change', browsersync.reload);
	done();
}));
