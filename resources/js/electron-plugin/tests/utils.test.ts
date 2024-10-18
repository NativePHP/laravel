import { notifyLaravel } from "../src/server/utils";
import state from "../src/server/state";
import axios from "axios";

jest.mock('axios', () => ({
  post: jest.fn(),
}));

describe('Utils test', () => {

  it('notifies laravel', async () => {
    state.phpPort = 8000;
    state.randomSecret = 'i-am-secret';

    notifyLaravel('endpoint', { payload: 'payload' });

    expect(axios.post).toHaveBeenCalledWith(
      `http://127.0.0.1:8000/_native/api/endpoint`,
      { payload: 'payload' },
      {
        headers: {
          "X-NativePHP-Secret": 'i-am-secret',
        }
      }
    );
  });

});
