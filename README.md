# FreeScout Global Mailbox Module

A **Global Mailbox** page that aggregates active/pending conversations from every mailbox you can access,
in one list — sorted by last activity, paginated. Click a conversation to open it in its own mailbox.
Free adaptation of the paid global-mailbox module.

## Features
- One page listing conversations across all your mailboxes (with the source mailbox shown).
- Respects per-user mailbox permissions (`mailboxesIdsCanView`) — no cross-mailbox leak.
- "Global Mailbox" link in the main menu (shown only if you can access ≥1 mailbox).
- Read-only: reply from the conversation as usual.

## How it works
Hooks-only, no database, no core changes. A dedicated `/global` route + a controller scoping the query to
your accessible mailboxes, rendered with FreeScout's layout.

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
