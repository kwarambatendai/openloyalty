var path = require('path');
module.exports = {
    entry: {
        app: ["./docs/main.js"]
    },
    output: {
        path: path.resolve(__dirname, "docs")
    }
};
