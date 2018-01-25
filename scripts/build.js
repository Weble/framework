import branch from 'git-branch'
import pkg from '../package.json'
import format from 'date-fns/format'
import { remove, copy, exec, zip, task } from '@miljan/build'

(async () => {
  await remove('dist/tmp')

  await task('Build Plugin', async (spinner) => exec('yarn build:plugin'))

  await task('Build Library - this can take a while...', async (spinner) => {
    await exec('yarn build:library')
    spinner.text = 'Build Library'
  })

  await task('Package', async () => {
    await copy('pkg.xml', 'dist/tmp/package', {
      rename: name => `${name.replace('.xml', '')}_zoolanders_framework.xml`
    })
    await copy('src/administrator/**/*.ini', 'dist/tmp/package/language')
    await Promise.all([
      zip('dist/tmp/libraries/zoolanders', 'dist/tmp/package/packages/lib_zoolanders.zip'),
      zip('dist/tmp/plugin', 'dist/tmp/package/packages/plg_zlframework.zip')
    ])
    await zip('dist/tmp/package', `dist/${getPackageName()}`)
  })

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
