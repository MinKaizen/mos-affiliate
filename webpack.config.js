var path = require('path');

module.exports = {
  mode: 'development',
  entry: './src/js/main.js',
  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: 'mosAffiliate.js'
  },
  watch: true,
};