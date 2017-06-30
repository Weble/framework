const build = require('@zoolanders/build')
const pkg = require('../package.json')

buildPackage()

async function buildPackage () {

  // create a clean temp folder
  build.log('Getting ready')
  await build.del('dist/tmp/build')
  // copy over all files without vendor|tests|node
  build.log('Copy files')
  await copyFiles()
  // add JEXEC
  build.log('Add JEXEC')
  await addJexec()
  // add banners
  build.log('Add banners')
  await addBanners()
  // install dependencies
  build.log('Install Composer')
  await installDependencies()
  // prepack cleanup
  build.log('Cleanup vendor')
  await cleanupVendor()
  // package
  build.log('Package')
  await package()
  // post tasks, remove tmp files
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
      '!node_modules{,/**}',
      '!libraries/zoolanders/tests{,/**}',
      '!libraries/zoolanders/vendor{,/**}'
    ]
  })
}

async function addJexec () {
  await build.jexec({
    src: [
      'dist/tmp/build/**/*.php'
    ]
  })
}

async function addBanners () {
  await build.banner({
    src: [
      'dist/tmp/build/libraries/**/*.php',
      'dist/tmp/build/plugins/**/*.php'
    ],
    product: pkg.description,
    version: pkg.version,
    license: pkg.license
  })
}

async function installDependencies () {
  // change cwd for composer
  const cwd = process.cwd()
  process.chdir('dist/tmp/build')

  await build.composer('install', ['--no-dev', '--optimize-autoloader', '--no-plugins'])
  process.chdir(cwd) // revert cwd
}

async function cleanupVendor () {
  const vendorPath = 'dist/tmp/build/libraries/zoolanders/vendor'

  await build.del([
    // remove common unnecessary files
    `${vendorPath}/**/.*`,
    `${vendorPath}/**/*.md`,
    `${vendorPath}/**/*.txt`,
    `${vendorPath}/**/*.pdf`,
    `${vendorPath}/**/Gemfile`,
    `${vendorPath}/**/Makefile`,
    `${vendorPath}/**/Dockerfile*`,
    `${vendorPath}/**/package.json`,
    `${vendorPath}/**/build.xml`,
    `${vendorPath}/**/travis-ci.xml`,
    `${vendorPath}/**/appveyor.yml`,
    `${vendorPath}/**/README*`,
    `${vendorPath}/**/LICENSE*`,
    `${vendorPath}/**/LICENCE*`,
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
    src: 'build/pkg.xml',
    dest: 'dist/tmp/pkg',
    options: {
      rename: name => `${name.replace('.xml', '')}_zoolanders_framework.xml`
    }
  })

  await build.copy({
    src: [
      'dist/tmp/build/administrator/language/en-GB/en-GB.plg_system_zlframework.ini',
      'dist/tmp/build/administrator/language/en-GB/en-GB.plg_system_zlframework.sys.ini'
    ],
    dest: 'dist/tmp/pkg/language'
  })

  await Promise.all([
    build.zip({
      src: ['dist/tmp/build/libraries/zoolanders/'],
      dest: 'dist/tmp/pkg/packages/lib_zoolanders.zip'
    }),
    build.zip({
      src: ['dist/tmp/build/plugins/system/zlframework/'],
      dest: 'dist/tmp/pkg/packages/plg_zlframework.zip'
    })
  ])

  await build.zip({
    src: ['dist/tmp/pkg'],
    dest: `dist/ZOOlanders_${pkg.version}.zip`
  })
}
