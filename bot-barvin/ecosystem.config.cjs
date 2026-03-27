module.exports = {
    apps: [
        {
            name: "SERVER",
            script: "./app.js",
            watch: false
        },
        {
            name: "COLORINGBOOK",
            script: "./src/capabilities/coloring-book/services/coloring-book-processor.js",
            watch: false
        }
    ]
}
