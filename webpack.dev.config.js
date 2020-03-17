const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

module.exports = {
  mode: 'development',
  entry: {
    admin:  './src/scss/wp-admin.scss',
    editor: './src/scss/wp-editor.scss',
    theme: ['./src/js/theme.js', './src/scss/theme.scss'],
  },
  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: '[name].min.js'
  },
  devtool: 'source-map',
  module: {
    rules: [
      {
        enforce: 'pre',
        exclude: /node_modules/,
        test: /\.jsx$/,
        loader: 'eslint-loader'
      },
      {
        test: /\.jsx?$/,
        loader: 'babel-loader'
      },
      {
        test: /\.s?css$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
      },
      {
        test: /\.(svg|eot|ttf|woff|woff2)?$/,
        loader: 'url-loader',
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({ filename: '../css/[name].min.css' }),
    new BrowserSyncPlugin({
      files: '**/*.php',
      injectChanges: true,
      proxy: 'http://one.wordpress.test'
    })
  ]
};
