import { exec, task } from '@miljan/build'

(async () => {
  await task('Install vendor - this can take a while...', async (spinner) => {
    await exec(`docker run --rm --interactive \
      --volume $PWD/src:/app \
      composer install --ignore-platform-reqs`
    )
    spinner.text = 'Install vendor'
  })
})()
