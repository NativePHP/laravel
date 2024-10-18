import { shell } from "electron";
import { notifyLaravel } from "../../utils";
function triggerMenuItemEvent(menuItem) {
    notifyLaravel('events', {
        event: '\\Native\\Laravel\\Events\\Menu\\MenuItemClicked',
        payload: [
            {
                id: menuItem.id,
                label: menuItem.label,
                checked: menuItem.checked
            }
        ]
    });
}
const mapMenu = (menu) => {
    if (menu.submenu) {
        menu.submenu = menu.submenu.map(mapMenu);
    }
    if (menu.type === 'link') {
        menu.type = 'normal';
        menu.click = () => {
            triggerMenuItemEvent(menu);
            shell.openExternal(menu.url);
        };
        return menu;
    }
    if (menu.type === 'checkbox') {
        menu.click = () => {
            menu.checked = !menu.checked;
            triggerMenuItemEvent(menu);
        };
    }
    if (menu.type === 'event') {
        return {
            label: menu.label,
            accelerator: menu.accelerator,
            click() {
                notifyLaravel('events', {
                    event: menu.event
                });
            }
        };
    }
    if (menu.type === 'role') {
        let menuItem = {
            role: menu.role
        };
        if (menu.label) {
            menuItem['label'] = menu.label;
        }
        return menuItem;
    }
    if (!menu.click) {
        menu.click = () => {
            triggerMenuItemEvent(menu);
        };
    }
    return menu;
};
export { mapMenu, };
