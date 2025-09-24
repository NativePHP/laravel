"use strict";
class Positioner {
    constructor(browserWindow) {
        this.browserWindow = browserWindow;
        this.electronScreen = require("electron").screen;
    }
    _getCoords(position, trayPosition) {
        let screenSize = this._getScreenSize(trayPosition);
        let windowSize = this._getWindowSize();
        if (trayPosition === undefined)
            trayPosition = {};
        let positions = {
            trayLeft: {
                x: Math.floor(trayPosition.x),
                y: screenSize.y,
            },
            trayBottomLeft: {
                x: Math.floor(trayPosition.x),
                y: Math.floor(screenSize.height - (windowSize[1] - screenSize.y)),
            },
            trayRight: {
                x: Math.floor(trayPosition.x - windowSize[0] + trayPosition.width),
                y: screenSize.y,
            },
            trayBottomRight: {
                x: Math.floor(trayPosition.x - windowSize[0] + trayPosition.width),
                y: Math.floor(screenSize.height - (windowSize[1] - screenSize.y)),
            },
            trayCenter: {
                x: Math.floor(trayPosition.x - windowSize[0] / 2 + trayPosition.width / 2),
                y: screenSize.y,
            },
            trayBottomCenter: {
                x: Math.floor(trayPosition.x - windowSize[0] / 2 + trayPosition.width / 2),
                y: Math.floor(screenSize.height - (windowSize[1] - screenSize.y)),
            },
            topLeft: {
                x: screenSize.x,
                y: screenSize.y,
            },
            topRight: {
                x: Math.floor(screenSize.x + (screenSize.width - windowSize[0])),
                y: screenSize.y,
            },
            bottomLeft: {
                x: screenSize.x,
                y: Math.floor(screenSize.height - (windowSize[1] - screenSize.y)),
            },
            bottomRight: {
                x: Math.floor(screenSize.x + (screenSize.width - windowSize[0])),
                y: Math.floor(screenSize.height - (windowSize[1] - screenSize.y)),
            },
            topCenter: {
                x: Math.floor(screenSize.x + (screenSize.width / 2 - windowSize[0] / 2)),
                y: screenSize.y,
            },
            bottomCenter: {
                x: Math.floor(screenSize.x + (screenSize.width / 2 - windowSize[0] / 2)),
                y: Math.floor(screenSize.height - (windowSize[1] - screenSize.y)),
            },
            leftCenter: {
                x: screenSize.x,
                y: screenSize.y +
                    Math.floor(screenSize.height / 2) -
                    Math.floor(windowSize[1] / 2),
            },
            rightCenter: {
                x: Math.floor(screenSize.x + (screenSize.width - windowSize[0])),
                y: screenSize.y +
                    Math.floor(screenSize.height / 2) -
                    Math.floor(windowSize[1] / 2),
            },
            center: {
                x: Math.floor(screenSize.x + (screenSize.width / 2 - windowSize[0] / 2)),
                y: Math.floor((screenSize.height + screenSize.y) / 2 - windowSize[1] / 2),
            },
        };
        if (position.substr(0, 4) === "tray") {
            if (positions[position].x + windowSize[0] >
                screenSize.width + screenSize.x) {
                return {
                    x: positions["topRight"].x,
                    y: positions[position].y,
                };
            }
        }
        return positions[position];
    }
    _getWindowSize() {
        return this.browserWindow.getSize();
    }
    _getScreenSize(trayPosition) {
        if (trayPosition) {
            return this.electronScreen.getDisplayMatching(trayPosition)
                .workArea;
        }
        else {
            return this.electronScreen.getDisplayNearestPoint(this.electronScreen.getCursorScreenPoint()).workArea;
        }
    }
    move(position, trayPos) {
        var coords = this._getCoords(position, trayPos);
        this.browserWindow.setPosition(coords.x, coords.y);
    }
    calculate(position, trayPos) {
        var coords = this._getCoords(position, trayPos);
        return {
            x: coords.x,
            y: coords.y,
        };
    }
}
export default Positioner;
