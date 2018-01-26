import pkg from '../package.json'
import { jexec } from '@zoolanders/build'
import { remove, copyRecursive, banner, exec, task } from '@miljan/build'

const bannerGPL = `/**
 * @package    ZOOlanders Framework ${pkg.version}
 * @copyright  Joolanders
 * @license    GPL
 */`

;(async () => {
  await remove('dist')

  // copy files filtering out vendor|tests|node
  await task('Copy Files', () => Promise.all([
    copyRecursive('src', 'dist', [
      '!phpStorm{,/**}',
      '!**/__tests__{,/**}',
      '!**/node_modules{,/**}',
      '!vendor{,/**}'
    ])
  ]))

  await task('Add jexec check', () => jexec('dist/**/*.php'))
  await task('Add banner', () => banner('dist/**/*.php', bannerGPL))

  // run composer install
  await task('Install vendor - this can take a while...', async (spinner) => {
    await exec(`docker run --rm --interactive \
      --volume $PWD/dist:/app \
      composer install --no-dev --optimize-autoloader --ignore-platform-reqs`
    )
    await remove('dist/composer.*')
    spinner.text = 'Install vendor'
  })

  await task('Optimize vendor', () => {
    const vendor = 'dist/vendor'

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
