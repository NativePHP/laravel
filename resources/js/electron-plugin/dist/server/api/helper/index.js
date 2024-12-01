import { shell } from 'electron';
import { notifyLaravel, goToUrl } from '../../utils';
import state from '../../state';
function triggerMenuItemEvent(menuItem, combo) {
    notifyLaravel('events', {
        event: menuItem.event || '\\Native\\Laravel\\Events\\Menu\\MenuItemClicked',
        payload: {
            item: {
                id: menuItem.id,
                label: menuItem.label,
                checked: menuItem.checked,
            },
            combo,
        },
    });
}
export function compileMenu(item) {
    var _a, _b;
    if (item.submenu) {
        if (Array.isArray(item.submenu)) {
            item.submenu = (_a = item.submenu) === null || _a === void 0 ? void 0 : _a.map(compileMenu);
        }
        else {
            item.submenu = (_b = item.submenu.submenu) === null || _b === void 0 ? void 0 : _b.map(compileMenu);
        }
    }
    if (item.type === 'link') {
        item.type = 'normal';
        item.click = (menuItem, focusedWindow, combo) => {
            triggerMenuItemEvent(item, combo);
            if (item.openInBrowser) {
                shell.openExternal(item.url);
                return;
            }
            if (!focusedWindow) {
                return;
            }
            const id = Object.keys(state.windows)
                .find(key => state.windows[key] === focusedWindow);
            goToUrl(item.url, id);
        };
        return item;
    }
    if (item.type === 'checkbox' || item.type === 'radio') {
        item.click = (menuItem, focusedWindow, combo) => {
            item.checked = !item.checked;
            triggerMenuItemEvent(item, combo);
        };
        return item;
    }
    if (item.type === 'role') {
        let menuItem = {
            role: item.role
        };
        if (item.label) {
            menuItem['label'] = item.label;
        }
        return menuItem;
    }
    if (!item.click) {
        item.click = (menuItem, focusedWindow, combo) => {
            triggerMenuItemEvent(item, combo);
        };
    }
    return item;
}
