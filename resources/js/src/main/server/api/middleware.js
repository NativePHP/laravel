export default function (secret) {
    return function (req, res, next) {
        if (req.headers['x-native-php-secret'] !== secret) {
            res.sendStatus(403);
            return;
        }
        next();
    };
}
