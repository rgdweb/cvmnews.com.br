import { NextResponse } from 'next/server'
import { db } from '@/lib/db'

// GET /api/settings - Return public system settings (no auth required)
export async function GET() {
  try {
    const setting = await db.systemSetting.findUnique({
      where: { key: 'enableVoiceUpload' },
    })

    return NextResponse.json({
      enableVoiceUpload: setting?.value === 'true',
    })
  } catch (error) {
    console.error('Error getting public settings:', error)
    return NextResponse.json({ enableVoiceUpload: false })
  }
}
