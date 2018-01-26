import path from 'path'
import globby from 'globby'
import pkg from '../package.json'
import { jexec } from '@zoolanders/build'
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
  await task('Cleanup', () => remove('dist'))

  await task('Copy source files', () => copyRecursive('src', 'dist'))

  await task('Process ZLUX', async () => {
    let sources

    // compiles ZLUX less files
    sources = await globby([
      'dist/zlframework/zlux/*/*.less',
      'dist/zlframework/zlux/zluxMain.less'
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

    await remove('dist/zlframework/zlux/**/*.less')

    // minify CSS/JS
    sources = await globby([
      'dist/zlframework/zlux/*/*.css',
      'dist/zlframework/zlux/zluxMain.css'
    ])

    await Promise.all(sources.map(src =>
      minifyCSS({
        src,
        options: {
          sourceMap: {}
        }
      })
    ))

    sources = await globby([
      'dist/zlframework/zlux/*/*.js',
      'dist/zlframework/zlux/zluxMain.js'
    ])

    await Promise.all(sources.map(src =>
      minifyJS({
        src,
        options: {
          sourceMap: {}
        }
      })
    ))

    await banner('dist/zlframework/zlux/**/*.{css,js}', bannerMIT)
  })

  await task('Process other assets', async () => {
    let sources

    sources = [
      'dist/zlframework/assets/libraries/zlux/zlux.less',
      'dist/zlframework/elements/separator/tmpl/edit/section/style.less'
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

  await task('Add jexec check', () => jexec('dist/**/*.php'))
  await task('Add banner', () => banner('dist/**/*.php', bannerGPL))
})()
