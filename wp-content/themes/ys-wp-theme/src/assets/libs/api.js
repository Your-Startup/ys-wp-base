export default class ysApi {
    static location = typeof YS_SITE_DATA !== 'undefined' ? YS_SITE_DATA.root : '';
    static init = {
        headers: {
            'X-WP-Nonce': typeof YS_SITE_DATA !== 'undefined' ? YS_SITE_DATA.nonce : ''
        }
    }

    static async get(endpoint, params = {}) {
        return await this.fetch(endpoint, params, 'get');
    }

    static async post(endpoint, params) {
        return await this.fetch(endpoint, params, 'post')
    }

    static async delete(endpoint, params) {
        return await this.fetch(endpoint, params, 'delete')
    }

    static async patch(endpoint, params) {
        return await this.fetch(endpoint, params, 'patch')
    }

    static async put(endpoint, params) {
        return await this.fetch(endpoint, params, 'put')
    }

    static async fetch(endpoint, params, method = 'get') {
        let url  = new URL(this.location + endpoint),
            init = {
                ...this.init,
                method: method
            }

        if (['post', 'patch', 'delete', 'put'].includes(method)) {
            init.body = JSON.stringify(params);
            init.headers['Content-Type'] = 'application/json;charset=utf-8'
        }

        if (['get'].includes(method)) {
            url.search = new URLSearchParams(params).toString();
        }

        return fetch(url.toString(), init).then(response => response.json())
    }
}