function runReload() {
    setInterval('refreshPage()', 5000);
}

function refreshPage() {
    location.reload();
}

var url = window.location.href;

if (url.search('disabled') >= 0) {
    runReload();
}
