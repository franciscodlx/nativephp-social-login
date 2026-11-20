<?php

namespace App\Native;

use Franciscodlxdev\SocialLogin\AuthException;
use Franciscodlxdev\SocialLogin\Events\AuthSessionCompleted;
use Franciscodlxdev\SocialLogin\Events\AuthSessionFailed;
use Franciscodlxdev\SocialLogin\Facades\SocialLogin;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;
use Throwable;

class SocialDemo extends NativeComponent
{
    /** idle | working | done | failed */
    public string $status = 'idle';

    public string $message = '';

    /** @var array<string, string|null> */
    public array $result = [];

    public function signIn(string $id): void
    {
        $this->status = 'working';
        $this->message = '';
        try {
            DemoProviders::signIn($id);
        } catch (Throwable $e) {
            $this->failWith($e);
        }
    }

    #[On(AuthSessionCompleted::class)]
    public function completed(string $url): void
    {
        try {
            $this->result = DemoProviders::finish(SocialLogin::complete($url));
            $this->status = 'done';
        } catch (Throwable $e) {
            $this->failWith($e);
        }
    }

    #[On(AuthSessionFailed::class)]
    public function failed(string $code, string $message = ''): void
    {
        $this->failWith(SocialLogin::fail($code, $message));
    }

    public function render(): View
    {
        return view('native.social-demo', [
            'providers' => collect(DemoProviders::ALL)->map(fn ($p, $id) => [
                'id' => $id,
                'label' => $p[0].(DemoProviders::isReal($id) ? '' : ' (mock)'),
            ])->values(),
        ]);
    }

    private function failWith(Throwable $e): void
    {
        $this->status = 'failed';
        $this->message = $e instanceof AuthException ? "ERROR {$e->authCode}: {$e->getMessage()}" : $e->getMessage();
    }
}
