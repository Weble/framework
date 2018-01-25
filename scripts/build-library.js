import jexec from './util/jexec'
import pkg from '../package.json'
import { remove, copy, copyRecursive, banner, exec, task } from '@miljan/build'

const bannerGPL = `/**
 * @package    ZOOlanders Framework ${pkg.version}
 * @copyright  Joolanders
 * @license    GPL
 */`

;(async () => {
  // create a clean temp folder
  await remove('dist/tmp/library')

  // copy over all src files filtering out vendor|tests|node
  await task('Copy source files', () => Promise.all([
    copyRecursive('src/libraries', 'dist/tmp/libraries', [
      '!phpStorm{,/**}',
      '!**/__tests__{,/**}',
      '!**/node_modules{,/**}',
      '!libraries/zoolanders/vendor{,/**}'
    ]),
    copy('src/composer.*', 'dist/tmp')
  ]))

  // add Joomla PHP jexec check
  await task('Add jexec check', () => jexec('dist/tmp/library/**/*.php'))

  // add banner
  await task('Add banner', () => banner('dist/tmp/library/**/*.php', bannerGPL))

  // run composer install
  await task('Install vendor - this can take a while...', async (spinner) => {
    await exec(`docker run --rm --interactive \
      --volume $PWD/dist/tmp:/app \
      composer install --no-dev --optimize-autoloader --ignore-platform-reqs`
    )
    spinner.text = 'Install vendor'
  })

  await task('Optimize vendor', () => {
    const vendor = 'dist/tmp/libraries/zoolanders/vendor'

    remove([
      // common unnecessary files
      `${vendor}/**/.*`,
      `${vendor}/**/*.md`,
      `${vendor}/**/*.txt`,
      `${vendor}/**/*.pdf`,
      `${vendor}/**/Gemfile`,
      `${vendor}/**/Makefile`,
      `${vendor}/**/Dockerfile*`,
      `${vendor}/**/package.json`,
      `${vendor}/**/build.xml`,
      `${vendor}/**/travis-ci.xml`,
      `${vendor}/**/appveyor.yml`,
      `${vendor}/**/README*`,
      `${vendor}/**/LICENSE*`,
      `${vendor}/**/LICENCE*`,
      `${vendor}/**/CHANGES*`,
      `${vendor}/**/VERSION*`,
      `${vendor}/**/AUTHORS*`,
      `${vendor}/**/UPGRADE*`,
      `${vendor}/**/CHANGELOG*`,
      `${vendor}/**/composer.json`,
      `${vendor}/**/composer.lock`,

      // ...folders
      `${vendor}/**/bin{,/**}`,
      `${vendor}/**/doc{,/**}`,
      `${vendor}/**/docs{,/**}`,
      `${vendor}/**/examples{,/**}`,

      // ..git
      `${vendor}/**/.git{,/**}`,
      `${vendor}/**/.gitkeep`,
      `${vendor}/**/.gitignore`,

      // ...tests
      `${vendor}/**/tests{,/**}`,
      `${vendor}/**/Tests{,/**}`,
      `${vendor}/**/unitTests{,/**}`,
      `${vendor}/**/phpunit*`
    ])
  })

})()
