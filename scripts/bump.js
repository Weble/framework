import pkg from '../package.json'
import { argv } from '@miljan/build'
import bump from './util/bump-version'

(async () => {
  const args = argv()

  await bump([
    'package.json',
    'CHANGELOG.MD',
    'build/pkg.xml',
    'src/libraries/zoolanders/lib_zoolanders.xml',
    'src/plugins/system/zlframework/zlframework.xml',
    'src/libraries/zoolanders/Framework/Element/**/*.xml'
  ], {
    version: pkg.version,
    release: args.release || 'patch'
  })

})()
