var path = require('path');

module.exports = {
  mode: 'development',
  entry: './src/js/main.js',
  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: 'bundle.js'
  },
  watch: true,
  watchOptions: {
    aggregateTimeout: 200,
    poll: 1000
  }
};