<?php

namespace App\Http\Controllers;
use App\Domain\Services\Twitch;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var TwitchgetStreamsPerGame
     */
    private $twitch;

    public function __construct(Twitch $twitch)
    {
        $this->twitch = $twitch;
    }

    public function dashboard() {
        return view('users/dashboard', []);
    }

    public function login(Request $request) {
        return view('users/login', []);
    }

    public function userIU(Request $request, Twitch $twitch) {
        $result = [];
        $result[] = $twitch->userIU($request->input('access_token'));
//        $result[] = $twitch->getStreamsPerGame($request->input('access_token'));
        return response()->json($result);
    }
    public function gameStreams(Request $request) {
        $result = $this->twitch->getStreamsPerGame($request->bearerToken());
        return response()->json($result);
    }
    public function getTopGames(Request $request) {
        $result = $this->twitch->getTopGames($request->bearerToken());
        return response()->json($result);
    }
    public function gameTopStreams(Request $request, $order = 'asc') {
        $result = $this->twitch->getTopStreams($request->bearerToken(), ($order == 'asc')?$order:'desc');
        return response()->json($result);
    }
    public function getDateStreams(Request $request, $pages = 1) {
        $result = $this->twitch->getDateStreams($request->bearerToken(), $pages);
        return response()->json($result);
    }
    public function getTopFollowedStreams(Request $request) {
        $result = $this->twitch->getTopFollowedStreams($request->bearerToken());
        return response()->json($result);
    }
    public function getLowestFollowedStream(Request $request) {
        $result = $this->twitch->getLowestFollowedStream($request->bearerToken());
        return response()->json(['result' => $result]);
    }
    public function getTopFollowedTags(Request $request) {
        $result = $this->twitch->getTopFollowedTags($request->bearerToken());
        return response()->json(['result' => $result]);
    }

}