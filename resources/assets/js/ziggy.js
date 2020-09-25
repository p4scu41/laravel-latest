    var Ziggy = {
        namedRoutes: {"login":{"uri":"login","methods":["GET","HEAD"],"domain":null},"logout":{"uri":"logout","methods":["POST"],"domain":null},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"],"domain":null},"password.email":{"uri":"forgot-password","methods":["POST"],"domain":null},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"],"domain":null},"password.update":{"uri":"reset-password","methods":["POST"],"domain":null},"register":{"uri":"register","methods":["GET","HEAD"],"domain":null},"verification.notice":{"uri":"email\/verify","methods":["GET","HEAD"],"domain":null},"verification.verify":{"uri":"email\/verify\/{id}\/{hash}","methods":["GET","HEAD"],"domain":null},"verification.send":{"uri":"email\/verification-notification","methods":["POST"],"domain":null},"password.confirm":{"uri":"user\/confirm-password","methods":["GET","HEAD"],"domain":null},"password.confirmation":{"uri":"user\/confirmed-password-status","methods":["GET","HEAD"],"domain":null},"two-factor.login":{"uri":"two-factor-challenge","methods":["GET","HEAD"],"domain":null},"profile.show":{"uri":"user\/profile","methods":["GET","HEAD"],"domain":null},"other-browser-sessions.destroy":{"uri":"user\/other-browser-sessions","methods":["DELETE"],"domain":null},"current-user.destroy":{"uri":"user","methods":["DELETE"],"domain":null},"current-user-photo.destroy":{"uri":"user\/profile-photo","methods":["DELETE"],"domain":null},"api-tokens.index":{"uri":"user\/api-tokens","methods":["GET","HEAD"],"domain":null},"api-tokens.store":{"uri":"user\/api-tokens","methods":["POST"],"domain":null},"api-tokens.update":{"uri":"user\/api-tokens\/{token}","methods":["PUT"],"domain":null},"api-tokens.destroy":{"uri":"user\/api-tokens\/{token}","methods":["DELETE"],"domain":null},"api.auth.login":{"uri":"api\/auth\/login","methods":["POST"],"domain":null},"api.auth.refresh":{"uri":"api\/auth\/refresh","methods":["POST"],"domain":null},"auth.logout":{"uri":"api\/auth\/logout","methods":["POST"],"domain":null},"auth.me":{"uri":"api\/auth\/me","methods":["POST"],"domain":null},"api.users.store":{"uri":"api\/users","methods":["POST"],"domain":null},"api.users.index":{"uri":"api\/users","methods":["GET","HEAD"],"domain":null},"api.users.show":{"uri":"api\/users\/{user}","methods":["GET","HEAD"],"domain":null},"api.users.update":{"uri":"api\/users\/{user}","methods":["PUT","PATCH"],"domain":null},"api.users.destroy":{"uri":"api\/users\/{user}","methods":["DELETE"],"domain":null},"activitylogs.index":{"uri":"activitylogs","methods":["GET","HEAD"],"domain":null},"activitylogs.show":{"uri":"api\/activitylogs\/{activitylog}","methods":["GET","HEAD"],"domain":null},"utils.commands":{"uri":"utils\/commands","methods":["GET","HEAD"],"domain":null},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"],"domain":null}},
        baseUrl: 'https://laravel-latest.local/',
        baseProtocol: 'https',
        baseDomain: 'laravel-latest.local',
        basePort: false,
        defaultParameters: []
    };

    if (typeof window.Ziggy !== 'undefined') {
        for (var name in window.Ziggy.namedRoutes) {
            Ziggy.namedRoutes[name] = window.Ziggy.namedRoutes[name];
        }
    }

    export {
        Ziggy
    }
