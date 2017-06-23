const build = require('z4-build')
const pkg = require('../package.json')

buildPackage()

async function buildPackage () {

  /*
    1) pre tasks, create a clean temp folder, change cwd
    2) copy over all files without vendor|tests|node
    3) add banners
    4) install dependencies
    5) prepack cleanup
    6) package
  */

  // 1
  build.log('Getting ready')
  await build.del('dist/tmp/build')

  // 2
  build.log('Copy files')
  await build.copyFolder({
    src: './',
    dest: 'dist/tmp/build',
    filter: [
      '!.*',
      '!.md',
      '!.xml',
      '!.lock',
      '!dist{,/**/*}',
      '!build{,/**/*}',
      '!phpStorm{,/**/*}',
      '!node_modules{,/**/*}',
      '!libraries/zoolanders/tests{,/**/*}',
      '!libraries/zoolanders/vendor{,/**/*}'
    ]
  })

  // 3
  build.log('Add banners')
  await build.banner({
    files: [
      'dist/tmp/build/libraries/**/*.php',
      'dist/tmp/build/plugins/**/*.php'
    ],
    product: 'ZOOlanders Framework',
    version: pkg.version,
    license: 'GPL'
  })

  // 4
  build.log('Install Composer')

  // change cwd for composer
  const cwd = process.cwd()
  process.chdir('dist/tmp/build')

  await build.composer('install', ['--no-dev', '--optimize-autoloader'])
  process.chdir(cwd) // revert cwd

  // 5
  build.log('Cleanup vendor')
  const vendorPath = 'dist/tmp/build/libraries/zoolanders/vendor'

  await build.del([
    // remove common unnecessary files
    `${vendorPath}/**/.*`,
    `${vendorPath}/**/Makefile`,
    `${vendorPath}/**/Dockerfile*`,
    `${vendorPath}/**/build.xml`,
    `${vendorPath}/**/travis-ci.xml`,
    `${vendorPath}/**/appveyor.yml`,
    `${vendorPath}/**/*.md`,
    `${vendorPath}/**/*.txt`,
    `${vendorPath}/**/*.pdf`,
    `${vendorPath}/**/README*`,
    `${vendorPath}/**/LICENSE*`,
    `${vendorPath}/**/CHANGES*`,
    `${vendorPath}/**/VERSION*`,
    `${vendorPath}/**/AUTHORS*`,
    `${vendorPath}/**/UPGRADE*`,
    `${vendorPath}/**/CHANGELOG*`,
    `${vendorPath}/**/composer.json`,
    `${vendorPath}/**/composer.lock`,

    // remove common unnecessary folders
    `${vendorPath}/**/bin{,/**}`,
    `${vendorPath}/**/doc{,/**}`,
    `${vendorPath}/**/docs{,/**}`,
    `${vendorPath}/**/examples{,/**}`,

    // remove git related
    `${vendorPath}/**/.git{,/**}`,
    `${vendorPath}/**/.gitkeep`,
    `${vendorPath}/**/.gitignore`,

    // remove test related
    `${vendorPath}/**/tests{,/**}`,
    `${vendorPath}/**/Tests{,/**}`,
    `${vendorPath}/**/unitTests{,/**}`,
    `${vendorPath}/**/phpunit*`,
  ])

  // 6
  build.log('Package')

  await build.copy({
    files: 'build/pkg_zoolanders.xml',
    dest: 'dist/tmp/pkg'
  })

  await Promise.all([
    build.zip({
      patterns: ['dist/tmp/build/libraries/zoolanders/'],
      dest: 'dist/tmp/pkg/packages/lib_zoolanders.zip'
    }),
    build.zip({
      patterns: ['dist/tmp/build/plugins/system/zlframework/'],
      dest: 'dist/tmp/pkg/packages/plg_zlframework.zip'
    })
  ])

  await build.zip({
    patterns: ['dist/tmp/pkg'],
    dest: `dist/ZOOlanders_${pkg.version}.zip`
  })

  // 7
  build.log('Post Cleanup')
  await build.del('dist/tmp')

  // required when using composer
  process.exit()
}
