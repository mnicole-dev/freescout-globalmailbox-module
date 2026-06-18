/**
 * Global Mailbox — JS du module.
 *
 * IMPORTANT : ce fichier est CONCATÉNÉ dans le bundle minifié du cœur. Il DOIT :
 *  - commencer par « ; » (si le fichier précédent n'a pas de point-virgule final, évite que
 *    « })(jQuery) » + « $(... » fusionnent en un appel cassé → TypeError qui tue tout le bundle) ;
 *  - être encapsulé dans une IIFE (aucune fuite, aucune exécution top-level fragile) ;
 *  - exposer globalMailboxInit en global (appelée depuis la vue /global).
 */
;(function () {
    'use strict';

    if (typeof window.jQuery === 'undefined') {
        return;
    }
    var $ = window.jQuery;

    // Déplace le lien « Global Mailbox » du menu de gauche vers la droite, juste après le menu compte (l'@).
    // S'exécute sur toutes les pages (fichier chargé globalement) pour que le lien soit toujours à droite.
    $(function () {
        var item = $('#menu-global-mailbox');
        var account = $('.navbar-right .dropdown-toggle-account').closest('li');
        if (item.length && account.length && !item.data('gm-moved')) {
            item.data('gm-moved', true).insertAfter(account);
        }
    });

    /**
     * Adaptations de l'interface native de liste pour la vue multi-boîtes. Appelée depuis la vue /global.
     *  1. Assignation groupée (admins) : menu natif scopé boîte masqué → on injecte + on poste les IDs.
     *  2. Tri & pagination : le JS natif (loadConversations) exige mailbox_id/folder_id (vides ici) →
     *     on bascule en navigation pleine page (?page=N / ?sorting[...]), lue par le contrôleur.
     */
    window.globalMailboxInit = function () {
        $(function () {
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

            // 2) Pagination → navigation pleine page (en conservant le filtre + le tri courants).
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
    };
})();
