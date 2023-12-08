// Save JSON object to file in Chrome Devtools
// https://gist.github.com/raecoo/dcbac9e94198dfd0801be8a0cbb14570
// e.g. console.save({hello: 'world'})
(function (console) {
    console.save = function (data, filename) {
        if (!data) {
            console.error('Console.save: No data')
            return;
        }
        if (!filename) filename = 'console.json'
        if (typeof data === "object") {
            data = JSON.stringify(data, undefined, 4)
        }
        var blob = new Blob([data], { type: 'text/json' }),
            a = document.createElement('a')
        var e = new MouseEvent('click', {
            view: window,
            bubbles: true,
            cancelable: false
        });

        a.download = filename
        a.href = window.URL.createObjectURL(blob)
        a.dataset.downloadurl = ['text/json', a.download, a.href].join(':')
        a.dispatchEvent(e)
    }
})(console)
