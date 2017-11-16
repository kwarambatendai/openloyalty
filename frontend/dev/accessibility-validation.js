var AccessSniff = require('access-sniff');

var options = {
    accessibilityLevel: 'WCAG2AAA'
};


AccessSniff
    .default(['dist/**/*.html'], options)
    .then(function(report) {
        AccessSniff.report(report);
    }, function(error) {
        console.error(error)
    });
