<?php


namespace App\Utils;


use App\Models\Playlist;
use App\Models\Video;

class VideoUtil
{

    public static function getLocalPlaylists($channel_id,$limit,$start=0)
    {
        $playlists = Playlist::orderBy('list_order','DESC')->where('channel_id',$channel_id)->offset($start)->limit($limit)->get();
        return $playlists;
    }
    public function getLocalPlaylist($playlist_id)
    {
        $filter = [
            'id' => $playlist_id,
        ];
        //$this->db->order_by('list_order','DESC');
        //return $this->db->get_where('radio_playlists',$filter)->row();
    }


    public function getLocalPlaylistVideos($playlist_id,$limit,$start=0)
    {
        $videos = Video::where('playlist_id',$playlist_id)->offset($start)->limit($limit)->get();
        return $videos;
    }

    public static function search($criteria,$limit,$start = 0)
    {
        $videos = Video::where('title','like','%'.$criteria.'%')->offset($start)->limit($limit)->get();
        return $videos;
    }


    public function pullYoutubePlaylists()
    {
        $order = ['Morning Vybez' => 3,'Vybez Adrenaline'=> 2, 'Cease N Sekkle'=> 1];

        $api = new YoutubeAPI();
        foreach ($api->getChannelPlaylists($api->channel) as $playlist)
        {
            $item = new Playlist();
            $exists = $item->where('id',$playlist->getId())->first();

            if(!is_null($exists))
                continue;

            $item->id = $playlist->getId();
            $item->title = $playlist->getSnippet()->getTitle();
            if(array_key_exists($item->title,$order))
            {
                $item->list_order = $order[$item->title];
            }
            $item->description = $playlist->getSnippet()->getDescription();
            $item->channel_id = $api->channel;
            $item->date_added = date('Y-m-d H:i:s',strtotime($playlist->getSnippet()->getPublishedAt()));

            $item->save();
        }
    }

    public function pullYoutubePlaylistsVideos()
    {

        $api = new YoutubeAPI();
        foreach ($this->getLocalPlaylists($api->channel,100) as $playlist)
        {
            foreach ($api->getPlaylistVideos($playlist->id ) as $item)
            {
                $video = new Video();
                $exists = $video->where('id',$item->getContentDetails()->getVideoId())->first();

                if(!is_null($exists))
                    continue;
                $video->playlist_id = $playlist->id;
                $video->id = $item->getContentDetails()->getVideoId();
                $video->title = $item->getSnippet()->getTitle();
                $video->url = ($item->getSnippet()->getThumbNails() != NULL ? $item->getSnippet()->getThumbNails()->getHigh()->getUrl() : NULL);
                $video->date_added = date('Y-m-d H:i:s',strtotime($item->getContentDetails()->getVideoPublishedAt()));

                $video->save();
            }
        }

    }


}
