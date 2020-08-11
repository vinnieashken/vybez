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


}
