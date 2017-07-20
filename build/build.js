const build = require('@zoolanders/build')
const pkg = require('../package.json')

build.util.checkEnvironment(pkg.engines);

build.run(async _ => {

  // create a clean temp folder
  await build.del('dist/tmp/build')

  // copy over all files without vendor|tests|node
  await build.runTask({
    text: 'Copy all files',
    task: () => build.copyFolder({
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
  })

  await build.runTask({
    text: 'Add JEXEC',
    task: () => build.jexec({
      src: [
        'dist/tmp/build/**/*.php'
      ]
    })
  })

  await build.runTask({
    text: 'Add banners',
    task: () => build.banner({
      src: [
        'dist/tmp/build/libraries/**/*.php',
        'dist/tmp/build/plugins/**/*.php'
      ],
      product: pkg.description,
      version: pkg.version,
      license: pkg.license
    })
  })

  await build.runTask({
    text: 'Install Composer ',
    task: () => build.exec({
      command: 'composer install --no-dev --optimize-autoloader --no-plugins',
      options: {
        cwd: 'dist/tmp/build'
      }
    })
  })

  await build.runTask({
    text: 'Cleanup vendor',
    task: async () => {
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
  })

  // package
  await build.runTask({
    text: 'Package ',
    task: async () => {
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
        src: 'dist/tmp/pkg/',
        dest: `dist/${build.packageName({
          name: 'zl-framework',
          version: pkg.version
        })}`
      })
    }
  })

  // post tasks, remove tmp files
  await build.runTask({
    text: 'Cleanup tmp',
    task: () => build.del('dist/tmp')
  })
})
