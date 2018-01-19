import path from 'path'
import globby from 'globby'
import { read, write } from '@miljan/build'

const JEXEC = `defined('_JEXEC') or die('Restricted access');`

/*
 * Add JEXEC check to PHP files
 */
export default async (src) => {
  const files = await globby(src, {
    nodir: true
  })

  await Promise.all(files.map(
    async file => {
      const ext = path.extname(file)

      if (ext !== '.php') {
        return
      }

      let content = await read(file)

      if (content.match(/namespace (.*);/)) {
        content = content.replace(/(namespace (.*);)/, `$1\n\n${JEXEC}`)
      } else {
        content = content.replace(/^(<\?php)/g, `$1\n\n${JEXEC}`)
      }

      await write(file, content)
    }
  ))
}
