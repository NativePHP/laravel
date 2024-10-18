module.exports = {
  coverageReporters: ['json', 'html'],
  preset: 'ts-jest',
  testEnvironment: 'node',
  moduleNameMapper: {
    "electron": "<rootDir>/mocks/electron.ts",
  }
};
