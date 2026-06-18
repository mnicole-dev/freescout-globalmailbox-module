@extends('layouts.app')

@section('title', __('Global Mailbox'))

{{-- Pas de boîte/dossier unique : on neutralise les attributs scopés (le realtime natif est gardé par "if (mailbox_id)"). --}}
@section('body_attrs')@parent data-mailbox_id="" data-folder_id=""@endsection

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    <div class="dropdown sidebar-title sidebar-title-extra">
        <span class="sidebar-title-real mailbox-name">{{ __('Global Mailbox') }}</span>
    </div>
    <ul class="sidebar-menu" id="folders">
        @php
            $gm_filters = [
                'all'        => ['label' => __('All'),                'icon' => 'folder-open'],
                'unassigned' => ['label' => __('Unassigned'),         'icon' => 'inbox'],
                'mine'       => ['label' => __('Mine'),               'icon' => 'user'],
                'closed'     => ['label' => __('Closed'),             'icon' => 'ok'],
            ];
        @endphp
        @foreach ($gm_filters as $key => $f)
            @php $count = $counts[$key] ?? 0; @endphp
            <li class="{{ $f['icon'] }}@if ($filter === $key) active @endif">
                <a href="{{ route('globalmailbox.index', ['filter' => $key]) }}" @if (!$count) class="no-active" @endif>
                    <i class="glyphicon glyphicon-{{ $f['icon'] }}"></i> <span class="folder-name">{{ $f['label'] }}</span>
                    @if ($count)
                        @if ($key === 'unassigned' || $key === 'mine')
                            <strong class="active-count pull-right">{{ $count }}</strong>
                        @else
                            <span class="active-count pull-right">{{ $count }}</span>
                        @endif
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
@endsection

@section('content')
    <div class="section-heading">{{ __('Global Mailbox') }}</div>

    <div class="alerts">
        @include('partials/flash_messages')
    </div>

    {{-- Interface native exacte : cases à cocher, colonnes, barre d'actions groupées, pagination.
         $mailbox volontairement absent → le menu d'assignation natif (scopé boîte) est masqué ;
         on le remplace par une assignation "admins" cross-boîtes (cf. template ci-dessous + JS). --}}
    @include('conversations/conversations_table')

    {{-- Menu d'assignation groupée (admins), déplacé dans la barre d'actions par le JS. --}}
    <div id="gm-assignee-tpl" class="hide">
        <div class="btn-group gm-assignee">
            <button type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('Assignee') }}">
                <span class="glyphicon glyphicon-user"></span><span class="caret"></span>
            </button>
            <ul class="dropdown-menu gm-conv-user dm-scrollable">
                <li><a href="#" data-user_id="-1">{{ __('Anyone') }}</a></li>
                <li><a href="#" data-user_id="{{ Auth::user()->id }}">{{ __('Me') }}</a></li>
                @foreach ($assignees as $assignee_user)
                    @if ($assignee_user->id != Auth::user()->id)
                        <li><a href="#" data-user_id="{{ $assignee_user->id }}">{{ $assignee_user->getFullName() }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@endsection

@section('javascript')
    @parent
    viewMailboxInit();
    globalMailboxInit();
@endsection
