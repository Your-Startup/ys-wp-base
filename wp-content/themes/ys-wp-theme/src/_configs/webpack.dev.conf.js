const
    path              = require('path'),
    webpack           = require('webpack'),
    merge             = require('webpack-merge'),
    baseWebpackConfig = require('./webpack.base.conf'),
    {PATHS}           = require('./helper');

const devWebpackConfig = merge(baseWebpackConfig, {
    mode     : 'development',
    devtool  : 'source-map',
    devServer: {
        allowedHosts: "all",
        magicHtml   : true,
        open        : '/dist/home.html',
        static      : {
            directory : path.join(PATHS.template),
            publicPath: '/dist',
        },
        compress    : true,
        port        : 9000,
    },
    output   : {
        publicPath: '/dist'
    },
    plugins  : [
        new webpack.SourceMapDevToolPlugin({
            filename: '[file].map'
        })
    ]
});

module.exports = new Promise((resolve, reject) => {
    resolve(devWebpackConfig);
});