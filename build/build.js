const build = require('z4-build')
const buildLibrary = require('./build-library.js')
const buildFrameworkLegacy = require('./build-framework-legacy.js')

buildPackage()

async function buildPackage () {

  // we want to start fresh
  build.del('dist/tmp')

  await build.copy({
    files: 'build/pkg_zoolanders.xml',
    dest: 'dist/tmp/pkg'
  })

  await buildPackages()

  await build.zip({
    patterns: ['dist/tmp/pkg'],
    dest: 'dist/ZOOlanders.zip'
  })

  build.del('dist/tmp')
}

function buildPackages () {
  return Promise.all([
    buildLibrary('dist/tmp/pkg/packages'),
    buildFrameworkLegacy('dist/tmp/pkg/packages')
  ])
}
