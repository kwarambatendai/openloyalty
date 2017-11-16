const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const extractCSS = new ExtractTextPlugin('assets/styles/[name].bundle.css');
const postCSSOptions = require('./postcss.config.js');
const CopyWebpackPlugin = require('copy-webpack-plugin');

const extractCommons = new webpack.optimize.CommonsChunkPlugin({
    name: 'commons',
    filename: 'assets/js/commons.js'
});

const config = {
    context: path.resolve(__dirname, 'src'),
    devServer: {
        disableHostCheck: true
    },
    entry: {
        admin: './appAdmin.js',
        client: './appClient.js',
        pos: './appPos.js'
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].app.js',
        publicPath: '/'
    },
    resolve: {
        alias: {
            'chart': require.resolve('chart.js'),
            'jquery': require.resolve('jquery/src/jquery'),
            'Selectize': require.resolve('selectize/dist/js/standalone/selectize.min.js')
        }
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [
                    path.resolve(__dirname, 'src'),
                    path.resolve(__dirname, 'node_modules/open-loyalty-frontend')
                ],
                use: [{
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            ['es2015', {modules: false}]
                        ]
                    }
                }]
            },
            {
                test: /\.css$/,
                use: [
                    'style-loader',
                    'css-loader'
                ]
            },
            {
                test: /\.scss$/,
                loader: extractCSS.extract([
                    {
                        loader: 'css-loader'
                    },
                    {
                        loader: 'postcss-loader',
                        options: postCSSOptions
                    },
                    {
                        loader: 'sass-loader'
                    }
                ])
            },
            {
                test: /\.(png|jpg|svg|gif)$/,
                use: [{
                    loader: 'file-loader?name=img/[name].[ext]'
                }]
            },
            {
                test: /.*\/(admin.html|client.html|pos.html)$/,
                loader: "file-loader?name=[name]/index.html"
            },
            {
                test: /^(?!.*\/+(admin\.html|client\.html|pos\.html))(?!(admin\.html|client\.html|pos\.html)).*html$/,
                use: [{
                    loader: 'file-loader?name=templates/[name].[ext]'
                }]
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]'
                    }
                }]
            }
        ]
    },
    plugins: [
        new webpack.NamedModulesPlugin(),
        extractCSS,
        extractCommons,
        new CopyWebpackPlugin([
            { from: 'config.js', to: 'config.js'}
        ])
    ]
};

module.exports = config;
