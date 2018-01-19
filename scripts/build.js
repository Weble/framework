import branch from 'git-branch'
import jexec from './util/jexec'
import pkg from '../package.json'
import format from 'date-fns/format'
import { remove, copy, copyRecursive, banner, exec, zip, task } from '@miljan/build'

const bannerTmpl = `/**
 * @package    ZOOlanders Framework ${pkg.version}
 * @copyright  ${pkg.copyright}
 * @license    ${pkg.license}
 */`

;(async () => {
  // create a clean temp folder
  await task('Cleanup', () => remove('dist/tmp/build'))

  // copy over all src files filtering out vendor|tests|node
  await task('Copy source files', () =>
    copyRecursive('src', 'dist/tmp/build', [
      '!phpStorm{,/**}',
      '!**/__tests__{,/**}',
      '!**/node_modules{,/**}',
      '!libraries/zoolanders/vendor{,/**}'
    ])
  )

  // add Joomla PHP jexec check
  await task('Add jexec check', () => jexec('dist/tmp/build/**/*.php'))

  // add banner
  await task('Add banner', () => banner('dist/tmp/build/**/*.php', bannerTmpl))

  // run composer install
  await task('Install vendor - this can take a while...', async (spinner) => {
    await exec(`docker run --rm --interactive \
      --volume $PWD/dist/tmp/build:/app \
      composer install --no-dev --optimize-autoloader --ignore-platform-reqs`
    )
    spinner.text = 'Install vendor'
  })

  await task('Optimize vendor', () => {
    const vendor = 'dist/tmp/build/libraries/zoolanders/vendor'

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

  // build package
  await task('Build main Package', async () => {
    await copy('pkg.xml', 'dist/tmp/pkg', {
      rename: name => `${name.replace('.xml', '')}_zoolanders_framework.xml`
    })
    await copy('dist/tmp/build/administrator/**/*.ini', 'dist/tmp/pkg/language')
    await Promise.all([
      zip('dist/tmp/build/libraries/zoolanders', 'dist/tmp/pkg/packages/lib_zoolanders.zip'),
      zip('dist/tmp/build/plugins/system/zlframework', 'dist/tmp/pkg/packages/plg_zlframework.zip')
    ])
    await zip('dist/tmp/pkg', `dist/${getPackageName()}`)
  })

  // post tasks
  await remove('dist/tmp')
})()

function getPackageName () {
  const date = format(new Date(), 'YYYY-MM-DDTHHmm')
  let branchName = branch.sync()

  if (!branchName) {
    // use travis env
    branchName = process.env.TRAVIS_BRANCH
  }

  return branchName === 'master'
    ? `zoolanders-framework_${pkg.version}.zip`
    : `zoolanders-framework_${pkg.version}_${branchName}_${date}.zip`
}
