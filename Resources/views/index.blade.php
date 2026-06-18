@extends('layouts.app')

@section('title_full', __('Global Mailbox'))

@section('content')
<div class="section-heading">{{ __('Global Mailbox') }}</div>
<div class="row-container"><div class="row"><div class="col-xs-12">
    @if ($conversations->total())
        <table class="table table-striped gm-table margin-top">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Mailbox') }}</th>
                    <th>{{ __('Last Reply') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($conversations as $conversation)
                    <tr>
                        <td>{{ $conversation->number }}</td>
                        <td><a href="{{ route('conversations.view', $conversation->id) }}">{{ $conversation->subject }}</a></td>
                        <td>{{ optional($conversation->customer)->getFullName() ?: $conversation->customer_email }}</td>
                        <td>{{ optional($conversation->mailbox)->name }}</td>
                        <td>{{ $conversation->last_reply_at ? \App\User::dateFormat($conversation->last_reply_at) : '' }}</td>
                        <td>{{ \App\Conversation::statusCodeToName($conversation->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $conversations->links() }}
    @else
        <p class="text-help margin-top">{{ __('No conversations.') }}</p>
    @endif
</div></div></div>
@endsection
