<?php
namespace App\Console\Commands;

use App\Exceptions\NotFoundException;
use App\Models\Stream;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class TopStreamsCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'TopStreamsCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Top 1000 Streams";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();
            $user = User::where('id', 1)->first();
        if(!$user) {
            throw new NotFoundException('User not found!');
        }
        $data = NULL;
        $returnData = [];
        $tagData = [];
        $cursor = NULL;
        $page = 0;

        do {
            $uri = env('TWITCH_URL').'/streams?first=100&'.(!is_null($cursor)?'&after='.$cursor:'');
            $streams = $client->request('GET', $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$user->twitch_token,
                        'Client-Id' =>env('TWITCH_CLIENT')
                    ]
                ]);
            $data = json_decode($streams->getBody()->getContents());
            foreach ($data->data as $d) {
                $returnData[] = [
                    'twitch_id' => $d->id,
                    'user_id' => $d->user_id,
                    'user_login' => $d->user_login,
                    'user_name' => $d->user_name,
                    'game_id' => $d->game_id,
                    'game_name' => $d->game_name,
                    'type' => $d->type,
                    'title' => $d->title,
                    'viewer_count' => $d->viewer_count,
                    'started_at' => $d->started_at,
                    'language' => $d->language,
                    'thumbnail_url' => $d->thumbnail_url,
                    'tag_ids' => json_encode($d->tag_ids),
                    'is_mature' => $d->is_mature,
                ];
                foreach ($d->tag_ids as $tag) {
                    $tagData[] = [
                        'user_id' => $d->user_id,
                        'tag_id' => $tag
                    ];
                }
            }
            $page++;
            $cursor = isset($data->pagination->cursor)?$data->pagination->cursor:NULL;
        } while($page < 10);

        if($returnData) {
            Stream::truncate();
            Tag::truncate();
            Stream::insert($returnData);
            Tag::insert($tagData);
        } else
            $this->alert('Twitch returned no data!');


        $this->info(count($returnData).' entries successfully added!' );
    }


}