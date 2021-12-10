<?php

namespace App\Domain\Services;
use App\Exceptions\NotFoundException;
use App\Exceptions\SQLException;
use App\Models\Stream;
use App\Models\Tag;
use App\Models\User;
use \GuzzleHttp\Client;
use Illuminate\Database\QueryException;
use App\Domain\Services\Helper;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class Twitch {
    public function userIU($token) {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', env('TWITCH_URL').'/users', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Client-Id' =>env('TWITCH_CLIENT')
            ]
        ]);
        $result = json_decode($response->getBody()->getContents());

        try {
            $user = User::firstOrNew(['twitch_id' => $result->data[0]->id]);
            $user->twitch_id = $result->data[0]->id;
            $user->twitch_token = $token;
            $user->email = $result->data[0]->email;
            $user->login = $result->data[0]->login;
            $user->display_name = $result->data[0]->display_name;
            $user->save();
        } catch (QueryException $e) {

            throw new SQLException($e->getMessage());
        }

        return $result;
    }

    /**
     * @param $token
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStreamsPerGame($token) {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', env('TWITCH_URL').'/games/top?first=10', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Client-Id' =>env('TWITCH_CLIENT')
            ]
        ]);

        $games = json_decode($response->getBody()->getContents());
        $viewerCounts = [];
        foreach ($games->data as $game) {
            $page = 0;
            $streams = NULL;
            $stream_count = 0;
            $cursor = NULL;
            $viewerCount = 0;
            do {
                    $uri = env('TWITCH_URL').'/streams?first=100&game_id='.$game->id.(!is_null($cursor)?'&after='.$cursor:'');
                    $streams = $client->request('GET', $uri,
                    //    isset($cursor)?'&after='.$after:'',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer '.$token,
                            'Client-Id' =>env('TWITCH_CLIENT')
                        ]
                    ]);
                    $data = json_decode($streams->getBody()->getContents());
                    foreach ($data as $streams) {
                        foreach ($streams as $stream) {
                            $viewerCounts[] = isset($stream->viewer_count)?(int)$stream->viewer_count:0;
                            $viewerCount += isset($stream->viewer_count)?(int)$stream->viewer_count:0;
                        }
                    }
                    $stream_count += count($data->data);
                    $page++;
                    $cursor = isset($data->pagination->cursor)?$data->pagination->cursor:NULL;

            } while($page <= 8);
            $game->streamer_count = $stream_count;
            $game->viewer_count = $viewerCount;
//            $game->stream_viewers = $viewerCounts;
        }
        $data = new \stdClass();
        $data->games = $games;
        $data->anals = new \stdClass();

        sort($viewerCounts);
        $index = floor(count($viewerCounts)/2);
        $median =  (count($viewerCounts) % 2 == 0)?$viewerCounts[$index]
            :($viewerCounts[$index-1]+$viewerCounts[$index])/2;
        $data->anals->median = $median;
        $data->anals->vcounts = $viewerCounts;
        return $data;
    }

    public function getTopGames($token) {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', env('TWITCH_URL').'/games/top?first=100', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Client-Id' =>env('TWITCH_CLIENT')
            ]
        ]);
        return json_decode($response->getBody()->getContents());

    }

    public function getTopStreams($token, $order) {
        $client = new \GuzzleHttp\Client();
        $uri = env('TWITCH_URL').'/streams?first=100';
        $streams = $client->request('GET', $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Client-Id' =>env('TWITCH_CLIENT')
                ]
            ]);
        $data = json_decode($streams->getBody()->getContents());
        if($order == 'desc') {
            $data->data = array_reverse($data->data);
        }
        return $data->data;
    }

    public function getDateStreams($token, $pages = 1) {
        $client = new \GuzzleHttp\Client();
        $data = NULL;
        $returnData = [];
        $cursor = NULL;
        $page = 0;

        do {
            $uri = env('TWITCH_URL').'/streams?first=100&'.(!is_null($cursor)?'&after='.$cursor:'');
            $streams = $client->request('GET', $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Client-Id' =>env('TWITCH_CLIENT')
                    ]
                ]);
            $data = json_decode($streams->getBody()->getContents());
            foreach ($data->data as $d) {
                $returnData[] = $d;
            }
            $page++;
            $cursor = isset($data->pagination->cursor)?$data->pagination->cursor:NULL;
        } while($page < $pages);
        usort($returnData, ["App\Domain\Services\Helper","date_cmp"]);

        return $returnData;
    }

    /**
     * @param $token
     * @return \Illuminate\Support\Collection
     * @throws NotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function getTopFollowedStreams($token) {
        $user = User::where('twitch_token', $token)->first();
        if(!$user) {
            throw new NotFoundException('User not found!');
        }
        $client = new \GuzzleHttp\Client();
        $data = NULL;
        $cursor = NULL;

        $uri = env('TWITCH_URL').'/streams/followed?first=100&user_id='.$user->twitch_id;
        $streams = $client->request('GET', $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Client-Id' =>env('TWITCH_CLIENT')
                ]
            ]);

        $data = json_decode($streams->getBody()->getContents());
        if (empty($data->data))
            throw new NotFoundException('No followed streams active!');
        $search = [];
        foreach ($data->data as $d) {
            $search[] = $d->user_id;
        }

        $streams = DB::table('streams')
            ->whereIn('user_id', $search)
            ->get();

        return $streams;
    }

    /**
     * @param $token
     * @return string
     * @throws NotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLowestFollowedStream($token) {
        $user = User::where('twitch_token', $token)->first();
        if(!$user) {
            throw new NotFoundException('User not found!');
        }
        $client = new \GuzzleHttp\Client();
        $data = NULL;
        $cursor = NULL;

        $uri = env('TWITCH_URL').'/streams/followed?first=100&user_id='.$user->twitch_id;
        $streams = $client->request('GET', $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Client-Id' =>env('TWITCH_CLIENT')
                ]
            ]);

        $data = json_decode($streams->getBody()->getContents());
        if (empty($data->data))
            throw new NotFoundException('No followed streams active!');
        $lowestFollowed = NULL;
        foreach ($data->data as $d) {
            if(
                is_null($lowestFollowed) ||
                $d->viewer_count < $lowestFollowed->viewer_count
            )
            $lowestFollowed = $d;
        }
        $lowest1000 = DB::table('streams')->latest('id')->first();
        $diff = $lowest1000->viewer_count-$lowestFollowed->viewer_count;
        if($diff <= 0) {
            $string = "User lowest followed already in top 1000 streams!";
        } else {
            $string = $lowestFollowed->user_name.
                ' needs '.($lowest1000->viewer_count-$lowestFollowed->viewer_count).
                ' viewers to be in the current top 1000 streams!';
        }
        return $string;
    }

    public function getTopFollowedTags($token) {
        $user = User::where('twitch_token', $token)->first();
        if(!$user) {
            throw new NotFoundException('User not found!');
        }
        $client = new \GuzzleHttp\Client();

        $cursor = NULL;
        $followedTags = [];
        $uri = env('TWITCH_URL').'/streams/followed?first=100&user_id='.$user->twitch_id;
        $streams = $client->request('GET', $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Client-Id' =>env('TWITCH_CLIENT')
                ]
            ]);

        $followedStreams = json_decode($streams->getBody()->getContents());
        if (empty($followedStreams->data))
            throw new NotFoundException('No followed streams active!');

        foreach ($followedStreams->data as $d) {
            foreach ($d->tag_ids as $tid) {
                if(!isset($followedTags[$d->user_id]))
                    $followedTags[$d->user_id]['user_login'] = $d->user_name;
                $followedTags[$d->user_id]['tags'][$tid] = '';
            }
        }
        $uniqueFollowedTags = [];
        foreach ($followedTags as $tags) {
            foreach ($tags['tags'] as $tag => $value) {
                if(!in_array( $tag, $uniqueFollowedTags))
                    $uniqueFollowedTags[] = $tag;
            }
        }

        foreach ($uniqueFollowedTags as $tag) {
            $uri = env('TWITCH_URL').'/tags/streams?first=100&tag_id='.$tag;
            $twitch_tag = $client->request('GET', $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Client-Id' =>env('TWITCH_CLIENT')
                    ]
                ]);


            $decoded_twitch_tag = json_decode($twitch_tag->getBody()->getContents());
            if(!empty($decoded_twitch_tag->data)) {
                foreach ($followedTags as $id => $user) {
                    foreach($user['tags'] as $followedTag => $v) {
                        if(isset($followedTags[$id]['tags'][$tag]) && $followedTags[$id]['tags'][$tag] == '') {
                            $followedTags[$id]['tags'][$tag] = $decoded_twitch_tag->data[0]->localization_descriptions->{'en-us'};;
                            break;
                        }
                    }
                }
            }
        }
        $topTagsCount = DB::table('tags')
            ->selectRaw('tag_id, count(*) as count')
            ->whereIn('tag_id', $uniqueFollowedTags)
            ->groupBy('tag_id')
            ->get();

        return ['followed_tags' => $followedTags, 'top_tags' => $topTagsCount];


        return $userTags;
    }

}