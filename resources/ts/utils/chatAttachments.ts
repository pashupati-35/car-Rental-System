import type { AttachmentPolicy } from '@/types/chat'

/**
 * Shared helpers for client-side attachment validation against the server's
 * AttachmentPolicy.  These mirror the rules enforced by
 * App\Services\Chat\ChatAttachmentService — keep them in sync.
 */

export interface FileCheck {
  ok:     boolean
  error?: string
}

/** Validate one file against the policy (size + extension). */
export function checkFile(file: File, policy: AttachmentPolicy | null): FileCheck {
  if (!policy) return { ok: true }

  const sizeKb = Math.ceil(file.size / 1024)
  if (sizeKb > policy.max_kilobytes) {
    return { ok: false, error: `${file.name}: too large (max ${policy.max_megabytes} MB).` }
  }

  const ext = (file.name.split('.').pop() ?? '').toLowerCase()
  if (!policy.allowed_extensions.includes(ext)) {
    return { ok: false, error: `${file.name}: type not allowed (${policy.allowed_extensions.join(', ')}).` }
  }

  return { ok: true }
}

/**
 * Partition a batch of files into accepted and rejected.  Returns
 * human-readable error strings for each rejected file so the caller can show
 * them in the UI without losing the rest of the batch.
 */
export function partitionFiles(
  files: File[],
  policy: AttachmentPolicy | null,
): { valid: File[]; errors: string[] } {
  const valid: File[] = []
  const errors: string[] = []

  for (const f of files) {
    const r = checkFile(f, policy)
    if (r.ok) valid.push(f)
    else if (r.error) errors.push(r.error)
  }

  return { valid, errors }
}

/**
 * Filter by an explicit MIME-type allowlist (used by the standard chat input
 * which only accepts images).  Returns the same `{valid, errors}` shape so
 * UI handling is uniform.
 */
export function partitionByMime(
  files: File[],
  allowedMimes: Set<string>,
  label = 'JPG, PNG, or WebP',
): { valid: File[]; errors: string[] } {
  const valid: File[] = []
  const errors: string[] = []

  for (const f of files) {
    if (allowedMimes.has(f.type)) valid.push(f)
    else errors.push(`${f.name}: only ${label} allowed.`)
  }

  return { valid, errors }
}
