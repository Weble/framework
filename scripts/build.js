import pkg from '../package.json'
import { pkgName } from '@zoolanders/build'
import { remove, copy, exec, zip, task } from '@miljan/build'

(async () => {
  await remove('dist/tmp')

  await task('Build Library - this can take a while...', async spinner => {
    await exec('yarn build', { cwd: 'packages/library' })
    await zip('packages/library/dist', 'dist/tmp/packages/lib_zoolanders.zip')
    spinner.text = 'Build Library'
  })

  await task('Build Plugin', async () => {
    await exec('yarn build', { cwd: 'packages/plugin' })
    await zip('packages/plugin/dist', 'dist/tmp/packages/plg_zlframework.zip')
  })

  await task('Package', async () => {
    await copy(['CHANGELOG.md', 'packages/package/*'], 'dist/tmp')
    await zip('dist/tmp', `dist/${pkgName('zl-framework', pkg.version)}`)
  })

  await remove('dist/tmp')
})()
