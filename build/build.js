const build = require('z4-build')
const pkg = require('../package.json')

buildPackage()

async function buildPackage () {

  // create a clean temp folder
  await preFlight()
  // copy over all files without vendor|tests|node
  await copyFiles()
  // add banners
  await addBanners()
  // install dependencies
  await installDependencies()
  // prepack cleanup
  await cleanupVendor()
  // package
  await package()
  // post tasks, remove tmp files
  await postFlight()

  // required when using composer
  process.exit()
}

async function preFlight () {
  build.log('Getting ready')
  await build.del('dist/tmp/build')
}

async function copyFiles () {
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
}

async function addBanners () {
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
}

async function installDependencies () {
  build.log('Install Composer')

  // change cwd for composer
  const cwd = process.cwd()
  process.chdir('dist/tmp/build')

  await build.composer('install', ['--no-dev', '--optimize-autoloader'])
  process.chdir(cwd) // revert cwd
}

async function cleanupVendor () {
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
    `${vendorPath}/**/phpunit*`
  ])
}

async function package () {
  build.log('Package')

  await build.copy({
    files: 'build/pkg.xml',
    dest: 'dist/tmp/pkg',
    options: {
      rename: name => `${name.replace('.xml', '')}_zoolanders_framework.xml`
    }
  })

  await build.copy({
    files: [
      'dist/tmp/build/administrator/language/en-GB/en-GB.plg_system_zlframework.ini',
      'dist/tmp/build/administrator/language/en-GB/en-GB.plg_system_zlframework.sys.ini'
    ],
    dest: 'dist/tmp/pkg/language'
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
}

async function postFlight () {
  build.log('Post Cleanup')
  await build.del('dist/tmp')
}
