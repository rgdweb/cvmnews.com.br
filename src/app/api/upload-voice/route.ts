import { NextRequest, NextResponse } from 'next/server'

const HF_SPACE_URL = process.env.HF_SPACE_URL || 'https://k2-fsa-omnivoice.hf.space'

// POST /api/upload-voice - Upload reference audio to HuggingFace Space
export async function POST(req: NextRequest) {
  try {
    const formData = await req.formData()
    const file = formData.get('file') as File | null

    if (!file) {
      return NextResponse.json({ error: 'Nenhum arquivo fornecido' }, { status: 400 })
    }

    // Upload the file to the Gradio Space's upload endpoint
    const uploadForm = new FormData()
    uploadForm.append('files', file)

    const uploadRes = await fetch(`${HF_SPACE_URL}/gradio_api/upload`, {
      method: 'POST',
      body: uploadForm,
    })

    if (!uploadRes.ok) {
      const errText = await uploadRes.text()
      console.error('Upload error:', uploadRes.status, errText)
      return NextResponse.json(
        { error: `Falha no upload para HuggingFace: ${uploadRes.status}` },
        { status: 502 }
      )
    }

    const uploadData = await uploadRes.json()

    // Gradio returns an array of file paths
    if (Array.isArray(uploadData) && uploadData.length > 0) {
      return NextResponse.json({
        path: uploadData[0],
        url: `${HF_SPACE_URL}/gradio_api/file=${uploadData[0]}`,
        name: file.name,
      })
    }

    return NextResponse.json({ error: 'Resposta inesperada do upload' }, { status: 502 })
  } catch (error) {
    console.error('Upload voice error:', error)
    return NextResponse.json(
      { error: error instanceof Error ? error.message : 'Erro no upload' },
      { status: 500 }
    )
  }
}
