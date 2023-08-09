const {notarize} = require('@electron/notarize')

module.exports = async (context) => {
    if (process.platform !== 'darwin') return

    console.log('aftersign hook triggered, start to notarize app.')

    if (!('NATIVEPHP_APPLE_ID' in process.env && 'NATIVEPHP_APPLE_ID_PASS' in process.env && 'NATIVEPHP_APPLE_TEAM_ID' in process.env)) {
        console.warn('skipping notarizing, NATIVEPHP_APPLE_ID, NATIVEPHP_APPLE_ID_PASS and NATIVEPHP_APPLE_TEAM_ID env variables must be set.')
        return
    }

    const appId = process.env.NATIVEPHP_APP_ID;

    const {appOutDir} = context

    const appName = context.packager.appInfo.productFilename

    try {
        await notarize({
            appBundleId: appId,
            appPath: `${appOutDir}/${appName}.app`,
            appleId: process.env.NATIVEPHP_APPLE_ID,
            appleIdPassword: process.env.NATIVEPHP_APPLE_ID_PASS,
            teamId: process.env.NATIVEPHP_APPLE_TEAM_ID,
            tool: 'notarytool',
        })
    } catch (error) {
        console.error(error)
    }

    console.log(`done notarizing ${appId}.`)
}
