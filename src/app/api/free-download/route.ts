import { NextRequest, NextResponse } from 'next/server'
import { getSession } from '@/lib/auth'
import { db } from '@/lib/db'

// GET /api/free-download - Check remaining free downloads
export async function GET() {
  try {
    const session = await getSession()
    if (!session.authenticated) {
      return NextResponse.json({ error: 'Não autenticado' }, { status: 401 })
    }

    const user = await db.user.findUnique({
      where: { id: session.userId },
      select: { freeDownloads: true },
    })

    if (!user) {
      return NextResponse.json({ error: 'Usuário não encontrado' }, { status: 404 })
    }

    return NextResponse.json({ freeDownloads: user.freeDownloads })
  } catch (error) {
    console.error('[Free Download] Check error:', error)
    return NextResponse.json({ freeDownloads: 0 }, { status: 500 })
  }
}

// POST /api/free-download - Use one free download
export async function POST() {
  try {
    const session = await getSession()
    if (!session.authenticated) {
      return NextResponse.json({ error: 'Não autenticado' }, { status: 401 })
    }

    const user = await db.user.findUnique({
      where: { id: session.userId },
      select: { freeDownloads: true },
    })

    if (!user) {
      return NextResponse.json({ error: 'Usuário não encontrado' }, { status: 404 })
    }

    if (user.freeDownloads <= 0) {
      return NextResponse.json({ error: 'Sem downloads gratuitos', hasFree: false, remaining: 0 })
    }

    const updated = await db.user.update({
      where: { id: session.userId },
      data: { freeDownloads: { decrement: 1 } },
    })

    return NextResponse.json({ 
      hasFree: true, 
      remaining: Math.max(0, updated.freeDownloads) 
    })
  } catch (error) {
    console.error('[Free Download] Use error:', error)
    return NextResponse.json({ error: 'Erro ao usar download gratuito' }, { status: 500 })
  }
}
