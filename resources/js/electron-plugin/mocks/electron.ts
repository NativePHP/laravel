module.exports = {
    app: {
        getPath: jest.fn().mockReturnValue('path'),
        isPackaged: jest.fn().mockResolvedValue(function () {
            console.log('isPackaged');
            return false;
        })
    },
    powerMonitor: {
        addListener: jest.fn()
    }
}
