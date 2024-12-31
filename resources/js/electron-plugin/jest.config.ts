import {createJsWithBabelEsmPreset, type JestConfigWithTsJest} from 'ts-jest'

const presetConfig = createJsWithBabelEsmPreset({
    //...options
})

const jestConfig: JestConfigWithTsJest = {
    ...presetConfig,

    moduleNameMapper: {
        "^electron$": "<rootDir>/mocks/electron.ts",
        "^(\\.{1,2}/.*)\\.js$": "$1",
    },
    transformIgnorePatterns: [
        // 'node_modules\\/(?!get-port\\/|electron-store\\/|conf|dot-prop\\/)'
    ],
    testEnvironment: 'node',
}

console.log(jestConfig);

export default jestConfig
