import { join } from 'path';
import { defineConfig, externalizeDepsPlugin } from 'electron-vite';

export default defineConfig({
    main: {
        build: {
            rollupOptions: {
                plugins: [
                    {
                        name: 'watch-external',
                        buildStart() {
                            this.addWatchFile(join(process.env.APP_PATH, 'app', 'Providers', 'NativeAppServiceProvider.php'));
                        }
                    }
                ]
            },
        },
        plugins: [externalizeDepsPlugin()]
    }
});
