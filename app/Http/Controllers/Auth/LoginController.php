<?php
namespace App\Http\Controllers\Auth;

use App\Artvenue\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use Exception;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * @author Abhimanyu Sharma <abhimanyusharma003@gmail.com>
 */
class LoginController extends Controller
{
    use ThrottlesLogins;

    /**
     * @var array
     */
    private $providers = [
        'facebook' => 'fbid',
        'google'   => 'gid',
        'twitter'  => 'twid'
    ];

    /**
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $request->session()->put('force_captcha', 1);

            return $this->sendLockoutResponse($request);
        }
        $loginData = $request->only(['username', 'password']);
        $loginKey = filter_var($loginData['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $loginData[$loginKey] = array_pull($loginData, 'username');

        if (auth()->attempt($loginData, (bool)$request->get('remember-me'))) {
            $request->session()->forget('force_captcha');

            if (auth()->user()->confirmed_at == null) {
                auth()->logout();

                return redirect()->route('login')->with('flashError', t('Email activation is required'));
            }
            if (auth()->user()->permission == 'ban') {
                auth()->logout();

                return redirect()->route('login')->with('flashError', t('You are not allowed'));
            }
            auth()->user()->ip_address = $request->getClientIp();
            auth()->user()->save();

            return redirect()->route('gallery')->with('flashSuccess', t('You are now logged in'));
        }
        $this->incrementLoginAttempts($request);

        return redirect()->route('login')->with('flashError', t('Your username/password combination was incorrect'));
    }


    /**
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        $title = t('Login');

        return view('auth.login', compact('title'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogout()
    {
        auth()->logout();

        return redirect()->route('home');
    }


    /**
     * @return mixed
     */
    public function getFacebook()
    {
        return Socialite::with('facebook')->redirect();
    }

    /**
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getSocial($provider = 'facebook')
    {
        try {
            if ($provider == 'google') {
                return Socialite::driver($provider)->scopes(['email'])->redirect();
            }

            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            return redirect('auth/' . $provider);
        }
    }

    /**
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getSocialCallback($provider = 'facebook', Request $request)
    {
        try {
            $social = Socialite::with($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('flashError', t('Please try again'));
        }

        if ($this->checkIfValidUser($social, $provider)) {
            return redirect('gallery')->with('flashSuccess', t('You are now logged in'));
        }
        $request->session()->put($provider . '_register', $social);

        return redirect()->to('registration/' . $provider);
    }

    /**
     * @param $social
     * @param $provider
     * @return bool
     */
    private function checkIfValidUser($social, $provider)
    {
        if ($user = User::where($this->providers[$provider], '=', $social->getId())->first()) {
            auth()->login($user, true);

            return true;
        }

        if ($user = User::whereEmail($social->getEmail())->first()) {
            auth()->login($user, true);
            $user->{$this->providers[$provider]} = $social->getId();
            $user->save();

            return true;
        }

        return false;
    }


    /**
     * @override
     * @return string
     */
    private function loginUsername()
    {
        return 'username';
    }

    /**
     * @override
     * @return string
     */
    private function loginPath()
    {
        return route('login');
    }
}
