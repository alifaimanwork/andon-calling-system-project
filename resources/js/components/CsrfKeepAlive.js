export class CsrfKeepAlive {
    token = null;
    requestUrl = '/csrf';

    constructor() {
        const refreshInterval = 900000;// refresh every 15 minutes
        let meta = document.head.querySelector("[name=csrf-token][content]");
        if(!meta)
            return;
        let pageToken = meta.content;
        if (!pageToken)
            return;

        this.token = pageToken;
        let self = this;
        setInterval(function () {
            self.renewToken();
        }, refreshInterval);
    }
    getToken() {
        return this.token;
    }
    renewToken() {
        let self = this;
        $.post(`${self.requestUrl}`, {
            _token: this.token
        }, function (data) {
            self.token = data._token;
            document.head.querySelector("[name=csrf-token][content]").content = self.token;
        });
        return this;
    }
}