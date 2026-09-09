/**
 * Shared document helpers used by both agent and student application-detail views.
 *
 * Single source of truth so `is_required` coercion and pending-document
 * counts can never drift between portals.
 */

/**
 * Backend may emit `is_required` as `true`, `1`, `'1'`, or `'true'` depending
 * on the serialization path. This normalises all of them.
 */
export const isRequiredDocument = (row: unknown): boolean => {
  const val = (row as Record<string, unknown> | null)?.is_required
  
  return val === true || val === 1 || val === '1' || val === 'true'
}

/**
 * Whether a document row has been submitted (file URL or link present).
 */
export const isSubmittedDocument = (row: unknown): boolean => {
  const r = row as Record<string, unknown> | null
  const url = (r?.user_submitted_file as Record<string, unknown> | null)?.original ?? r?.link ?? ''
  
  return Boolean(url)
}

/**
 * Count the required-but-not-yet-submitted rows.
 */
export const pendingRequiredCount = (
  totalDocuments: number,
  totalSubmittedDocuments: number,
): number => Math.max(totalDocuments - totalSubmittedDocuments, 0)

/**
 * Filter to only required rows from a list.
 */
export const filterRequired = <T>(rows: T[]): T[] =>
  rows.filter(isRequiredDocument)

/**
 * Count of required rows that have been submitted.
 */
export const requiredSubmittedCount = <T>(rows: T[]): number =>
  filterRequired(rows).filter(isSubmittedDocument).length
