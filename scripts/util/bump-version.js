import path from 'path'
import semver from 'semver'
import globby from 'globby'
import format from 'date-fns/format'
import { read, write } from '@miljan/build'

/*
 * Bump the version of all files
 * release String the bump release, major, minor or patch
 */
export default async function (src, {
  version = '',
  release = 'patch'
}) {
  const newVersion = semver.inc(version, release)

  const files = await globby(src, {
    nodir: true
  })

  if (files.length === 0) {
    throw new Error('No files matched for the operation.')
  }

  // process them all
  await Promise.all(
    files.map(async src => bump(src, newVersion))
  )
}

async function bump (src, version) {
  const ext = path.extname(src)
  const basename = path.basename(src)
  let content = await read(src)

  if (ext === '.xml') {
    content = bumpXML(content, version)
  } else if (basename === 'package.json') {
    content = bumpPkg(content, version)
  } else if (basename.match(/CHANGELOG/)) {
    content = bumpChangelog(content, version)
  } else {
    return
  }

  await write(src, content)
}

function bumpXML (content, version) {
  const today = format(new Date(), 'MMMM YYYY')
  return content
    .replace(/<version>(.*)<\/version>/, `<version>${version}</version>`)
    .replace(/<creationDate>(.*)<\/creationDate>/, `<creationDate>${today}</creationDate>`)
}

function bumpPkg (content, version) {
  return content
    .replace(/"version":."(.+)"/, `"version": "${version}"`)
}

function bumpChangelog (content, version) {
  return content
    .replace(/###.WIP/, `### ${version}`)
}
