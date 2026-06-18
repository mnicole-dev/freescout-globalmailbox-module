# FreeScout Global Mailbox Module

A **Global Mailbox** page that aggregates active/pending conversations from every mailbox you can access,
using FreeScout's **native conversations interface** — the same list you get inside a mailbox, with
checkboxes, columns, sorting, pagination and bulk actions. Free adaptation of the paid global-mailbox module.

## Features
- The **native conversations list** across all your mailboxes (with the source mailbox shown), not a stripped-down table.
- **Manage conversations in bulk**: change status, delete, and assign — directly from the global view.
  Assignment is offered for **admin** users (they have access to every mailbox), matching the paid module's behavior.
- Column sorting and pagination (full-page navigation, since the view spans several mailboxes).
- Respects per-user mailbox permissions (`mailboxesIdsCanView`) — no cross-mailbox leak.
- "Global Mailbox" link in the main menu (shown only if you can access ≥1 mailbox).
- Click a conversation to open it in its own mailbox.

## How it works
Hooks-only, no database, no core changes. A dedicated `/global` route + a controller scoping the query to
your accessible mailboxes, rendering FreeScout's own `conversations_table` partial. Bulk actions reuse the
core id-based endpoints (`bulk_conversation_change_status` / `_change_user` / `bulk_delete_conversation`),
which authorize each conversation individually — so cross-mailbox management is safe.

## Requirements
FreeScout ≥ 1.8.0. No composer dependencies.

## Installation
```bash
cd Modules
git clone https://github.com/mnicole-dev/freescout-globalmailbox-module GlobalMailbox
```
Activate **GlobalMailbox** in **Manage → Modules**.

> If installing via CLI in a container, run `artisan` as the web user, not root.

## License
AGPL-3.0 — see [LICENSE](LICENSE).
