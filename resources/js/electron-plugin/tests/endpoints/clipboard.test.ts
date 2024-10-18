import startAPIServer, { APIProcess } from "../../src/server/api";
import axios, { AxiosError } from "axios";
import electron from "electron";

let apiServer: APIProcess;

const mockReadImage = {
  isEmpty: jest.fn(() => true),
  toDataURL: jest.fn(() => 'clipboard image'),
};

jest.mock('electron', () => ({
  clipboard: {
    readText: jest.fn(() => 'clipboard text'),
    readHTML: jest.fn(() => 'clipboard html'),
    readImage: jest.fn(() => mockReadImage),
    writeText: jest.fn(),
    writeHTML: jest.fn(),
    writeImage: jest.fn(),
    clear: jest.fn(),
  },
  nativeImage: {
    createFromDataURL: jest.fn(() => 'native image'),
  }
}));

describe('Clipboard test', () => {
  beforeEach(async () => {
    apiServer = await startAPIServer('randomSecret')

    axios.defaults.baseURL = `http://localhost:${apiServer.port}/api`;
    axios.defaults.headers.common['x-nativephp-secret'] = 'randomSecret';
  })

  afterEach(done => {
    apiServer.server.close(done);
  });

  it('can get clipboard contents as text', async () => {
    const response = await axios.get('/clipboard/text');
    expect(response.data.text).toBe('clipboard text');
  });

  it('can write clipboard contents as text', async () => {
    const response = await axios.post('/clipboard/text', {
      text: 'new clipboard text',
    });
    expect(electron.clipboard.writeText).toHaveBeenCalledWith('new clipboard text', 'clipboard');
    expect(response.data.text).toBe('new clipboard text');
  });

  it('can write clipboard contents as HTML', async () => {
    const response = await axios.post('/clipboard/html', {
      html: 'new clipboard HTML',
    });
    expect(electron.clipboard.writeHTML).toHaveBeenCalledWith('new clipboard HTML', 'clipboard');
    expect(response.status).toBe(200);
    expect(response.data.html).toBe('new clipboard HTML');
  });

  it('can get clipboard contents as HTML', async () => {
    const response = await axios.get('/clipboard/html');
    expect(response.data.html).toBe('clipboard html');
  });

  it('returns an error when trying to write an invalid data URL as image', async () => {
    try {
      const response = await axios.post('/clipboard/image', {
        image: 'new image data URL',
      });
    } catch (error) {
      expect(error.response.status).toBe(400);
    }
  });

  it('can write new images to clipboard', async () => {
    const exampleImage = 'example image url';

    const response = await axios.post('/clipboard/image', {
      image: exampleImage,
    });

    expect(electron.nativeImage.createFromDataURL).toHaveBeenCalledWith('example image url');
    expect(electron.clipboard.writeImage).toHaveBeenCalledWith('native image', 'clipboard');
  });

  it('returns null when the clipboard image is empty', async () => {
    const response = await axios.get('/clipboard/image');
    expect(response.data.image).toBe(null);
  });

  it('returns the data URL when the clipboard image is not empty', async () => {
    mockReadImage.isEmpty.mockImplementation(() => false);

    const response = await axios.get('/clipboard/image');
    expect(response.data.image).toBe('clipboard image');
  });

  it('can clear clipboard contents', async () => {
    const response = await axios.delete('/clipboard');
    expect(electron.clipboard.clear).toHaveBeenCalledWith('clipboard');
    expect(response.status).toBe(200);
  });

});
