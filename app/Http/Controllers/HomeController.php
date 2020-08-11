<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Utils\VideoUtil;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public $channel ;
    public function __construct()
    {
        $this->channel = 'UC1OysNaZ0JDRv-bC4RcV6wQ';
    }

    public function index()
    {
        //$order = ['The Situation Room','Spice Drive', 'Adults In The Room','SpiceFM Live Sessions','SpiceFM African Proverbs'];


        $playlists = VideoUtil::getLocalPlaylists($this->channel,100);
        //dump($playlists);
        //return ;

        $VideoUtil = new VideoUtil();

        return view('home',compact('playlists','VideoUtil'));
    }

    public function listen()
    {
        return view('listen');
    }

    public function watch($videoid,$slug = null)
    {
        return view('watch',compact('videoid'));
    }

    public function show($playlistid,$slug = null)
    {
        $show = Playlist::where('id',$playlistid)->first();
        $util = new VideoUtil();
        $show_items = $util->getLocalPlaylistVideos($playlistid,100);

        return view('show',compact('show','show_items'));
    }

    public function search(Request $request)
    {
        $query = $request->search;
        $search_items = VideoUtil::search($query,100);

        return view('search',compact('query','search_items'));
    }

    public function store()
    {
        return view('store');
    }

    public function updateDB(Request $request,$secret)
    {
        if($secret == "verasidika")
        {
            $util = new VideoUtil();
            $util->pullYoutubePlaylists();
            $util->pullYoutubePlaylistsVideos();

            return response()->json(['message'=>'successful'],200);
        }

    }

}
