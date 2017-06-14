const build = require('z4-build')
const pkg = require('../package.json')

module.exports = async dest => {

  // we want to start fresh
  build.del('dist/tmp/framework')

  await build.copyFolder({
    src: 'plugins/system/zlframework',
    dest: 'dist/tmp/framework'
  })

  await build.banner({
    files: [
      'dist/tmp/framework/**/*.php'
    ],
    product: 'ZOOlanders Legacy Framework',
    license: 'GPL'
  })

  await build.zip({
    patterns: ['dist/tmp/framework/'],
    dest: `${dest}/plg_zlframework.zip`
  })

  build.del('dist/tmp/framework')
}
