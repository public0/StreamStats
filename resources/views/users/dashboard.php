<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Twitch Test!</title>
    <style>
        body {
            background-color: gray;
        }
    </style>
</head>
<body>
<div class="container text-left">
    <div>
        <button id="gameStreamCountBtn" type="button" class="btn btn-primary">Streamer Count + median + Viewer count</button>
        <div id="gameStreamCount">
            <div id="gameStreamCountSpinner" class="spinner spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div id="gameStreamCountBody" class="" ></div>
        </div>
        <br />
        <button id="gameViewerCountBtn" type="button" class="btn btn-primary">Top games by viewer DESC</button>
        <button id="gameViewerCountBtnDesc" type="button" class="btn btn-primary">Top games by viewer ASC</button>
        <div id="gameViewerCount">
            <div id="gameViewerCountSpinner" class="spinner spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div id="gameViewerCountBody" class="" ></div>
        </div>
        <br />
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <button id="streamsByDateBtn" type="button" class="btn btn-primary">Streams by date</button>
            </div>
            <input id="streamsByDatePages" type="number" step="1" class="form-control col-1" placeholder="" value="1" aria-label="Page count" aria-describedby="basic-addon1">
        </div>
        <div id="streamsByDate">
            <div id="streamsByDateSpinner" class="spinner spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div id="streamsByDateBody" class="" ></div>
        </div>
        <br />
        <button id="topFollowedBtn" type="button" class="btn btn-primary">Top 1000 followed</button>
        <div id="topFollowed">
            <div id="topFollowedSpinner" class="spinner spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div id="topFollowedBody" class="" ></div>
        </div>
        <br />
        <button id="lowestBtn" type="button" class="btn btn-primary">Lowest followed to be top 1000</button>
        <div id="lowest">
            <div id="lowestSpinner" class="spinner spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div id="lowestBody" class="" ></div>
        </div>
        <br />
        <button id="tagsBtn" type="button" class="btn btn-primary">Tags</button>
        <div id="tags">
            <div id="tagsSpinner" class="spinner spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div id="tagsBody" class="" ></div>
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script>
    let domain = '<?php echo env('APP_URL'); ?>';
    $(function(){
        $('.spinner').hide();
        $("button").click(function(){
            switch ($(this).attr('id')) {
                case 'gameStreamCountBtn':
                    getGameStreams();
                break;
                case 'gameViewerCountBtn':
                    getGameViewers('asc');
                break;
                case 'gameViewerCountBtnDesc':
                    getGameViewers('desc');
                break;
                case 'streamsByDateBtn':
                    getDateStreams($('#streamsByDatePages').val());
                break;
                case 'topFollowedBtn':
                    getTop1000();
                break;
                case 'lowestBtn':
                    getLowest();
                break;
                case 'tagsBtn':
                    getTags();
                break;
            }
        });
    });

    async function getGameStreams() {
        let url = domain+'/gamestreams';
        // Default options are marked with *
        $('#gameStreamCountSpinner').show();
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('token')

                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        });
        $('#gameStreamCountSpinner').hide();
        let data = await response.json();

        let output = '<ul class="list-group">';
        output+='<li class="list-group-item list-group-item list-group-item-secondary">Median: '+data.anals.median+' viewers</li>';
        data.games.data.forEach(game => {
            output+='<li class="list-group-item list-group-item list-group-item-secondary">'+game.name+', '+game.streamer_count+' streamers, '+game.viewer_count+' viewers</li>';
        });
        output+='</ul>';
        $('#gameStreamCountBody').html(output);
    }

    async function getGameViewers(order) {
        let url = domain+'/topstreams/'+order;
        // Default options are marked with *
        $('#gameViewerCountSpinner').show();
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('token')

                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        });
        $('#gameViewerCountSpinner').hide();
        let data = await response.json();
        let output = '<ul class="list-group">';
        data.forEach(game => {
            output+='<li class="list-group-item list-group-item list-group-item-secondary">'+game.game_name+', '+game.viewer_count+' views</li>';
        });
        output+='</ul>';
        $('#gameViewerCountBody').html(output);
    }

    async function getDateStreams(pages) {
        let url = domain+'/datestreams/'+pages;
        // Default options are marked with *
        $('#streamsByDateSpinner').show();
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('token')

                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        });
        $('#streamsByDateSpinner').hide();
        let data = await response.json();
        let output = '<ul class="list-group">';
        data.forEach(stream => {
            output+='<li class="list-group-item list-group-item list-group-item-secondary">'+stream.user_name+', '+stream.started_at+'</li>';
        });
        output+='</ul>';
        $('#streamsByDateBody').html(output);
    }

    async function getTop1000() {
        let url = domain+'/followed/';
        // Default options are marked with *
        $('#topFollowedSpinner').show();
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('token')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        });
        $('#topFollowedSpinner').hide();
        let data = await response.json();
        let output = '<ul class="list-group">';
        data.forEach(stream => {
            output+='<li class="list-group-item list-group-item list-group-item-secondary">'+stream.user_name+', '+stream.started_at+'</li>';
        });
        output+='</ul>';
        $('#topFollowedBody').html(output);
    }

    async function getLowest() {
        let url = domain+'/lowest/';
        // Default options are marked with *
        $('#lowestSpinner').show();
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('token')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
//            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        });
        $('#lowestSpinner').hide();
        let data = await response.json();
        let output = '<ul class="list-group">';
        output+='<li class="list-group-item list-group-item list-group-item-secondary">'+data.result+'</li>';
        output+='</ul>';
        $('#lowestBody').html(output);
    }
    async function getTags() {
        let url = domain+'/tags/';
        // Default options are marked with *
        $('#tagsSpinner').show();
        const response = await fetch(url, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer '+localStorage.getItem('token')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
//            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        });
        $('#tagsSpinner').hide();
        let data = await response.json();
        let output = '<ul class="list-group">';
        output+='<li class="list-group-item list-group-item list-group-item-secondary"><b>Tag Owners and transaltions</b></li>';
        for(let key in data.result.followed_tags) {
            if (data.result.followed_tags.hasOwnProperty(key)) {
                output+='<li class="list-group-item list-group-item list-group-item-secondary">'+data.result.followed_tags[key].user_login+'</li>';
                for(let tag in data.result.followed_tags[key].tags) {
                    if (data.result.followed_tags[key].tags.hasOwnProperty(tag)) {
                        output+='<li class="list-group-item list-group-item list-group-item-secondary">'+
                            'Tag ID: '+tag + ' - '+data.result.followed_tags[key].tags[tag]+' (english)'
                        +'</li>';
                    }
                }

            }
        }
        output+='<li class="list-group-item list-group-item list-group-item-secondary"><b>Shared tags found in top 1000</b></li>';
        data.result.top_tags.forEach(tag => {
            output+='<li class="list-group-item list-group-item list-group-item-secondary">'+'Tag ID: '+tag.tag_id+
                ' Counter: '+tag.count+' times</li>';
        });
        output+='</ul>';
        $('#tagsBody').html(output);
    }
</script>

</body>
</html>