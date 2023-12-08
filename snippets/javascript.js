function xhrPost(url, data, done=null, uploadProgress=null) {
    let formData = new FormData();
    if (data) for (const [k, v] of Object.entries(data)) {
        if (v instanceof FileList)
            for(const file of [...v])
                formData.append(k, file);
        else formData.append(k, v);
    }

    XHR = new XMLHttpRequest();
    XHR.open('POST', url);

    // handlers
    uploadProgress && XHR.upload.addEventListener('progress', uploadProgress);
    done && XHR.addEventListener('load', done);

    XHR.send(formData);
    return XHR;

}
function fetchJson(url){
    return fetch(url).then((r)=>r.ok?r.json():alert(`error fetching ${url}`));
}
function newEl(tagName='div') {
    return document.createElement(tagName);
}
function elById(id){
    return document.getElementById(id);
}
function hrefDir() {
    let href = window.location.href;
    return href.substring(0, href.lastIndexOf('/'));
}
function basename(path) {
    return path.substr(path.lastIndexOf('/') + 1);
}
function getExtension(fname){
    // https://stackoverflow.com/a/12900504
    return fname.slice((Math.max(0, fname.lastIndexOf(".")) || Infinity) + 1);
}
