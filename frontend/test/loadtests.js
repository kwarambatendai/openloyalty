'use strict';

// Add support for all files in the test directory
var testsContext = require.context('.', true, /(Test\.js$)|(Helper\.js$)/);
testsContext.keys().forEach(testsContext);
