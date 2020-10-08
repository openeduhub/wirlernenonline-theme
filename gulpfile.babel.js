// Please consider that a working copy of /dist/assets/js/app.js and /dist/assets/js/app.js.map
// must be copied from live working stage, it will be excluded

var gulp = require('gulp');
var sass = require('gulp-sass');
var autoPrefixer = require('gulp-autoprefixer');
var babel = require('gulp-babel');
var plumber = require('gulp-plumber');

gulp.task('sass', done => {
    gulp.src([
        'src/assets/scss/**/*.sass',
        'src/assets/scss/**/*.scss',])
        .pipe(sass({
            includePaths: [
                'node_modules/foundation-sites/scss',
                'node_modules/motion-ui/'
            ]
        }))
        .pipe(autoPrefixer())
        .pipe(gulp.dest('dist/assets/scss'));
    done();
});
gulp.task('js', done => {
    gulp.src([
        'src/assets/js/**/*.js',
        '!src/assets/js/app.js'
    ])
        .pipe(plumber())
        // Transpile the JS code using Babel's preset-env.
        .pipe(babel({
            presets: ['@babel/preset-env']
        }))
        .pipe(gulp.dest('dist/assets/js/'));

    done();
});
gulp.task('fonts', done => {
    gulp.src(['src/assets/fonts/**/*'])
        .pipe(gulp.dest('dist/assets/fonts/'));

    gulp.src(['src/assets/scss/fonts/**/*'])
        .pipe(gulp.dest('dist/assets/scss/fonts/'));
    done();
});
gulp.task('image', done => {
    gulp.src(['src/assets/img/**/*'])
        .pipe(gulp.dest('dist/assets/img/'));

    done();
});
gulp.task('css', done => {
    gulp.src(['src/assets/scss/**/*.css'])
        .pipe(gulp.dest('dist/assets/scss'));

    done();
});
gulp.task('php', done => {
    gulp.src([
        '**/*.php',
        '!dist/**/*.php'
    ])
        .pipe(gulp.dest('dist/'));

    done();
});
gulp.task('default',function(){
    gulp.watch('src/assets/js/**/*.js',gulp.series('js'));
    gulp.watch('src/assets/scss/**/*.scss',gulp.series('sass'));
    gulp.watch(['**/*.css', '!dist/**/*.css'],gulp.series('css'));
    gulp.watch(['**/*.php', '!dist/**/*.php'],gulp.series('php'));
});

gulp.task('build', gulp.series('sass', 'js', 'css', 'fonts', 'image', 'php'));