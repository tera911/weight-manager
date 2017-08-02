<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;

class OauthLoginController extends Controller
{

    /** @var $provider Google  */
    protected $provider;


    /**
     * OauthLoginController constructor.
     */
    public function __construct()
    {
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri = env('APP_URL')."/api/cb";

        $this->provider = new \OAuthClient2Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri
        ]);
    }


    public function login()
    {
        if(\Auth::check()){
            return redirect('/api/user');
        }
        $authUrl = $this->provider->getAuthorizationUrl();
        return redirect($authUrl);
    }

    public function callback(Request $req)
    {

        if (!$req->has('state') || !$req->has('code')) {
            throw new \Exception('invalid state');
        }
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $req->get('code')
        ]);

        /** @var GoogleUser $user */
        $user = $this->provider->getResourceOwner($token);

        $userModel = User::whereGoogleAuthId($user->getId())->first();
        if(!$userModel){
            $userModel = new User();
            $userModel->name = $user->getName();
            $userModel->google_auth_id = $user->getId();
            $userModel->save();
        }

        \Auth::login($userModel, true);
        return redirect('http://w.tera.jp');
    }
}
