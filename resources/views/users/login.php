<html>
<head>

</head>
<body>
    <p><a href="https://id.twitch.tv/oauth2/authorize?response_type=token&client_id=0gqrdaa6mwgb9d5zk8n2fow8ryve34&redirect_uri=http://localhost/StreamStats/public/login&scope=user:read:follows+viewing_activity_read+openid+user:read:email+analytics:read:games&claims={"id_token":{"email_verified":null}}">Login</a></p>
</body>
<script>
    let domain = '<?php echo env('APP_URL'); ?>';

    function getParamsAfterHash() {
        let url;
        if (typeof url !== "string" || !url) url = window.location.href;
        url = url.split("#")[1];
        if (!url) return {};
        return url.split("&").reduce(function(result, param) {
            var [key, value] = param.split("=");
            result[key] = value;
            return result;
        }, {});
    }

    async function postUserIU(data = {}) {
        if(data.access_token) {
            let url = domain+'/useriu';
            // Default options are marked with *
            const response = await fetch(url, {
                method: 'POST', // *GET, POST, PUT, DELETE, etc.
                mode: 'cors', // no-cors, *cors, same-origin
                cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
                credentials: 'same-origin', // include, *same-origin, omit
                headers: {
                    'Content-Type': 'application/json'
                    // 'Content-Type': 'application/x-www-form-urlencoded',
                },
                redirect: 'follow', // manual, *follow, error
                referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
                body: JSON.stringify(data) // body data type must match "Content-Type" header
            });
            localStorage.setItem('token', getParamsAfterHash()['access_token']);
            window.location.href = '../public/dashboard';
        }
    }
    postUserIU({access_token:getParamsAfterHash()['access_token']});
</script>
</html>