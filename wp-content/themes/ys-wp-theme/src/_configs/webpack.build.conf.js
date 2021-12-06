const merge = require('webpack-merge');
const baseWebpackConfig = require('./webpack.base.conf');
const {entriesFilter} = require('./helper');

// Производим слияние настроек
const buildWebpackConfig = merge(baseWebpackConfig, {
    mode   : 'production',
    plugins: []
});

module.exports = new Promise((resolve, reject) => {
    resolve(buildWebpackConfig);
});