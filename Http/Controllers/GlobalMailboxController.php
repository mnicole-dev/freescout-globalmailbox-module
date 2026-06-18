<?php

namespace Modules\GlobalMailbox\Http\Controllers;

use App\Conversation;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class GlobalMailboxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $ids = $user->mailboxesIdsCanView();
        if (empty($ids)) {
            abort(403);
        }

        // Tri (miroir du cœur) : subject|number|date, asc|desc, défaut date desc.
        $sorting = ['sort_by' => 'date', 'order' => 'desc'];
        $req_sort = $request->input('sorting', []);
        if (is_array($req_sort)
            && !empty($req_sort['sort_by']) && in_array($req_sort['sort_by'], ['subject', 'number', 'date'], true)
            && !empty($req_sort['order']) && in_array($req_sort['order'], ['asc', 'desc'], true)) {
            $sorting = ['sort_by' => $req_sort['sort_by'], 'order' => $req_sort['order']];
        }
        $order_column = ($sorting['sort_by'] === 'date') ? 'last_reply_at' : $sorting['sort_by'];

        $conversations = Conversation::whereIn('mailbox_id', $ids)
            ->whereIn('status', [Conversation::STATUS_ACTIVE, Conversation::STATUS_PENDING])
            ->where('state', Conversation::STATE_PUBLISHED)
            ->with(['customer', 'mailbox'])
            ->orderBy($order_column, $sorting['order'])
            ->paginate(Conversation::DEFAULT_LIST_SIZE, ['*'], 'page', $request->get('page'));

        // Assignables pour l'assignation groupée cross-boîtes = admins (accès à toutes les boîtes).
        $assignees = User::nonDeleted()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('first_name')
            ->get();

        return view('globalmailbox::index', [
            'conversations' => $conversations,
            'sorting'       => $sorting,
            'params'        => ['target_blank' => false],
            'assignees'     => $assignees,
        ]);
    }
}
