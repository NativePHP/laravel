import { existsSync } from "fs";
import { join } from "path";
import { spawn } from "child_process";
import { getAppPath } from "./php";
import state from "./state";
function startWebsockets() {
    if (!existsSync(join(getAppPath(), "vendor", "beyondcode", "laravel-websockets"))) {
        return;
    }
    const phpServer = spawn(state.php, ["artisan", "websockets:serve"], {
        cwd: getAppPath(),
    });
    phpServer.stdout.on("data", (data) => { });
    phpServer.stderr.on("data", (data) => { });
    return phpServer;
}
export default startWebsockets;
