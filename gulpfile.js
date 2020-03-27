'use strict';
 
const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const { gulpSassError } = require('gulp-sass-error');
var notify = require("gulp-notify");
const throwError = true;
sass.compiler = require('node-sass');

gulp.task('css', function () {
    return gulp.src('./assets/src/sass/**/*.scss')
        .pipe(sass({
            outputStyle: 'compressed'
            })
            .on('error', function(err) {
                gulpSassError(throwError);
                this.emit('end');
                return notify().write(err);
            })
        )
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(gulp.dest('./assets/dist/css'));
});
 
gulp.task('watch', function () {
    gulp.watch('./assets/src/sass/**/*.scss', gulp.series('css'));
});