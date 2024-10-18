export default function (secret) {
    return function (req, res, next) {
        if (req.headers['x-nativephp-secret'] !== secret) {
            res.sendStatus(403);
            return;
        }
        next();
    };
}
