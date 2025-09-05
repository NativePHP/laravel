# Menubar Library

This is a vendored copy of the [menubar](https://github.com/max-mapper/menubar) library, originally created by max-mapper.

This library is pulled in from the https://github.com/max-mapper/menubar repository. The package sadly is not actively maintained, preventing our ability to update Electron dependencies in a timely manner.

We decided to pull the lib in directly instead of maintaining a fork to:

- Control our dependency update timeline
- Ensure compatibility with newer Electron versions
- Maintain stability for NativePHP desktop applications

## About Menubar

Menubar is a high-level Electron library for creating desktop menubar applications. It provides boilerplate for setting up a menubar application using Electron.

## NativePHP Integration

This vendored copy is specifically maintained for use within the NativePHP Electron backend, enabling PHP developers to create native desktop applications with menubar functionality.
