import startAPIServer, { APIProcess } from "../../src/server/api";
import axios, { AxiosError } from "axios";
import electron from "electron";

let apiServer: APIProcess;

const mockReadImage = {
  isEmpty: jest.fn(() => true),
  toDataURL: jest.fn(() => 'clipboard image'),
};

jest.mock('electron', () => ({
  dialog: {
    showOpenDialogSync: jest.fn(() => ['open dialog result']),
    showSaveDialogSync: jest.fn(() => ['save dialog result']),
    showMessageBoxSync: jest.fn(() => 1),
    showErrorBox: jest.fn(),
  }
}));

describe('Dialog test', () => {
  beforeEach(async () => {
    apiServer = await startAPIServer('randomSecret')

    axios.defaults.baseURL = `http://localhost:${apiServer.port}/api`;
    axios.defaults.headers.common['x-nativephp-secret'] = 'randomSecret';
  })

  afterEach(done => {
    apiServer.server.close(done);
  });

  it('can open a dialog', async () => {
    const options = {
      title: 'Open Dialog',
      defaultPath: '/home/user/Desktop',
      buttonLabel: 'Open',
      filters: [
        { name: 'Images', extensions: ['jpg', 'png', 'gif'] },
        { name: 'All Files', extensions: ['*'] }
      ],
      message: 'Select an image',
      properties: ['openFile', 'multiSelections']
    }

    const response = await axios.post('/dialog/open', options);

    expect(electron.dialog.showOpenDialogSync).toHaveBeenCalledWith(options);
    expect(response.data.result).toEqual(['open dialog result']);
  });

  it('can open a save dialog', async () => {
    const options = {
      title: 'Open Dialog',
      defaultPath: '/home/user/Desktop',
      buttonLabel: 'Open',
      filters: [
        { name: 'Images', extensions: ['jpg', 'png', 'gif'] },
        { name: 'All Files', extensions: ['*'] }
      ],
      message: 'Select an image',
      properties: ['openFile', 'multiSelections']
    }

    const response = await axios.post('/dialog/save', options);

    expect(electron.dialog.showSaveDialogSync).toHaveBeenCalledWith(options);
    expect(response.data.result).toEqual(['save dialog result']);
  });

  it('can open a message dialog', async () => {
    const options = {
      title: 'Open Dialog',
      message: 'Select an image',
      type: 'info',
      buttons: ['OK', 'Cancel']
    }

    const response = await axios.post('/dialog/message', options);

    expect(electron.dialog.showMessageBoxSync).toHaveBeenCalledWith(options);
    expect(response.data.result).toEqual(1);
  });

  it('can open an error dialog', async () => {
    const options = {
      title: 'Error Dialog',
      message: 'Uh oh!',
    }

    const response = await axios.post('/dialog/error', options);

    expect(electron.dialog.showErrorBox).toHaveBeenCalledWith(options.title, options.message);
    expect(response.data.result).toEqual(true);
  });

});
