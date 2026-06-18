<?php

namespace Modules\GlobalMailbox\Http\Controllers;

use App\Conversation;
use App\Http\Controllers\Controller;

class GlobalMailboxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $ids = auth()->user()->mailboxesIdsCanView();
        if (empty($ids)) {
            abort(403);
        }

        $conversations = Conversation::whereIn('mailbox_id', $ids)
            ->whereIn('status', [Conversation::STATUS_ACTIVE, Conversation::STATUS_PENDING])
            ->where('state', Conversation::STATE_PUBLISHED)
            ->with(['customer', 'mailbox'])
            ->orderBy('last_reply_at', 'desc')
            ->paginate(50);

        return view('globalmailbox::index', ['conversations' => $conversations]);
    }
}
