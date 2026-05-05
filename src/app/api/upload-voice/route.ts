import { NextRequest, NextResponse } from 'next/server'

export const maxDuration = 60

const HF_SPACE_URL = process.env.HF_SPACE_URL || 'https://k2-fsa-omnivoice.hf.space'

// POST /api/upload-voice - Upload reference audio to Gradio (e PHP como backup)
export async function POST(req: NextRequest) {
  try {
    const formData = await req.formData()
    const file = formData.get('file') as File | null

    if (!file) {
      return NextResponse.json({ error: 'Nenhum arquivo fornecido' }, { status: 400 })
    }

    const ext = file.name.match(/\.(mp3|wav|ogg|m4a|flac|webm)$/i)?.[0] || '.wav'
    const uniqueName = `${Date.now()}-${Math.random().toString(36).substring(2, 8)}${ext}`

    // Step 1: Upload to Gradio Space (funciona sempre)
    const uploadForm = new FormData()
    uploadForm.append('files', file)

    let gradioPath = ''
    try {
      const uploadRes = await fetch(`${HF_SPACE_URL}/gradio_api/upload`, {
        method: 'POST',
        body: uploadForm,
      })

      if (uploadRes.ok) {
        const uploadData = await uploadRes.json()
        if (Array.isArray(uploadData) && uploadData.length > 0) {
          gradioPath = uploadData[0]
          console.log('[UploadVoice] Gradio upload OK:', gradioPath)
        }
      } else {
        const errText = await uploadRes.text()
        console.error('[UploadVoice] Gradio upload error:', uploadRes.status, errText.substring(0, 200))
      }
    } catch (err) {
      console.error('[UploadVoice] Gradio upload failed:', err)
    }

    if (!gradioPath) {
      return NextResponse.json(
        { error: 'Falha no upload para servidor de IA. Tente novamente.' },
        { status: 502 }
      )
    }

    // Step 2: Tentar upload pro PHP hosting (armazenamento permanente)
    // Se falhar, o audio ainda funciona via Gradio (vai ser re-upado na geracao)
    let serverUrl = ''
    let serverFilename = ''
    try {
      const { uploadToAudioServer } = await import('@/lib/audio-server')
      const audioServerResult = await uploadToAudioServer(file, uniqueName, 'ref')
      serverUrl = audioServerResult.url
      serverFilename = audioServerResult.filename
      console.log('[UploadVoice] PHP hosting OK:', serverUrl)
    } catch (err) {
      console.warn('[UploadVoice] PHP hosting falhou, audio ficara apenas no Gradio:', err)
    }

    // Construir URL do Gradio
    const gradioUrl = `${HF_SPACE_URL}/gradio_api/file=${gradioPath}`

    return NextResponse.json({
      path: gradioPath,
      serverUrl: serverUrl || gradioUrl,
      filename: serverFilename || uniqueName,
      url: serverUrl || gradioUrl,
      name: file.name,
      gradioPath: gradioPath,
      gradioUrl: gradioUrl,
      phpOnly: !serverUrl,
    })
  } catch (error) {
    console.error('[UploadVoice] Error:', error)
    return NextResponse.json(
      { error: error instanceof Error ? error.message : 'Erro no upload' },
      { status: 500 }
    )
  }
}
