export class Cookies {
    /**
     *  Returns value of requested cookie or null if such cookie doesn't exist
     *
     *  @param  {String} key Cookie's key
     *  @return {String|null} Cookie's value or mull if such cookie doesn't exist
     */
    static get(key) {
        let matches = document.cookie.match(new RegExp('(?:^|;\\s*)' + encodeURIComponent(key).replace(/[^\w%]/g, '\\$&') + '\\s*=\\s*([^;]*)(?:$|;)'));
        return matches ? decodeURIComponent(matches[1]) : null;
    };

    /**
     *  Sets cookie
     *
     *  @param {String}      key
     *  @param {*}           value
     *  @param {Date|Number} [expires] Date object with the expires date or Number with the time of cookie life
     *                                 in seconds. Can be Infinity what equals 31 Dec 9999 23:59:59 GMT
     *  @param {String}      [path]
     *  @param {String}      [domain]
     *  @param {Boolean}     [secure=false]
     */
    static set(key, value, expires, path, domain, secure) {
        let parts = [encodeURIComponent(key) + '=' + encodeURIComponent(value)];

        if (expires) {
            if (expires.constructor === Date) {
                parts.push('expires=' + expires.toUTCString());
            } else if (expires.constructor === Number) {
                parts.push(expires === Infinity ? 'expires=Fri, 31 Dec 9999 23:59:59 GMT' : 'max-age=' + expires);
            }
        }

        domain && parts.push('domain=' + domain);
        path && parts.push('path=' + path);
        secure && parts.push('secure');

        document.cookie = parts.join('; ');
    };

    /**
     *  Deletes cookie
     *
     *  @param {String} key
     *  @param {String} [path]
     *  @param {String} [domain]
     */
    static delete(key, path, domain) {
        let parts = [encodeURIComponent(key) + '=', 'expires=Thu, 01 Jan 1970 00:00:00 GMT'];

        domain && parts.push('domain=' + domain);
        path && parts.push('path=' + path);

        document.cookie = parts.join('; ');
    }

    /**
     *  Returns true if cookie exists, else returns false
     *
     *  @param  {String}  key
     *  @return {Boolean}
     */
    static exists(key) {
        return new RegExp('(?:^|;\\s*)' + encodeURIComponent(key).replace(/[^\w%]/g, '\\$&') + '=').test(document.cookie);
    }

}