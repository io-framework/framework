---
title: "Io\\View"
description: "The classes, interfaces, traits and enums in Io\\View."
order: 200
generated: true
---

## Types

- [`Check`](Check.md) · final class: `vendor/bin/io-check`: the views of a project, and the templates of packages that add theirs (Checks), checked without running the app.
- [`Checks`](Checks.md) · interface: What a package adds to `vendor/bin/io-check`: its own templates, checked without running the app.
- [`Partials`](Partials.md) · final readonly class: Views rendered as markup without a layout, set up as a page's view is (helpers, CSRF, the request): what `@include` does in a view, for a plugin that renders templates of its own (Live's components).
