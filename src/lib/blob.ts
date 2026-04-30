import { put, del, head } from '@vercel/blob'

/**
 * Upload a file to Vercel Blob storage.
 * Returns the Blob URL of the uploaded file.
 */
export async function uploadToBlob(filename: string, file: File | Buffer, contentType?: string): Promise<string> {
  const blob = await put(filename, file, {
    access: 'public',
    contentType,
  })
  return blob.url
}

/**
 * Delete a file from Vercel Blob storage by its URL.
 */
export async function deleteFromBlob(url: string): Promise<void> {
  try {
    await del(url)
  } catch (error) {
    console.error('[Blob] Delete error:', error)
    // Don't throw - blob deletion should be best-effort
  }
}

/**
 * Check if a URL is a Vercel Blob URL.
 */
export function isBlobUrl(url: string): boolean {
  return url.includes('.blob.vercel-storage.com') || url.includes('public.blob.vercel-storage.com')
}

/**
 * Get metadata about a blob file.
 */
export async function getBlobMetadata(url: string) {
  try {
    const metadata = await head(url)
    return metadata
  } catch {
    return null
  }
}
