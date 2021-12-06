const path                 = require('path'),
      MiniCssExtractPlugin = require('mini-css-extract-plugin'),
      CopyWebpackPlugin    = require('copy-webpack-plugin'),
      {CleanWebpackPlugin} = require('clean-webpack-plugin'),
      {PATHS, getEntries}  = require('./helper'),
      webpack              = require('webpack');
module.exports = {
    optimization: {
        minimize: true,

        // Для того чтобы весь общий вспомогательный код webpack'a помещался в один отдельный файл.
        runtimeChunk: 'single'
    },
    // Отключает предупреждения о размере бандла
    performance: {
        hints: false
    },
    // Убирает логирование плагинов в терминал (чтобы проще читалось)
    stats    : {children: false},
    resolve  : {
        alias: {
            '@theme'          : PATHS.theme,
            '@assets'         : PATHS.assets,
            '@base-assets'    : PATHS.baseAssets,
            '@theme-assets'   : PATHS.themeAssets,
            '@homepage-assets': PATHS.homepageAssets,
            '@plugins'        : PATHS.plugins,
            '@exjs'           : path.join(PATHS.assets, '/libs/extendedjs'),
        }
    },
    entry    : getEntries(),
    output   : {
        filename  : 'js/[name].min.js',
        path      : PATHS.dist,
        publicPath: '/dist'
    },
    module   : {
        //noParse: /jquery/,
        rules: [{
            test   : /\.js$/,
            loader : 'babel-loader',
            exclude: /node_modules/,
            options: {
                presets: ['@babel/preset-env'],
                plugins: ['@babel/plugin-proposal-class-properties', "transform-regenerator"]
            }
        }, {
            test   : /\.(png|jpg|gif|svg)$/,
            loader : 'file-loader',
            options: {
                name      : '[name].[ext]',
                outputPath: 'images'
            }
        }, {
            test   : /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
            loader : 'file-loader',
            options: {
                name      : '[name].[ext]',
                outputPath: 'fonts'
            }
        }, {
            test: /\.s[ca]ss$/,
            use : [
                MiniCssExtractPlugin.loader,
                {
                    loader : 'css-loader',
                    options: {importLoaders: 2} // 2 - sass-loader, postcss-loader
                },
                {
                    loader : 'postcss-loader',
                    options: {config: {path: `${PATHS.configs}/postcss/postcss.config.js`}}
                },
                'sass-loader'
            ]
        }, {
            test: /\.css$/,
            use : [
                MiniCssExtractPlugin.loader,
                {
                    loader : 'css-loader',
                    options: {importLoaders: 1} // 1 - sass-loader
                },
                {
                    loader : 'postcss-loader',
                    options: {config: {path: `${PATHS.configs}/postcss/postcss.config.js`}}
                },
            ],
        }]
    },
    externals: {
        //     $: 'jquery',
        //     jquery: 'jQuery',
    },
    plugins  : [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),

        // Удаляет все в папке dist
        new CleanWebpackPlugin({
            cleanStaleWebpackAssets: false
        }),

        new CopyWebpackPlugin([
            {from: `${PATHS.src}/assets/images`, to: `${PATHS.dist}/images`},
            {from: `${PATHS.src}/assets/fonts`, to: `${PATHS.dist}/fonts`},
        ]),

        new webpack.ProvidePlugin({
            "$"            : "jquery",
            "jQuery"       : "jquery",
            "window.$"     : "jquery",
            "window.jQuery": "jquery"
        }),
    ],
};