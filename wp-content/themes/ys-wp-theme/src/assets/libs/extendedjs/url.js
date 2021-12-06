export function getUrlParam (param, url) {
    if(typeof param === "undefined") {
        return null;
    }

    if (!url) url = window.location.href;
    param = param.replace(/[\[\]]/g, "\\$&");
    let regex = new RegExp("[?&]" + param + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
