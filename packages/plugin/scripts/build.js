import path from 'path'
import globby from 'globby'
import pkg from '../package.json'
import { jexec } from '@zoolanders/build'
import { remove, copyRecursive, banner, task, less, minify } from '@miljan/build'

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
    // compile less files
    sources = [
      'dist/zlframework/assets/libraries/zlux/zlux.less'
    ]
    await Promise.all(sources.map(src =>
      less({
        src
      })
    ))
    await remove(sources)

    // minify
    await minify([
      'dist/zlframework/assets/libraries/zlux/*.css'
    ], { sourceMap: true })

    await banner('dist/zlframework/assets/libraries/zlux/*.css', bannerMIT)
  })

  await task('Process ZLUX 2', async () => {
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
    sources = await minify([
      'dist/zlframework/zlux/*/*.{css,js}',
      'dist/zlframework/zlux/zluxMain.{css,js}'
    ], { sourceMap: true })

    await banner('dist/zlframework/zlux/**/*.{css,js}', bannerMIT)
  })

  await task('Process Other Assets', async () => {
    const sources = [
      'dist/zlframework/assets/css/*.css',
      'dist/zlframework/elements/filespro/*.js',
      'dist/zlframework/elements/repeatablepro/*.{css,js}',
      'dist/zlframework/elements/separator/tmpl/edit/*/*.{css,js}'
    ]

    await minify(sources, { sourceMap: true })
    await banner(sources, bannerMIT)
  })

  await task('Add jexec check', () => jexec('dist/**/*.php'))
  await task('Add banner', () => banner('dist/**/*.php', bannerGPL))
})()
