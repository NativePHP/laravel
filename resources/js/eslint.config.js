import globals from "globals";
import pluginJs from "@eslint/js";
import tseslint from "typescript-eslint";
import eslintPluginPrettierRecommended from "eslint-plugin-prettier/recommended";
import eslintPluginUnicorn from "eslint-plugin-unicorn";

/** @type {import('eslint').Linter.Config[]} */
export default [
    {
        files: ["**/*.{js,mjs,cjs,ts}"],
    },
    {
        languageOptions: {
            globals: {
                ...globals.builtin,
                ...globals.browser,
                ...globals.node,
            },
        },
    },
    pluginJs.configs.recommended,
    ...tseslint.configs.recommended,
    eslintPluginPrettierRecommended,
    // {
    //     languageOptions: {
    //         globals: globals.builtin,
    //     },
    //     plugins: {
    //         unicorn: eslintPluginUnicorn,
    //     },
    //     rules: {
    //         'unicorn/prefer-module': 'error',
    //     },
    // },
];
