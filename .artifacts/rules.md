# Project Artifacts — Custom Rules

> This file is read by the AI agent at the **start of every conversation**.
> Rules defined here **override** the default artifact behavior from AGENTS.md.
> Delete the examples below and add your own, or leave blank to use defaults.

---

## Naming

- Use date-prefixed filenames: `YYYY-MM-DD_<short-kebab-description>.md`
- Example: `2026-07-05_user-auth-implementation.md`

## Storage Locations

| Artifact Type | Save To |
|---------------|---------|
| Implementation plans | `.artifacts/plans/` |
| Task checklists | `.artifacts/plans/` |
| Walkthroughs / summaries | `.artifacts/walkthroughs/` |
| Research notes / analysis | `.artifacts/notes/` |
| Temporary / scratch files | `.artifacts/scratch/` |

## Behavior

- NEVER delete or overwrite existing artifacts without explicit user approval.
- When resuming work from a prior chat, reference existing artifacts for continuity.
- Always update `.artifacts/index.md` after saving any new artifact.
- Keep scratch files minimal; clean them up when they are no longer relevant.

## Custom Rules

<!-- ──────────────────────────────────────────────────────────── -->
<!-- Add your project-specific artifact rules below this line.  -->
<!-- These override the defaults above when they conflict.      -->
<!-- ──────────────────────────────────────────────────────────── -->

<!-- Examples: -->
<!-- - Always include code diffs in walkthrough artifacts -->
<!-- - Save API design docs to .artifacts/notes/ with prefix "api-" -->
<!-- - Never auto-save scratch files — only save plans and walkthroughs -->
