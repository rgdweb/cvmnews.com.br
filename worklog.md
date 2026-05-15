---
Task ID: 1
Agent: Main Agent
Task: Adicionar botão de download de áudio de referência no painel admin

Work Log:
- Explorou a estrutura completa do projeto (Next.js 16 + Prisma + PHP backend)
- Leu o arquivo `src/app/admin/page.tsx` (~2500+ linhas) para entender a exibição de vozes e variações
- Identificou 4 locais onde botões de download precisavam ser adicionados:
  1. Variações de voz na view detalhada (categoria selecionada, linhas ~2143)
  2. Vozes compactas na folder view sem categoria (linhas ~2198)
  3. Trilhas na view detalhada (linhas ~2651)
  4. Trilhas compactas na folder view sem categoria (linhas ~2700)
- Adicionou `Download` do lucide-react nos imports
- Implementou tags `<a>` com atributo `download` para forçar download em vez de abrir no navegador
- Estilização azul para diferenciar dos outros botões de ação
- Build Next.js passou sem erros novos
- Commit `0c1de58`, push `cbfdfed` para GitHub

Stage Summary:
- Botão de download adicionado com sucesso em todas as views de vozes e trilhas
- Ícone azul (Download) aparece ao lado de Play/Pause, Upload, Edit, Switch e Delete
- Para vozes compactas (folder view), faz download da primeira variação ativa com áudio
- Para trilhas, usa o `track.audioPath` como URL de download
- Build OK, push OK
