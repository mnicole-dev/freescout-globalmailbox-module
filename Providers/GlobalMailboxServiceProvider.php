<?php

namespace Modules\GlobalMailbox\Providers;

use Illuminate\Support\ServiceProvider;

class GlobalMailboxServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'globalmailbox');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'globalmailbox');
        $this->hooks();
    }

    public function register()
    {
    }

    public function hooks()
    {
        \Eventy::addFilter('stylesheets', function ($styles) {
            $styles[] = asset('modules/globalmailbox/css/globalmailbox.css');
            return $styles;
        });

        // Lien "Global Mailbox" dans le menu principal, si l'utilisateur peut voir au moins une boîte.
        \Eventy::addAction('menu.append', function () {
            if (!auth()->check()) {
                return;
            }
            if (empty(auth()->user()->mailboxesIdsCanView())) {
                return;
            }
            $active = (\Route::currentRouteName() === 'globalmailbox.index') ? 'active' : '';
            echo '<li class="' . $active . '"><a href="' . route('globalmailbox.index') . '">'
                . '<i class="glyphicon glyphicon-globe"></i> ' . e(__('Global Mailbox')) . '</a></li>';
        });
    }
}
