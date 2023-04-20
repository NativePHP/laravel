import { join } from 'path'
import { spawn } from 'child_process'
import { getAppPath } from './php'
import php from '../../../resources/php?asset&asarUnpack'

function serveWebsockets() {
  const phpServer = spawn(php, ['artisan', 'websockets:serve'], {
    cwd: getAppPath(),
  })

  phpServer.stdout.on('data', (data) => {
    console.log(data.toString())
  })

  phpServer.stderr.on('data', (data) => {
    console.error(data.toString())
  })
  return phpServer;
}

export default serveWebsockets;
