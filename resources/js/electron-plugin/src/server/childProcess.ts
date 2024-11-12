const { spawn } = require('child_process')

const proc = spawn(
    process.argv[2],
    process.argv.slice(3)
);

process.parentPort.on('message', (message) => {
    proc.stdin.write(message.data)
});

// Handle normal output
proc.stdout.on('data', (data) => {
    console.log(data.toString());
});

// Handle error output
proc.stderr.on('data', (data) => {
    console.error(data.toString());
});

// Handle process exit
proc.on('close', (code) => {
    process.exit(code)
});
