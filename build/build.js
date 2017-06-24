const build = require('z4-build')
const pkg = require('../package.json')

buildPackage()

async function buildPackage () {

  // 1) create a clean temp folder
  build.log('Getting ready')
  await build.del('dist/tmp/build')
  // 2) copy over all files without vendor|tests|node
  build.log('Copy files')
  await copyFiles()
  // 3) add banners
  build.log('Add banners')
  await addBanners()
  // 4) install dependencies
  build.log('Install Composer')
  await installDependencies()
  // 5) prepack cleanup
  build.log('Cleanup vendor')
  await cleanupVendor()
  // 6) package
  build.log('Package')
  await package()
  // 7 post tasks, remove tmp files
  build.log('Post Cleanup')
  await build.del('dist/tmp')

  // required when using composer
  process.exit()
}

async function copyFiles () {
  await build.copyFolder({
    src: './',
    dest: 'dist/tmp/build',
    filter: [
      '!.*',
      '!*.md',
      '!*.xml',
      '!*.lock',
      '!dist{,/**}',
      '!build{,/**}',
      '!phpStorm{,/**}',
      '!node_modules{,/**}'
      '!libraries/zoolanders/tests{,/**}',
      '!libraries/zoolanders/vendor{,/**}'
    ]
  })
}

async function addBanners () {
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
  // change cwd for composer
  const cwd = process.cwd()
  process.chdir('dist/tmp/build')

  await build.composer('install', ['--no-dev', '--optimize-autoloader'])
  process.chdir(cwd) // revert cwd
}

async function cleanupVendor () {
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
