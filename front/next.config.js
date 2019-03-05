const withCSS = require('@zeit/next-css')

module.exports = withCSS({
  env: {
    api: 'http://localhost/api'
  }
})
