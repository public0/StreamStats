<html>
<head>

</head>
<body>
    <p>Hello</p>
</body>
<script>
    const [hash, query] = window.location.href.split('#')[1].split('?')
    const params = Object.fromEntries(new URLSearchParams(query))

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
        let url = 'http://localhost/StreamStats/public/useriu';
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
    postUserIU({access_token:getParamsAfterHash()['access_token']});
</script>
</html>