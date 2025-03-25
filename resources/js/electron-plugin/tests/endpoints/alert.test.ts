import startAPIServer, {APIProcess} from "../../src/server/api";
import axios from "axios";
import electron from "electron";

let apiServer: APIProcess;

jest.mock('electron', () => ({
    ...jest.requireActual('electron'),

    dialog: {
        showMessageBoxSync: jest.fn(() => 1),
        showErrorBox: jest.fn(),
    }
}));

describe('Alert test', () => {
  beforeEach(async () => {
    apiServer = await startAPIServer('randomSecret')

    axios.defaults.baseURL = `http://localhost:${apiServer.port}/api`;
    axios.defaults.headers.common['x-nativephp-secret'] = 'randomSecret';
  })

  afterEach(done => {
    apiServer.server.close(done);
  });

  it('can open a alert', async () => {
    const options = {
      message: 'Do you really want to delete this?',
      type: 'info',
      title: 'Are you sure',
      detail: 'This action cannot be undone',
      buttons: ['Delete', 'Cancel'],
      defaultId: 0,
      cancelId: 1
    }

    const response = await axios.post('/alert/message', options);

    expect(electron.dialog.showMessageBoxSync).toHaveBeenCalledWith(options);
    expect(response.data.result).toEqual(1);
  });

  it('can open an error alert', async () => {
    const options = {
      title: 'Error Dialog',
      message: 'Uh oh!',
    }

    const response = await axios.post('/alert/error', options);

    expect(electron.dialog.showErrorBox).toHaveBeenCalledWith(options.title, options.message);
    expect(response.data.result).toEqual(true);
  });
});
