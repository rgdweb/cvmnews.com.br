---
Task ID: 1
Agent: main
Task: Fix chunking regression — audio still 33% short even with 7 chunks

Work Log:
- Diagnosed root cause: `postprocess_output: true` was being sent for EACH chunk individually, causing OmniVoice to cut ~29% of each chunk's audio — same as the single-shot issue
- Key insight: The postprocess was designed for single-shot long text, but for chunks it was still active and cutting per-chunk audio
- Fix 1: Set `postprocess_output: false` for all chunks in `generateChunk()` via `data[11] = false`
- Fix 2: Removed `trimPcmStart()` function — was risky (threshold 200 could clip word beginnings) and unnecessary without postprocess cutting
- Fix 3: Increased chunk download delay from `min(3000, 1500 + ...)` to `min(5000, 2500 + ...)` for more robust file saving
- Fix 4: Added `ChunkResult` interface returning buffer + durationSec + textLength per chunk
- Fix 5: Added `chunkDiagnostics` array to response with per-chunk success/failure and duration
- Fix 6: Added `failedChunks`, `succeededChunks`, `postprocessDisabled` to chunking response
- Fix 7: Updated page.tsx debug panel to show per-chunk durations, failure badges, total audio time

Stage Summary:
- Root cause: postprocess_output=true cutting each chunk individually
- Solution: Disable postprocess for chunks (data[11] = false) — raw TTS output preserved
- Build passes successfully
- Files modified: src/app/api/tunnel-generate/route.ts, src/app/page.tsx
