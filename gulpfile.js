'use strict';
 
const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');

sass.compiler = require('node-sass');

gulp.task('css', function () {
  return gulp.src('./assets/src/sass/**/*.scss')
    .pipe(autoprefixer({
        cascade: false
    }))
    .pipe(sass({
        outputStyle: 'compressed'
        })
        .on('error', sass.logError)
    )
    .pipe(gulp.dest('./assets/dist/css'));
});
 
gulp.task('watch', function () {
  gulp.watch('./assets/src/sass/**/*.scss', gulp.series('css'));
});