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

        \Eventy::addFilter('javascripts', function ($javascripts) {
            $javascripts[] = asset('modules/globalmailbox/js/globalmailbox.js');
            return $javascripts;
        });

        // Colonne « Boîte » ajoutée AVANT la colonne Numéro, UNIQUEMENT sur la page globale
        // (les hooks ci-dessous se déclenchent sur toutes les listes de conversations du cœur).
        $is_global = function () {
            $route = \Request::route();
            return $route && $route->getName() === 'globalmailbox.index';
        };

        \Eventy::addAction('conversations_table.col_before_conv_number', function () use ($is_global) {
            if ($is_global()) {
                echo '<col class="conv-mailbox">';
            }
        });
        \Eventy::addAction('conversations_table.th_before_conv_number', function () use ($is_global) {
            if ($is_global()) {
                echo '<th class="conv-mailbox">' . e(__('Mailbox')) . '</th>';
            }
        });
        \Eventy::addAction('conversations_table.td_before_conv_number', function ($conversation) use ($is_global) {
            if ($is_global()) {
                $name = optional($conversation->mailbox_cached)->name ?? '';
                echo '<td class="conv-mailbox" title="' . e($name) . '">' . e($name) . '</td>';
            }
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
            // id pour que le JS puisse le déplacer dans la barre de droite (pas de hook PHP côté navbar-right).
            echo '<li id="menu-global-mailbox" class="' . $active . '"><a href="' . route('globalmailbox.index') . '">'
                . '<i class="glyphicon glyphicon-globe"></i> ' . e(__('Global Mailbox')) . '</a></li>';
        });
    }
}
