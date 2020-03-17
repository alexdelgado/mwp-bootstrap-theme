const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  mode: 'production',
  entry: {
    admin:  './src/scss/wp-admin.scss',
    editor: './src/scss/wp-editor.scss',
    theme: ['./src/js/theme.js', './src/scss/theme.scss'],
  },
  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: '[name].min.js'
  },
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
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader', 'sass-loader']
      },
      {
        test: /\.(svg|eot|ttf|woff|woff2)?$/,
        loader: 'url-loader',
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({ filename: '../css/[name].min.css' }),
  ]
};
