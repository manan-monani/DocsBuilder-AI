# Antigravity Artifact Hooks — Portable Setup Guide

## What This Does

Automatically copies AI agent artifacts (plans, walkthroughs, notes) from Antigravity's internal storage to your project's `.artifacts/` directory, so everything stays in your codebase.

## Quick Setup for a New Project

Copy these **two things** to your new project:

### 1. Copy the hooks folder
```
.agents/
├── hooks.json              ← Stop hook config
└── hooks/
    └── copy_artifacts.ps1  ← Auto-copy script (Windows)
```

### 2. Copy the artifacts folder
```
.artifacts/
├── rules.md       ← Custom rules (edit per project)
├── index.md       ← Auto-updated artifact log
├── plans/         ← Implementation plans
├── walkthroughs/  ← Work summaries
├── notes/         ← Research & analysis
└── scratch/       ← Temp files (gitignored)
```

### 3. Add to your AGENTS.md / GEMINI.md / CLAUDE.md

Paste this block at the end of your project's rule file:

```markdown
## Project Artifacts Management

### On Chat Start (Every Conversation — New or Resumed)
- Check if `.artifacts/` exists in the project root.
- If it exists, scan `.artifacts/index.md` for a summary of prior work across all previous chats.
- If `.artifacts/rules.md` exists, read and follow those custom rules. They override defaults.
- Scan `.artifacts/plans/`, `.artifacts/walkthroughs/`, and `.artifacts/notes/` for existing context relevant to the current task.

### On Chat End (When Completing Work)
- Save key artifacts to `.artifacts/` in the project root:
  - Implementation plans → `.artifacts/plans/`
  - Walkthroughs → `.artifacts/walkthroughs/`
  - Research notes and analysis → `.artifacts/notes/`
  - Temporary/scratch files → `.artifacts/scratch/`
- Use date-prefixed filenames: `YYYY-MM-DD_<short-description>.md`
- Update `.artifacts/index.md` with a summary entry for each new artifact.

### Artifact Rules
- NEVER delete existing artifacts in `.artifacts/` without explicit user approval.
- When resuming work from a previous chat, reference the relevant artifacts in `.artifacts/` to maintain continuity.
- If `.artifacts/rules.md` defines custom rules, follow those rules. Otherwise, follow the defaults above.
```

### 4. Add to .gitignore

```
/.artifacts/scratch/*
!/.artifacts/scratch/.gitkeep
```

## That's It

No `chmod`, no bash, no Linux dependencies. Works natively on Windows with PowerShell.

## How It Works

```
Chat Starts ──→ Agent reads .artifacts/index.md + rules.md
     │               for context from prior chats
     ↓
Agent Works ──→ Creates plans, walkthroughs, notes
     │
     ↓
Chat Ends ────→ Layer 1: Agent saves artifacts (behavioral rule)
              → Layer 2: Stop Hook fires copy_artifacts.ps1 (automated safety net)
              → Both write to .artifacts/ in the project root
```

## Customization

Edit `.artifacts/rules.md` to change artifact behavior per project.
