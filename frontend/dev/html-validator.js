var validator = require('html-validator');
var fs = require('fs');
var options = {
    validator: 'http://html5.validator.nu',
    format: 'text'
};

var Filehound = require('filehound');

Filehound.create()
    .ext('html')
    .paths("./dist")
    .find((err, htmlFiles) => {
        if (err) return console.error('handle err', err);

        htmlFiles.forEach(function(filename) {
            fs.readFile(filename, function(err, content) {
                if (err) {
                    throw err;
                }
                options.data = content;
                validator(options, (error, data) => {
                    if (error) {
                        console.error(error)
                    }

                    console.log(filename + '\n' + data)
                })
            });
        });
    });
