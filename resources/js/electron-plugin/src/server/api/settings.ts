import express from 'express';
import state from '../state';

const router = express.Router();

router.get('/:key', (req, res) => {
  const key = req.params.key;

  const value = state.store.get(key, null);

  res.json({value});
});

router.post('/:key', (req, res) => {
  const key = req.params.key;
  const value = req.body.value;

  state.store.set(key, value);

  res.sendStatus(200)
});

router.delete('/:key', (req, res) => {
  const key = req.params.key;

  state.store.delete(key);

  res.sendStatus(200)
});

router.delete('/', (req, res) => {
  state.store.clear();

  res.sendStatus(200)
});

export default router;
