import startAPIServer, { APIProcess } from "./api";
import {
  retrieveNativePHPConfig,
  retrievePhpIniSettings,
  serveApp,
  startQueueWorker,
  startScheduler,
} from "./php";
import { appendCookie } from "./utils";
import state from "./state";

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

export function startQueue() {
  if (!process.env.NATIVE_PHP_SKIP_QUEUE) {
    return startQueueWorker(
      state.randomSecret,
      state.electronApiPort,
      state.phpIni
    );
  } else {
    return undefined;
  }
}

export function runScheduler() {
  startScheduler(state.randomSecret, state.electronApiPort, state.phpIni);
}

export function startAPI(): Promise<APIProcess> {
  return startAPIServer(state.randomSecret);
}

export { retrieveNativePHPConfig, retrievePhpIniSettings };
