import startAPIServer, { APIProcess } from "./api.js";
import {
  retrieveNativePHPConfig,
  retrievePhpIniSettings,
  serveApp,
  startScheduler,
} from "./php.js";
import { appendCookie } from "./utils.js";
import state from "./state.js";
import { ChildProcess } from "child_process";

let schedulerProcess: ChildProcess | null = null;

export async function startPhpApp() {
  const result = await serveApp(
    state.randomSecret,
    state.electronApiPort,
    state.phpIni
  );

  state.phpPort = result.port;

  await appendCookie();

  return result.process;
}

export function runScheduler() {
  killScheduler();
  schedulerProcess = startScheduler(state.randomSecret, state.electronApiPort, state.phpIni);
}

export function killScheduler() {
  if (schedulerProcess && !schedulerProcess.killed) {
    schedulerProcess.kill();
    schedulerProcess = null;
  }
}

export function startAPI(): Promise<APIProcess> {
  return startAPIServer(state.randomSecret);
}

export { retrieveNativePHPConfig, retrievePhpIniSettings };
