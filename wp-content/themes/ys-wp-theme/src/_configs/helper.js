const path = require('path');
const glob = require('glob');
const HtmlWebpackPlugin = require("html-webpack-plugin");

const PATHS = {
    src     : path.resolve(__dirname, '../../src'),
    dist    : path.resolve(__dirname, '../../dist'),
    template: path.resolve(__dirname, '../../template-parts'),
    configs : path.resolve(__dirname, '../_configs'),
    assets  : path.resolve(__dirname, '../../src/assets'),

    plugins       : path.resolve(__dirname, '../../../../plugins'),
    theme         : path.resolve(__dirname, '../../../bmr'),
    themeAssets   : path.resolve(__dirname, '../../../bmr/assets-v2'),
    homepageAssets: path.resolve(__dirname, '../../../bmr/webpack/src/assets'),
    baseAssets    : path.resolve(__dirname, '../../../base/assets'),
};
exports.PATHS = PATHS;

exports.getEntries = () => {
    return glob.sync(PATHS.src + '/entry/**/*.js').reduce((res, filePath) => {
        const normalizedPath = path.normalize(filePath);

        // entry - то что подставляется в переменную [name] шаблона
        const entry = normalizedPath
            .replace(path.join(PATHS.src, '/entry/'), '')
            .replace('.js', '')
        ;
        res[entry] = ['babel-polyfill', normalizedPath];

        return res;
    }, {});
};

// Фильтр сборок
exports.entriesFilter = (entries, cb) => {
    return Object.keys(entries)
        .filter(key => cb(key, entries[key]))
        .reduce((res, key) => (res[key] = entries[key], res), {})
};
