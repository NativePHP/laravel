import startAPIServer, { APIProcess } from "../src/server/api";
import axios from "axios";

let apiServer: APIProcess;

describe('API test', () => {
  beforeEach(async () => {
    jest.resetModules();
    apiServer = await startAPIServer('randomSecret');
    axios.defaults.baseURL = `http://localhost:${apiServer.port}`;
  })

  afterEach(done => {
    apiServer.server.close(done);
  });

  it('starts API server on port 4000', async () => {
    expect(apiServer.port).toBe(4000);
  });

  it('uses the next available API port', async () => {
    const nextApiProcess = await startAPIServer('randomSecret');
    expect(nextApiProcess.port).toBe(apiServer.port + 1);

    nextApiProcess.server.close();
  });

  it('protects API endpoints with a secret', async () => {
    try {
      await axios.get('/api/process')
    } catch (error) {
      expect(error.response.status).toBe(403);
    }

    const response = await axios.get('/api/process', {
      headers: {
        'x-nativephp-secret': 'randomSecret',
      }
    })

    expect(response.status).toBe(200);
  });
})
