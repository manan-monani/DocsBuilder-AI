<#
.SYNOPSIS
    Antigravity Stop Hook — Copies agent artifacts to the project's .artifacts/ folder.

.DESCRIPTION
    Triggered automatically by Antigravity's Stop event (configured in .agents/hooks.json).
    
    What it does:
    1. Reads conversation context from stdin (JSON piped by Antigravity)
    2. Locates the brain artifacts directory for the current conversation
    3. Copies all .md artifacts to .artifacts/ in the project root
    4. Organizes files into plans/, walkthroughs/, notes/ by type
    5. Date-prefixes filenames to prevent collisions across chats
    6. Updates .artifacts/index.md with new entries

    Portable: Works on any project — just copy .agents/hooks.json + .agents/hooks/ folder.
    No hardcoded project paths. Auto-detects everything from environment.

.NOTES
    Platform: Windows (PowerShell 5.1+)
    Trigger:  Antigravity Stop Hook (.agents/hooks.json)
    Exit 0:   Always exits 0 (success) — hook failures should not crash the agent.
#>

# ═══════════════════════════════════════════════════════════════════════════════
#  CONFIGURATION — Edit these if your setup differs
# ═══════════════════════════════════════════════════════════════════════════════

# Project root is wherever the agent was launched (current working directory)
$ProjectRoot = Get-Location

# Artifact output directory inside the project
$ArtifactsDir = Join-Path $ProjectRoot ".artifacts"

# Antigravity brain base — standard location across all Antigravity installs
# Format: ~/.gemini/antigravity-ide/brain/<conversation-id>/
$BrainBase = Join-Path $env:USERPROFILE ".gemini\antigravity-ide\brain"

# Date formats for filenames and logging
$DatePrefix = Get-Date -Format "yyyy-MM-dd"
$LogTimestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

# Subdirectories to create/maintain inside .artifacts/
$ArtifactSubdirs = @("plans", "walkthroughs", "notes", "scratch")

# File-to-folder routing rules (order matters — first match wins, *.md is catch-all)
$FileRouting = @(
    @{ Pattern = "implementation_plan.md"; Dest = "plans";        Type = "Plan" }
    @{ Pattern = "task.md";                Dest = "plans";        Type = "Task" }
    @{ Pattern = "walkthrough.md";         Dest = "walkthroughs"; Type = "Walkthrough" }
    @{ Pattern = "research*.md";           Dest = "notes";        Type = "Research" }
    @{ Pattern = "analysis*.md";           Dest = "notes";        Type = "Analysis" }
    @{ Pattern = "*.md";                   Dest = "notes";        Type = "Note" }
)

# Files/patterns to skip (system files, hidden files, etc.)
$SkipPatterns = @(
    "^\.",           # Hidden files (dot-prefixed)
    "^scratch_",     # Scratch files handled separately
    "^\.system"      # System-generated files
)

# ═══════════════════════════════════════════════════════════════════════════════
#  STEP 1: Ensure .artifacts/ directory structure exists
# ═══════════════════════════════════════════════════════════════════════════════

foreach ($sub in $ArtifactSubdirs) {
    $dirPath = Join-Path $ArtifactsDir $sub
    if (-not (Test-Path $dirPath)) {
        New-Item -ItemType Directory -Path $dirPath -Force | Out-Null
        Write-Host "[copy_artifacts] Created directory: .artifacts/$sub"
    }
}

# ═══════════════════════════════════════════════════════════════════════════════
#  STEP 2: Read conversation context from stdin
# ═══════════════════════════════════════════════════════════════════════════════

$stdinContent = ""
try {
    if ([Console]::In.Peek() -ne -1) {
        $stdinContent = [Console]::In.ReadToEnd()
    }
} catch {
    # stdin not available or empty — not an error, just means no context piped
}

# ═══════════════════════════════════════════════════════════════════════════════
#  STEP 3: Extract conversation ID
# ═══════════════════════════════════════════════════════════════════════════════

$ConversationId = ""

# Strategy 1: Parse from stdin JSON (Antigravity pipes this)
if ($stdinContent) {
    try {
        $jsonData = $stdinContent | ConvertFrom-Json -ErrorAction Stop

        # Try common JSON field names for conversation ID
        $idFields = @("conversation_id", "conversationId", "session_id", "id")
        foreach ($field in $idFields) {
            if ($jsonData.PSObject.Properties[$field]) {
                $ConversationId = $jsonData.$field
                break
            }
        }
    } catch {
        # Not valid JSON — try regex extraction as fallback
        if ($stdinContent -match '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}') {
            $ConversationId = $Matches[0]
        }
    }
}

