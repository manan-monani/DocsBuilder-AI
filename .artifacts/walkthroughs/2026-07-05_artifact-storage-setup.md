# Walkthrough: Project-Local Artifact Storage Setup

## What Was Done

Set up a portable, generalized system to store Antigravity artifacts directly in the project codebase. Works on any project — just copy the folders.

---

## Files Created / Updated

### `.artifacts/` — Artifact Storage (Copy this whole folder to new projects)

| File | Purpose |
|------|---------|
| `rules.md` | Custom rules the agent reads at chat start — edit per project |
| `index.md` | Auto-updated log of all artifacts across all chats |
| `plans/` | Implementation plans & task checklists |
| `walkthroughs/` | Post-work summaries |
| `notes/` | Research & analysis |
| `scratch/` | Temp files (gitignored) |

### `.agents/hooks/` — Stop Hook Automation (Copy to new projects)

| File | Purpose |
|------|---------|
| `hooks.json` | Antigravity Stop Hook config — fires on agent stop |
| `hooks/copy_artifacts.ps1` | PowerShell script — copies brain artifacts to `.artifacts/` |
| `hooks/README.md` | Portable setup guide for new projects |

### Rule Files — Behavioral Layer (Paste the section into new projects)

| File | Change |
|------|--------|
| `AGENTS.md` | Added "Project Artifacts Management" section (lines 258-279) |
| `GEMINI.md` | Same section added |
| `CLAUDE.md` | Same section added |
| `~/.gemini/GEMINI.md` | Global rules — applies to ALL projects automatically |

### `.gitignore`

Added `/.artifacts/scratch/*` and `!/.artifacts/scratch/.gitkeep`

---

## How to Apply to a New Project

1. Copy `.artifacts/` folder (reset `index.md` to empty table)
2. Copy `.agents/hooks.json` + `.agents/hooks/` folder
3. Paste the artifact rules block into your AGENTS.md/GEMINI.md/CLAUDE.md
4. Add scratch exclusion to `.gitignore`
5. Done — or just use the global `~/.gemini/GEMINI.md` which applies everywhere automatically
