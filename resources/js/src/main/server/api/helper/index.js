import {shell} from "electron";
import {notifyLaravel} from "../../index";

const mapMenu = (menu) => {
    if (menu.submenu) {
        menu.submenu = menu.submenu.map(mapMenu)
    }

    if (menu.type === 'link') {
        return {
            label: menu.label,
            accelerator: menu.accelerator,
            click() {
                shell.openExternal(menu.url)
            }
        }
    }

    if (menu.type === 'event') {
        return {
            label: menu.label,
            click() {
                notifyLaravel('events', {
                    event: menu.event
                })
            }
        }
    }

    if (menu.type === 'role') {
        return {
            role: menu.role
        }
    }

    return menu
}

export {
    mapMenu,
}