/**
 * Global Mailbox — adaptations de l'interface native de liste pour une vue multi-boîtes.
 *
 * 1. Assignation groupée : le menu natif est scopé à une boîte (masqué ici) ; on injecte un menu
 *    "admins" (valable sur toutes les boîtes) dans la barre d'actions et on poste les IDs sélectionnés.
 * 2. Tri & pagination : le cœur les fait en AJAX via loadConversations() (qui exige mailbox_id/folder_id,
 *    vides ici) → on bascule en navigation pleine page (?page=N / ?sorting[...]), lue par le contrôleur.
 */
function globalMailboxInit()
{
    $(document).ready(function () {
        var bulk = $('#conversations-bulk-actions');

        // 1) Assignation groupée (admins).
        var tpl = $('#gm-assignee-tpl .gm-assignee:first');
        var clear = bulk.find('.conv-checkbox-clear:first');
        if (tpl.length && clear.length && !bulk.find('.gm-assignee').length) {
            clear.after(tpl);
            bulk.find('.gm-conv-user li > a').click(function (e) {
                e.preventDefault();
                var conv_ids = getSelectedConversations();
                if (!conv_ids.length) {
                    return;
                }
                fsAjax(
                    {
                        action: 'bulk_conversation_change_user',
                        conversation_id: conv_ids,
                        user_id: $(this).attr('data-user_id')
                    },
                    laroute.route('conversations.ajax'),
                    function (response) {
                        if (isAjaxSuccess(response)) {
                            location.reload();
                        } else {
                            showAjaxError(response);
                        }
                    },
                    true
                );
            });
        }

        // 2) Pagination → navigation pleine page (en conservant le tri courant).
        $('.table-conversations .pager-nav').off('click').on('click', function (e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) {
                return;
            }
            var url = new URL(window.location);
            url.searchParams.set('page', $(this).attr('data-page'));
            window.location = url.toString();
        });

        // 3) Tri par colonne → navigation pleine page (toggle asc/desc, retour page 1).
        $('.conv-col-sort').off('click').on('click', function (e) {
            e.preventDefault();
            var sort_by = $(this).attr('data-sort-by');
            var order = ($(this).attr('data-order') === 'asc') ? 'desc' : 'asc';
            var url = new URL(window.location);
            url.searchParams.set('sorting[sort_by]', sort_by);
            url.searchParams.set('sorting[order]', order);
            url.searchParams.delete('page');
            window.location = url.toString();
        });
    });
}