# Strategy 2: Fallback — find the most recently modified brain directory
if (-not $ConversationId -and (Test-Path $BrainBase)) {
    $latestDir = Get-ChildItem -Path $BrainBase -Directory -ErrorAction SilentlyContinue |
        Sort-Object LastWriteTime -Descending |
        Select-Object -First 1

    if ($latestDir) {
        $ConversationId = $latestDir.Name
        Write-Host "[copy_artifacts] No conversation ID in stdin. Using most recent: $($ConversationId.Substring(0,8))..."
    }
}

# No conversation found — exit cleanly
if (-not $ConversationId) {
    Write-Host "[copy_artifacts] No conversation ID found. Nothing to copy."
    exit 0
}

# ═══════════════════════════════════════════════════════════════════════════════
#  STEP 4: Locate and validate brain directory
# ═══════════════════════════════════════════════════════════════════════════════

$BrainDir = Join-Path $BrainBase $ConversationId
$ShortId = $ConversationId.Substring(0, [Math]::Min(8, $ConversationId.Length))

if (-not (Test-Path $BrainDir)) {
    Write-Host "[copy_artifacts] Brain directory not found for conversation $ShortId. Skipping."
    exit 0
}

# Check if there are any .md files to copy
$mdFiles = Get-ChildItem -Path $BrainDir -Filter "*.md" -File -ErrorAction SilentlyContinue
if (-not $mdFiles -or $mdFiles.Count -eq 0) {
    Write-Host "[copy_artifacts] No .md artifacts found in brain directory for $ShortId."
    exit 0
}

Write-Host "[copy_artifacts] Found $($mdFiles.Count) artifact(s) in conversation $ShortId"

# ═══════════════════════════════════════════════════════════════════════════════
#  STEP 5: Copy artifacts using routing rules
# ═══════════════════════════════════════════════════════════════════════════════

$CopiedFiles = @()
$CopiedEntries = @()

foreach ($routing in $FileRouting) {
    $sourceFiles = Get-ChildItem -Path $BrainDir -Filter $routing.Pattern -File -ErrorAction SilentlyContinue

    foreach ($file in $sourceFiles) {
        # Skip if already copied (earlier, more specific rule won)
        if ($CopiedFiles -contains $file.Name) {
            continue
        }

        # Skip files matching skip patterns
        $shouldSkip = $false
        foreach ($skipPattern in $SkipPatterns) {
            if ($file.Name -match $skipPattern) {
                $shouldSkip = $true
                break
            }
        }
        if ($shouldSkip) { continue }

        # Build destination path: .artifacts/<subfolder>/YYYY-MM-DD_<8-char-id>_<filename>
        $destFolder = Join-Path $ArtifactsDir $routing.Dest
        $destName = "${DatePrefix}_${ShortId}_$($file.Name)"
        $destPath = Join-Path $destFolder $destName

        # Copy the file
        try {
            Copy-Item -Path $file.FullName -Destination $destPath -Force
            $CopiedFiles += $file.Name
            $CopiedEntries += @{
                Date     = $DatePrefix
                ChatId   = $ShortId
                Type     = $routing.Type
                Path     = "$($routing.Dest)/$destName"
                FileName = $file.Name
            }
            Write-Host "[copy_artifacts]   $($file.Name) -> .artifacts/$($routing.Dest)/$destName"
        } catch {
            Write-Host "[copy_artifacts]   FAILED: $($file.Name) — $($_.Exception.Message)"
        }
    }
}

# ═══════════════════════════════════════════════════════════════════════════════
#  STEP 6: Update .artifacts/index.md
# ═══════════════════════════════════════════════════════════════════════════════

$IndexFile = Join-Path $ArtifactsDir "index.md"

if ((Test-Path $IndexFile) -and $CopiedEntries.Count -gt 0) {
    try {
        $content = Get-Content $IndexFile -Raw -ErrorAction Stop

        foreach ($entry in $CopiedEntries) {
            $row = "| $($entry.Date) | Chat $($entry.ChatId) | $($entry.Type) | ``$($entry.Path)`` | Auto-copied by stop hook |"

            # Replace placeholder row if it exists
            if ($content -match "\| — \| — \| — \| — \|") {
                $content = $content -replace "\| — \| — \| — \| — \|[^\r\n]*", $row
            } else {
                # Append after the last table row (find the last pipe-delimited line before ---)
                $content = $content -replace "(\|---\|)", "`$1`n$row"
            }
        }

        Set-Content -Path $IndexFile -Value $content -NoNewline -ErrorAction Stop
        Write-Host "[copy_artifacts] Updated .artifacts/index.md with $($CopiedEntries.Count) entries."
    } catch {
        Write-Host "[copy_artifacts] WARNING: Could not update index.md — $($_.Exception.Message)"
    }
}

# ═══════════════════════════════════════════════════════════════════════════════
#  DONE
# ═══════════════════════════════════════════════════════════════════════════════

Write-Host "[copy_artifacts] Complete. Copied $($CopiedFiles.Count) artifact(s) to .artifacts/"
exit 0
