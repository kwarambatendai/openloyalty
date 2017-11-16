var sassdoc = require('sassdoc');
var source = './src/**/*.scss';
var config = {
    dest: './docs/sass'
};
sassdoc(source, config);
