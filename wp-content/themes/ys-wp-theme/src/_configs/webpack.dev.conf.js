const
    path              = require('path'),
    webpack           = require('webpack'),
    merge             = require('webpack-merge'),
    baseWebpackConfig = require('./webpack.base.conf'),
    {PATHS}           = require('./helper'),
    HtmlWebpackPlugin = require("html-webpack-plugin"),
    glob              = require("glob");

glob.sync(`${PATHS.template}/**/*.html`).forEach(item => {
    let file = path.normalize(item)
        .replace(path.join(PATHS.template, '/'), '')
        .replace('.html', '');

    baseWebpackConfig.plugins.push(
        new HtmlWebpackPlugin({
            filename: `${path.basename(item, path.extname(item))}.html`,
            template: '!!ejs-compiled-loader!' + item,
            inject  : true,
            chunks  : ['base', file],
        })
    );
});

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
    plugins  : [
        new webpack.SourceMapDevToolPlugin({
            filename: '[file].map'
        })
    ]
});

module.exports = new Promise((resolve, reject) => {
    resolve(devWebpackConfig);
});