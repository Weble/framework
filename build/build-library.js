const build = require('z4-build')
const pkg = require('../package.json')

module.exports = async (dest) => {

  // we want to start fresh
  build.del('dist/tmp/library')

  await build.copyFolder({
    src: 'libraries/zoolanders',
    dest: 'dist/tmp/library',
    filter: [
      // remove all hidden files
      '!vendor/**/.*',

      // remove build files
      '!vendor/**/Makefile',
      '!vendor/**/Dockerfile*',
      '!vendor/**/build.xml',
      '!vendor/**/travis-ci.xml',
      '!vendor/**/appveyor.yml',

      // remove common unnecessary files
      '!vendor/**/*.md',
      '!vendor/**/*.txt',
      '!vendor/**/*.pdf',
      '!vendor/**/README*',
      '!vendor/**/LICENSE*',
      '!vendor/**/CHANGES*',
      '!vendor/**/VERSION*',
      '!vendor/**/AUTHORS*',
      '!vendor/**/UPGRADE*',
      '!vendor/**/CHANGELOG*',
      '!vendor/**/composer.json',
      '!vendor/**/composer.lock',

      // remove common unnecessary folders
      '!vendor/**/bin',
      '!vendor/**/bin/**',
      '!vendor/**/doc',
      '!vendor/**/doc/**',
      '!vendor/**/docs',
      '!vendor/**/docs/**',
      '!vendor/**/examples',
      '!vendor/**/examples/**',

      // remove git related
      '!vendor/**/.git',
      '!vendor/**/.git/**',
      '!vendor/**/.gitkeep',
      '!vendor/**/.gitignore',

      // remove test related
      '!tests',
      '!tests/**/*',
      '!vendor/**/tests',
      '!vendor/**/tests/**',
      '!vendor/**/Tests',
      '!vendor/**/Tests/**',
      '!vendor/**/unitTests',
      '!vendor/**/unitTests/**',
      '!vendor/joolanders',
      '!vendor/joolanders/**',
      '!vendor/**/phpunit.xml',
      '!vendor/**/phpunit.xml.dist'
    ]
  })

  await build.banner({
    files: [
      'dist/tmp/library/**/*.php',
      '!dist/tmp/library/vendor/**/*'
    ],
    product: 'ZOOlanders Library',
    version: pkg.version,
    license: 'GPL'
  })

  await build.zip({
    patterns: ['dist/tmp/library/'],
    dest: `${dest}/lib_zoolanders.zip`
  })

  build.del('dist/tmp/library')
}
