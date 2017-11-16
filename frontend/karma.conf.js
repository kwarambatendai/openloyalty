var path = require('path');

module.exports = function (config) {
    config.set({
        basePath: '',
        frameworks: ['jasmine'],
        files: [
            'test/loadtests.js'
        ],
        exclude: [],
        preprocessors: {
            'test/loadtests.js': ['webpack', 'sourcemap']
        },
        webpack: {
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        include: [
                            path.resolve(__dirname, 'src'),
                            path.resolve(__dirname, 'test')
                        ],
                        use: [{
                            loader: 'babel-loader',
                            options: {
                                presets: [
                                    ['es2015', { modules: false }]
                                ]
                            }
                        }]
                    }
                ]
            }
        },
        reporters: ['progress'],
        port: 9876,
        colors: true,
        logLevel: config.LOG_INFO,
        autoWatch: false,
        browsers: ['PhantomJS'],
        singleRun: true,
        concurrency: Infinity,
        plugins: [
            'karma-webpack',
            'karma-jasmine',
            'karma-sourcemap-loader',
            'karma-phantomjs-launcher'
        ]
    })
};
