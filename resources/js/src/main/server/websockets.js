import { join } from 'path'
import { spawn } from 'child_process'
import { getAppPath } from './php'
import php from '../../../resources/php?asset&asarUnpack'

function serveWebsockets() {
  const phpServer = spawn(php, ['artisan', 'websockets:serve'], {
    cwd: getAppPath(),
  })

  phpServer.stdout.on('data', (data) => {
  })

  phpServer.stderr.on('data', (data) => {
  })
  return phpServer;
}

export default serveWebsockets;
