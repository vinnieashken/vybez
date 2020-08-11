<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;

class RevenueController extends Controller
{
    //

    private $api;
    public function __construct()
    {
        $this->api = 'https://vas.standardmedia.co.ke/api/';
    }

    public function login(Request $request)
    {
        $username = $request->email;
        $password = $request->password;
        $url = $request->url;

        $user =  new User();

        $params = ["body"=>json_encode(['username'=> $username, 'password'=>$password,"app_id"=> 9,"app_secret"=>"pFvwrdA3ycw6VKq3"])];
        $response = null;
        $client = new Client(['headers' => [ 'Content-Type' => 'application/json' ],'verify'=> base_path('/cacert.pem'),'http_errors'=>true]);

        try {

            $response = $client->request('POST', $this->api . 'auth', $params);

        }catch (Exception $e)
        {

        }

        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        $objbody = json_decode($body);

        //dump($objbody);

        //return ;

        if(property_exists($objbody ,'message'))
        {
            $request->session()->flash('loginerror', $objbody->message);
            $request->session()->flash('artloginerror', $objbody->message);
            return redirect($url);
        }

        $user->id = $objbody->id;
        $user->email = $objbody->email;
        $user->name = $objbody->name;

        $existing = $user->find($objbody->id);

        if(is_null($existing))
            $user->save();

        Auth::setUser($user);
        Auth::login($user);

        $request->session()->flash('loginnotify', true);
        return redirect($url);
    }

    public function register(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;
        $phone = $request->phone;
        $url = $request->url;

        $user =  new User();

        $params = ["body"=>json_encode(['name'=> $name,'email'=> $email ,'password'=>$password,'password_confirmation'=>$password_confirmation,"app_id"=> 9,"app_secret"=>"pFvwrdA3ycw6VKq3"])];
        $response = null;
        $client = new Client(['headers' => [ 'Content-Type' => 'application/json' ],'verify'=> base_path('/cacert.pem'),'http_errors'=>false]);
        try {

            $response = $client->request('POST', $this->api . 'register', $params);

        }catch (Exception $e)
        {

        }

        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        $objbody = json_decode($body);


        if($response->getStatusCode() >= 500)
        {
            //$request->session()->flash('registrationerror', '');

            return redirect( \url('/'));
        }

        if(property_exists($objbody ,'message') && ( (int) $response->getStatusCode()) > 250)
        {
            $request->session()->flash('registrationerror', $objbody->message.' or reset your password if you have forgotten it.');
            $request->session()->flash('artregistrationerror', $objbody->message.' or reset your password if you have forgotten it.');

            return redirect($url);
        }

        $user->id = $objbody->id;
        $user->email = $objbody->email;
        $user->name = $objbody->name;
        $user->phone = $phone;



        $existing = $user->find($objbody->id);

        if(is_null($existing))
        {
            $user->save();
//            Auth::setUser($user);
//            Auth::login($user);
            $this->login($request);
        }


//        dump($objbody);
//        dump(( (int) $response->getStatusCode()));
//        dump($user);
//        return ;
        //$request->session()->flash('loginerror', 'success! Login to continue');

        return redirect($url);
    }

    public function resetPassword(Request $request)
    {
        $email = $request->email;
        $redirect_url = $request->url;

        $url = \url('/');

        $params = ["body"=>json_encode(['email'=> $email, 'redirect_url'=> $url ,"app_id"=> 9,"app_secret"=>"pFvwrdA3ycw6VKq3" ])];

        //return $params;
        $response = null;
        $client = new Client(['headers' => [ 'Content-Type' => 'application/json' ],'verify'=> base_path('/cacert.pem'),'http_errors'=>false]);
        try {

            $response = $client->request('POST', $this->api . 'email/password', $params);

        }catch (Exception $e)
        {

        }

        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        $objbody = json_decode($body);

        if(property_exists($objbody ,'message'))
        {
            $request->session()->flash('resetmsg', $objbody->message);
            $request->session()->flash('artresetmsg', $objbody->message);

            return redirect($redirect_url);
        }

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();

        return redirect(URL::previous());
    }

    public function subscribe(Request $request)
    {
        $email = $request->email;

        if(empty($email))
        {
            $request->session()->flash('subscriberror', 'Please provide a valid email address');
            return redirect(URL::previous());
        }

        $params = ["body"=>json_encode(['email'=> $email, 'category_id'=> 38 ])];
        //return $params;
        $response = null;
        $client = new Client(['headers' => [ 'Content-Type' => 'application/json',
            "appkey"=> "VHV1VElESjRMNklTYnNubGJlZVY0WkRieVNiZU9vendsSm5mRU56NDdYR2w2WXBESEF2UnVQWU9MdXhm5eaa84f9ba4bc"
        ],
            'verify'=> base_path('/cacert.pem'),'http_errors'=>false]);
        try {

            $response = $client->request('POST', 'https://mail.standarddigitalworld.co.ke/api/subscription', $params);

        }catch (Exception $e)
        {

        }

        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        $objbody = json_decode($body);

        $message = $objbody->message;

        $response = "Thank you for subscribing to our newsletter. A subscription email has been sent to your account";

        $request->session()->flash('subscribemsg', $response);

        return redirect(URL::previous());
    }

    public function checkAccount(Request $request)
    {
        $email = $request->email;

        $params = ["body"=>json_encode(['email'=> $email])];
        //$params = json_encode(['email'=> $email ]);

        //return $params;

        $client = new Client(['headers' => [ 'Content-Type' => 'application/json' ],'verify'=> base_path('/cacert.pem'),'http_errors'=>false]);
        try {

            $response = $client->request('POST', $this->api . 'check/email', $params);

        }catch (Exception $e)
        {

        }

        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        $objbody = json_decode($body);

        if((int)$response->getStatusCode() >= 400 )
        {
            //$request->session()->flash('registrationerror', 'Your email is not registered.Fill in the form to create an account');
            $request->session()->flash('artregistrationerror', 'Your email is not registered.Fill in the form to create an account');
            $request->session()->flash('email', $email);
            return redirect(URL::previous());
        }

        $request->session()->flash('loginprompt', 'Enter your password to continue');
        $request->session()->flash('email', $email);
        return redirect(URL::previous());

    }

    public function getUser()
    {
        //return Auth::user() ?? 'not logged in';
    }

    public function getMostRead()
    {
        $articles = new Articles();

        dump($articles->getMostRead());


        //return $articles->getLocalArticles($articles->getMostRead());
    }
}
