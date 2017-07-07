const pkg = require('../package.json')
const build = require('@zoolanders/build')

build.util.checkEnvironment(pkg.engines);

build.run(async _ => {
  const args = build.cliArgs()

  await build.bump({
    src: [
      'package.json',
      'CHANGELOG.MD',
      'build/pkg.xml',
      'libraries/zoolanders/lib_zoolanders.xml',
      'plugins/system/zlframework/zlframework.xml',
      'libraries/zoolanders/Framework/Element/**/*.xml'
    ],
    release: args.release
  })

})
