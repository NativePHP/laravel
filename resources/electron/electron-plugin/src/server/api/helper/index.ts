import { shell } from 'electron';
import { notifyLaravel, goToUrl } from '../../utils.js';
import state from '../../state.js';

function triggerMenuItemEvent(menuItem, combo) {
    notifyLaravel('events', {
        event: menuItem.event || '\\Native\\Desktop\\Events\\Menu\\MenuItemClicked',
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

export function compileMenu (item) {
    if (item.submenu) {
        if (Array.isArray(item.submenu)) {
            item.submenu = item.submenu?.map(compileMenu);
        } else {
            item.submenu = item.submenu.submenu?.map(compileMenu);
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

            if (! focusedWindow) {
                // TODO: Bring a window to the front?
                return;
            }

            const id = Object.keys(state.windows)
                .find(key => state.windows[key] === focusedWindow);

            goToUrl(item.url, id);
        }

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

    // Default click event
    if (! item.click) {
        item.click = (menuItem, focusedWindow, combo) => {
            triggerMenuItemEvent(item, combo);
        }
    }

    return item;
}
