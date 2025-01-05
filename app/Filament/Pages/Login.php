<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.login';

    public function mount(): void {
        parent::mount();

        $this->form->fill([
            'email' => 'admin@kaido.com',
            'password' => 'password',
            'remember' => true,
        ]);
    }
}
