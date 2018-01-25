import path from 'path'
import globby from 'globby'
import jexec from './util/jexec'
import pkg from '../package.json'
import { remove, copyRecursive, banner, task, less, minifyJS, minifyCSS } from '@miljan/build'

const bannerMIT = `/**
 * ZOOlanders Framework ${pkg.version}
 * (c) Joolanders
 * license MIT
 */`

const bannerGPL = `/**
 * @package    ZOOlanders Framework ${pkg.version}
 * @copyright  Joolanders
 * @license    GPL
 */`

;(async () => {
  await task('Cleanup', () => remove('dist/tmp/plugin'))

  await task('Copy source files', () =>
    copyRecursive('src/plugins/system/zlframework', 'dist/tmp/plugin')
  )

  await task('Process ZLUX', async () => {
    let sources

    // compiles ZLUX less files
    sources = await globby.sync([
      'dist/tmp/plugin/zlframework/zlux/*/*.less',
      'dist/tmp/plugin/zlframework/zlux/zluxMain.less'
    ])

    await Promise.all(sources.map(src =>
      less({
        src,
        options: {
          relativeUrls: true,
          paths: [ path.resolve(path.dirname(src)) ]
        }
      })
    ))

    await remove('dist/tmp/plugin/zlframework/zlux/**/*.less')

    // minify CSS/JS
    sources = await globby.sync([
      'dist/tmp/plugin/zlframework/zlux/*/*.css',
      'dist/tmp/plugin/zlframework/zlux/zluxMain.css'
    ])

    await Promise.all(sources.map(src =>
      minifyCSS({
        src,
        options: {
          sourceMap: {}
        }
      })
    ))

    sources = await globby.sync([
      'dist/tmp/plugin/zlframework/zlux/*/*.js',
      'dist/tmp/plugin/zlframework/zlux/zluxMain.js'
    ])

    await Promise.all(sources.map(src =>
      minifyJS({
        src,
        options: {
          sourceMap: {}
        }
      })
    ))

    await banner('dist/tmp/plugin/zlframework/zlux/**/*.{css,js}', bannerMIT)
  })

  await task('Process other assets', async () => {
    let sources

    sources = [
      'dist/tmp/plugin/zlframework/assets/libraries/zlux/zlux.less',
      'dist/tmp/plugin/zlframework/elements/separator/tmpl/edit/section/style.less'
    ]

    // compile less files
    await Promise.all(sources.map(src =>
      less({
        src,
        options: {
          relativeUrls: true,
          paths: [ path.resolve(path.dirname(src)) ]
        }
      })
    ))

    await remove(sources)
  })

  await task('Add jexec check', () => jexec('dist/tmp/plugin/**/*.php'))
  await task('Add banner', () => banner('dist/tmp/plugin/**/*.php', bannerGPL))
})()
