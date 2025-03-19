<?php

namespace Native\Electron\Traits;

trait SetsAppName
{
    protected function setAppName($developmentMode = false): string
    {
        $packageJsonPath = __DIR__.'/../../resources/js/package.json';
        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        $name = str(config('app.name'))->slug();

        /*
         * Suffix the app name with '-dev' if it's a development build
         * this way, when the developer test his freshly built app,
         * configs, migrations won't be mixed up with the production app
         */
        if ($developmentMode) {
            $name .= '-dev';
        }

        $packageJson['name'] = $name;

        file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $name;
    }
}
