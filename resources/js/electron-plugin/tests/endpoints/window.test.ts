import startAPIServer, { APIProcess } from "../../src/server/api";
import axios from "axios";
import state from "../../src/server/state";

let apiServer: APIProcess;

jest.mock('../../src/server/state', () => ({
    ...jest.requireActual('../../src/server/state'),
    windows: {
        main: {
            hide: jest.fn(),
            show: jest.fn(),
        },
    }
}))

describe('Window test', () => {
  beforeEach(async () => {
    apiServer = await startAPIServer('randomSecret')

    axios.defaults.baseURL = `http://localhost:${apiServer.port}/api`;
    axios.defaults.headers.common['x-nativephp-secret'] = 'randomSecret';
  })

  afterEach(done => {
    apiServer.server.close(done);
  });

  it('can hide a window', async () => {
    const response = await axios.post('/window/hide', { id: 'main' });
    expect(response.status).toBe(200);
    expect(state.windows.main.hide).toHaveBeenCalled();
  });

  it('can show a window', async () => {
    const response = await axios.post('/window/show', { id: 'main' });
    expect(response.status).toBe(200);
    expect(state.windows.main.show).toHaveBeenCalled();
  });
});
