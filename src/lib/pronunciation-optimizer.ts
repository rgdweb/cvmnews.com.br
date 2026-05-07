/**
 * Pronunciation Optimizer — Pipeline completo de pronúncia PT-BR para TTS
 *
 * Camada 1: Regex expandido (0ms de latência)
 * Camada 2: Dicionário de palavras problemáticas (0ms)
 * Camada 3: LLM fallback (1-3s, só quando necessário)
 *
 * Substitui a função optimizePronunciation() inline do page.tsx.
 * Usa colchetes [pronúncia] nativos do VozPro e troca de pontuação
 * para controlar prosódia.
 */

// ============================================================
// NÚMEROS POR EXTENSO (0 até bilhões)
// ============================================================

const UNITS = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove']
const TEENS = ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove']
const TENS = ['', '', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa']
const HUNDREDS = ['', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos']

/**
 * Converte número inteiro (0-999.999.999) para palavras em PT-BR.
 */
export function numberToWords(n: number): string {
  if (n === 0) return 'zero'
  if (n < 0) return 'menos ' + numberToWords(-n)
  if (n > 999999999) return String(n) // fora do alcance

  const parts: string[] = []

  // Milhões
  if (n >= 1000000) {
    const millions = Math.floor(n / 1000000)
    parts.push(millions === 1 ? 'um milhão' : numberToWords(millions) + ' milhões')
    n %= 1000000
  }

  // Milhares
  if (n >= 1000) {
    const thousands = Math.floor(n / 1000)
    if (thousands === 1) {
      parts.push('mil')
    } else {
      parts.push(numberToWords(thousands) + ' mil')
    }
    n %= 1000
  }

  // Centenas
  if (n >= 100) {
    if (n === 100) {
      parts.push('cem')
    } else {
      parts.push(HUNDREDS[Math.floor(n / 100)])
    }
    n %= 100
  }

  // Dezenas e unidades
  if (n >= 20) {
    const t = Math.floor(n / 10)
    const u = n % 10
    if (u === 0) {
      parts.push(TENS[t])
    } else {
      parts.push(TENS[t] + ' e ' + UNITS[u])
    }
  } else if (n >= 10) {
    parts.push(TEENS[n - 10])
  } else if (n > 0) {
    parts.push(UNITS[n])
  }

  return parts.join(' e ')
}

/** Converte valor monetário para palavras: "1.599,90" → "mil quinhentos e noventa e nove reais e noventa centavos" */
export function currencyToWords(val: string): string {
  const clean = val.replace(/\./g, '').replace(',', '.')
  const num = parseFloat(clean)
  if (isNaN(num)) return val

  if (num === 1) return 'um real'
  if (num < 0.01) return 'zero reais'

  const reais = Math.floor(num)
  const centavos = Math.round((num - reais) * 100)
  let result = ''

  if (reais > 0) {
    result = reais === 1 ? 'um real' : numberToWords(reais) + ' reais'
  }
  if (centavos > 0) {
    if (result) result += ' e '
    result += centavos === 1 ? 'um centavo' : numberToWords(centavos) + ' centavos'
  }

  return result || 'zero reais'
}

// ============================================================
// DICIONÁRIO DE PRONÚNCIA PT-BR
// ============================================================

/**
 * Palavras que o VozPro/F5-TTS frequentemente pronuncia errado em PT-BR.
 * Formato: palavra_original → pronúncia_correta
 *
 * O TTS tende a:
 * - Ler siglas como palavras ("DVD" → "davide" em vez de "dê vê dê")
 * - Pronunciar estrangeirismos com sotaque inglês ("marketing" → "márketing")
 * - Ler abreviações literalmente ("Av." → "ave" em vez de "avenida")
 * - Confundir homógrafos ("segundo" tempo vs "segundo" número)
 */
const PRONUNCIATION_DICTIONARY: Record<string, string> = {
  // === SIGLAS / ACRÔNIMOS (soletrar) ===
  'API': '[a p i]',
  'DVD': '[dê vê dê]',
  'GPS': '[gê pê és]',
  'IPTU': '[i pê tê u]',
  'INSS': '[i êne és és]',
  'URL': '[u erre éle]',
  'PDF': '[pê dê éfe]',
  'HTML': '[agá tê ême éle]',
  'CSS': '[cê és és]',
  'CRM': '[cê erre ême]',
  'CNPJ': '[cê êne pê jota]',
  'CPF': '[cê pê éfe]',
  'RG': '[erre gê]',
  'IMC': '[i ême cê]',
  'DVDs': '[dê vê dês]',
  'CEP': '[cê ê pê]',
  'CNPJs': '[cê êne pê jotas]',
  'CPFs': '[cê pê és]',
  'PIB': '[pê i bê]',
  'PIBC': '[pê i bê cê]',
  'SUV': '[ês u vê]',
  'IBGE': '[i bê gê i]',
  'PNG': '[pê êne gê]',
  'JPG': '[jota pê gê]',
  'GIF': '[gife]',
  'USB': '[u és bê]',
  'Wi-Fi': '[uái fái]',
  'wifi': '[uái fái]',
  'WiFi': '[uái fái]',
  '3D': '[três dê]',
  '4D': '[quatro dê]',
  '5G': '[quinto gê]',
  '4G': '[quarto gê]',
  'HD': '[agá dê]',
  'SSD': '[ês és dê]',

  // === ESTRANGEIRISMOS COMUNS (pronúncia aportuguesada) ===
  'marketing': '[marqueting]',
  'Marketing': '[Marqueting]',
  'MARKETING': '[MARQUETING]',
  'download': '[daunloud]',
  'Download': '[Daunloud]',
  'upload': '[aploud]',
  'Upload': '[Aploud]',
  'software': '[softeuér]',
  'Software': '[Softeuér]',
  'hardware': '[ardeuér]',
  'Hardware': '[Ardeuér]',
  'mouse': '[mause]',
  'Mouse': '[Mause]',
  'link': '[linque]',
  'Link': '[Linque]',
  'links': '[linques]',
  'Links': '[Linques]',
  'login': '[login]',
  'Login': '[Login]',
  'logout': '[logoúte]',
  'Logout': '[Logoúte]',
  'online': '[onlaine]',
  'Online': '[Onlaine]',
  'offline': '[offlaine]',
  'Offline': '[Offlaine]',
  'browser': '[brauzér]',
  'Browser': '[Brauzér]',
  'app': '[épe]',
  'App': '[Épe]',
  'apps': '[épes]',
  'Apps': '[Épes]',
  'startup': '[startape]',
  'Startup': '[Startape]',
  'feedback': '[fidebáque]',
  'Feedback': '[Fidebáque]',
  'layout': '[leiáute]',
  'Layout': '[Leiáute]',
  'design': '[dizaine]',
  'Design': '[Dizaine]',
  'sprint': '[esprinte]',
  'Sprint': '[Esprinte]',
  'benchmark': '[benchmarque]',
  'Benchmark': '[Benchmarque]',
  'hacker': '[râquer]',
  'Hacker': '[Râquer]',
  'podcast': '[podcáste]',
  'Podcast': '[Podcáste]',
  'vlog': '[vlogue]',
  'Vlog': '[Vlogue]',
  'blog': '[blogue]',
  'Blog': '[Blogue]',
  'e-commerce': '[comércio eletrônico]',
  'e-mail': '[imeil]',
  'email': '[imeil]',
  'E-mail': '[Imeil]',
  'site': '[sáite]',
  'Site': '[Sáite]',
  'smartphone': '[smartifone]',
  'Smartphone': '[Smartifone]',
  'selfie': '[selfie]',
  'Selfie': '[Selfie]',
  'hashtag': '[rastague]',
  'Hashtag': '[Rastague]',
  'influencer': '[influenser]',
  'Influencer': '[Influenser]',
  'live': '[laive]',
  'Live': '[Laive]',
  'streaming': '[estrimingue]',
  'Streaming': '[Estrimgue]',
  'know-how': '[nou rau]',
  'showroom': '[chorume]', // aportuguesado
  'background': '[bécigraunde]',
  'framework': '[freimeuorquê]',
  'office': '[ófice]',
  'Office': '[Ófice]',
  'business': '[biznise]',
  'performance': '[perfománsse]',
  'standard': '[stándarde]',
  'ranking': '[ranquingue]',
  'tester': '[téster]',
  'manager': '[manájer]',
  'partner': '[pártenér]',
  'delivery': '[delivéri]',
  'coffee': '[cófi]',
  'break': '[breique]',
  'meeting': '[mitingue]',

  'home': '[roume]',
  'upgrade': '[apgreide]',
  'downgrade': '[daungreide]',
  'backup': '[bécape]',
  'chip': '[tchip]',
  'byte': '[baite]',
  'pixel': '[píxél]',
  'click': '[clique]',
  'touch': '[tatx]',
  'display': '[displei]',
  'storage': '[estorage]',
  'server': '[servér]',
  'router': '[raúter]',
  'switch': '[suitx]',
  'patch': '[pétch]',
  'hug': '[rague]',
  'spray': '[espréi]',
  'sticker': '[stiquér]',
  'flag': '[flague]',
  'kit': '[quité]',
  'Premium': '[Prêmium]',
  'premium': '[prêmium]',
  'VIP': '[vipe]',
  'vip': '[vipe]',
  'outlet': '[aulete]',
  'smart': '[smarte]',
  'factory': '[fáctore]',
  'outdoor': '[aútedor]',
  'drive-thru': '[draive tru]',
  'play': '[plei]',
  'stop': '[stope]',
  'start': '[starte]',
  'fast': '[fáste]',
  'food': '[fude]',
  'center': '[senter]',
  'shopping': '[choping]',
  'fitness': '[fitnes]',
  'personal': '[perssonal]',
  'trainer': '[treiner]',
  'crossfit': '[crosfite]',
  'boot': '[bute]',
  'bootcamp': '[butecampe]',
  'coding': '[codingue]',
  'debug': '[dibague]',
  'deploy': '[diploy]',
  'commit': '[comite]',
  'token': '[toquên]',
  'cache': '[cache]',
  'cookies': '[cúquis]',
  'script': '[escripte]',
  'prompt': '[prompete]',
  'bot': '[bote]',
  'chat': '[chate]',
  'share': '[chere]',
  'like': '[laique]',
  'post': '[póste]',
  'tag': '[tegue]',
  'viral': '[vairal]',
  'hype': '[raipe]',
  'geek': '[guique]',
  'nerd': '[nerde]',
  'pop': '[pope]',
  'rock': '[roque]',
  'jazz': '[jázze]',
  'blues': '[blúze]',
  'remix': '[remixe]',
  'featuring': '[fiuturinge]',
  'rapper': '[reper]',
  'gameplay': '[gemeplei]',
  'gameover': '[geme ouver]',
  'e-sports': '[isportes]',
  'esports': '[isportes]',
  'score': '[escóre]',
  'goal': '[gole]',
  'penalti': '[penalte]',
  'shoot': '[chute]',
  'match': '[metxe]',
  'round': '[raunde]',
  'set': '[sete]',

  // === MAIS ESTRANGEIRISMOS (inglês comum no PT-BR) ===
  // O TTS fala tudo errado quando vê palavras em inglês
  'bullshit': '[buxite]',
  'fuck': '[foque]',
  'shit': '[xite]',
  'yeah': '[ié]',
  'Wow': '[Uau]',
  'wow': '[uau]',
  'Oh': '[Ó]',
  'oh': '[ó]',
  'OK': '[oquêi]',
  'ok': '[oquêi]',
  'Ok': '[oquêi]',
  'friend': '[frende]',
  'friends': '[frendes]',
  'best': '[béste]',
  'bad': '[bède]',
  'cool': '[cule]',
  'nice': '[náise]',
  'hot': '[rote]',
  'top': '[tope]',
  // 'super' — NÃO mapear! "super" em PT-BR já é pronunciado corretamente
  'full': '[fule]',
  'deal': '[dile]',
  'fail': '[feile]',
  'test': '[téste]',
  'team': '[time]',
  'tech': '[tcheque]',
  // 'data' — NÃO mapear! "data" em PT-BR é palavra comum ("data de nascimento")
  'code': '[code]',  // só em contexto tech
  'tool': '[tule]',
  'free': '[frí]',
  // 'pro' — NÃO mapear! "pro" em PT-BR é contração de "para o"
  'new': '[niú]',
  'big': '[bige]',
  'old': '[oude]',
  // 'real' — NÃO mapear! "real" em PT-BR é moeda e adjetivo comum
  'over': '[ouver]',
  'power': '[pauer]',
  'prime': '[praimi]',
  'cash': '[caxe]',
  'card': '[carde]',
  'bank': '[banque]',
  'risk': '[risque]',
  'push': '[puxe]',
  'pull': '[pule]',
  'bug': '[beige]',
  'web': '[uébé]',
  'map': '[mepe]',
  'run': '[rane]',
  'win': '[uine]',
  'end': '[ende]',
  'off': '[ofe]',
  'out': '[aute]',
  'yes': '[iése]',
  // 'no' — NÃO mapear! "no" em PT-BR é preposição comum ("no dia", "no carro")
  // Só palavras claramente inglesas em contexto inglês devem ser mapeadas
  'hi': '[rai]',
  'hey': '[rêi]',
  'bye': '[bái]',
  'please': '[plíze]',
  'sorry': '[sóri]',
  'thanks': '[fencos]',
  'welcome': '[uelcome]',
  'hello': '[relou]',
  // 'world' aparece 2x nesta seção — mantendo apenas esta ocorrência
  'work': '[uorque]',
  'job': '[jóbe]',
  // 'office' já existe acima na seção de estrangeirismos
  'money': '[mane]',
  'time': '[taime]',
  'life': '[laife]',
  'love': '[lave]',
  'night': '[naite]',
  'day': '[dêi]',
  'game': '[gêime]',
  // 'home' já existe acima na seção de estrangeirismos
  'hand': '[rende]',
  'head': '[rede]',
  'heart': '[rarte]',
  'help': '[relpe]',
  'back': '[bèque]',
  'door': '[dore]',
  'face': '[feice]',
  'girl': '[gueirle]',
  'guy': '[gái]',
  'man': '[mene]',
  'name': '[neime]',
  'place': '[pleice]',
  'point': '[poite]',
  'road': '[roude]',
  'room': '[rume]',
  'show': '[xou]',
  'star': '[stare]',
  'step': '[stepe]',
  'story': '[stóri]',
  'talk': '[toque]',
  'thing': '[finge]',
  'type': '[taipe]',
  'view': '[viú]',
  'voice': '[voise]',
  'watch': '[uotxe]',
  'word': '[ueorde]',
  'write': '[raite]',
  'young': '[ionge]',

  // === ABREVIAÇÕES (expandir) ===
  'Sr.': '[Senhor]',
  'Sra.': '[Senhora]',
  'Srta.': '[Senhorita]',
  'Dr.': '[Doutor]',
  'Dra.': '[Doutora]',
  'Prof.': '[Professor]',
  'Profa.': '[Professora]',
  'Gov.': '[Governador]',
  'Govª.': '[Governadora]',
  'Av.': '[Avenida]',
  'R.': '[Rua]',
  'Pça.': '[Praça]',
  'Ltda.': '[Limitada]',
  'S/A': '[Sociedade Anônima]',
  'MEI': '[Microempreendedor Individual]',
  'ME': '[Microempresa]',
  'EPP': '[Empresa de Pequeno Porte]',
  'Vol.': '[Volume]',
  'Cap.': '[Capítulo]',
  'Pág.': '[Página]',
  'Tel.': '[Telefone]',
  'Ref.': '[Referência]',
  'Obs.': '[Observação]',
  'Exmo.': '[Excelentíssimo]',
  'Exma.': '[Excelentíssima]',
  'Ilmo.': '[Ilustríssimo]',
  'Ilma.': '[Ilustríssima]',
  'V.Exa.': '[Vossa Excelência]',
  'V.Sa.': '[Vossa Senhoria]',
  'Att.': '[Atenciosamente]',
  'Cia.': '[Companhia]',
  'Deptº': '[Departamento]',
  'Min.': '[Ministro]',
  'Maj.': '[Major]',
  'Cel.': '[Coronel]',
  'Gen.': '[General]',
  'Emb.': '[Embaixador]',

  // === PALAVRAS PROBLEMÁTICAS ESPECÍFICAS DO TTS ===
  // O VozPro/F5-TTS frequentemente pronuncia estas errado
  // CONSOANTES MUDAS — o modelo DROPA o P/C inicial
  'pneu': '[peneu]',
  'Pneu': '[Peneu]',
  'pneus': '[peneus]',
  'Pneus': '[Peneus]',
  'pneumonia': '[peneumonia]',
  'Pneumonia': '[Peneumonia]',
  'pneumonita': '[peneumonite]',
  'Pneumonita': '[Peneumonite]',
  'pneumático': '[peneumático]',
  'Pneumático': '[Peneumático]',
  'pneumotórax': '[peneumotórax]',
  'psicólogo': '[psicólogo]',
  'Psicólogo': '[Psicólogo]',
  'psiquiatra': '[psiquiatra]',
  'Psiquiatra': '[Psiquiatra]',
  'psicose': '[psicose]',
  'psicopata': '[psicopata]',
  'ptialismo': '[petialismo]',
  'Ptialismo': '[Petialismo]',
  'ptose': '[petose]',
  'gnomo': '[nomo]',
  'Gnomo': '[Nomo]',
  'gnose': '[nose]',
  'Gnose': '[Nose]',
  'gnóstico': '[nóstico]',
  'mnemônico': '[nemônico]',
  'Mnemônico': '[Nemônico]',
  'mnemônica': '[nemônica]',
  'cpt': '[cê pê tê]',
  'CPT': '[cê pê tê]',
  // Consoantes mudas adicionais (que não estavam no bloco acima)
  'psicologia': '[psicologia]',  // PS- em psicologia o TTS geralmente acerta
  'Psicologia': '[Psicologia]',
  'psiquiatria': '[psiquiatria]',
  'Psiquiatria': '[Psiquiatria]',
  'psi': '[psí]',
  'Psi': '[Psí]',
  'pterodáctilo': '[pterodáctilo]',
  'Pterodáctilo': '[Pterodáctilo]',
  'tbd': '[tê bê dê]',
  'TBD': '[tê bê dê]',
  'i.e.': '[id est]',
  'e.g.': '[exemplo grátis]',

  // H MUDO — apenas palavras onde o TTS REALMENTE erra
  // NOTA: A maioria das palavras com H mudo (hoje, hora, hotel, homem, humor)
  // o TTS já pronuncia corretamente. Só mapear as problemáticas.
  // Palavras comuns com H mudo foram REMOVIDAS pois causavam regressão
  // (o TTS pronuncia "hoje" corretamente, não precisa virar "[oje]")
  'hernia': '[érnia]',
  'Hérnia': '[Érnia]',

  // OUTRAS PALAVRAS PROBLEMÁTICAS
  'automóvel': '[automóvel]',
  'Automóvel': '[Automóvel]',
  'automóveis': '[automóveis]',
  'Automóveis': '[Automóveis]',
  'ecocardiograma': '[ecocardiograma]',
  'transesofágico': '[transesofágico]',
  'estenose': '[estenose]',
  'adenocarcinoma': '[adenocarcinoma]',
  'eletroencefalograma': '[eletroencefalograma]',
  'hemodiálise': '[emodiálise]',
  'azitromicina': '[azitromicina]',
  'omeprazol': '[omeprazol]',
  'dipirona': '[dipirona]',
  'ressonância': '[ressonância]',
  'metástase': '[metástase]',
  'aneurisma': '[aneurisma]',
  'insuficiência': '[insuficiência]',
  'biópsia': '[biópsia]',

  // === NOMES PRÓPRIOS DIFÍCEIS ===
  'Wolski': '[Volski]',
  'Kowalski': '[Covalski]',
  'Higashi': '[Rigaxi]',
  'Schütz': '[Xuts]',
  'Constança': '[Constança]',
  'Ilhéus': '[Ilhéus]',
  'Niterói': '[Niterói]',
  'Teotônio': '[Teotônio]',
  'Xangai': '[Xangai]',
  'Yngrid': '[Ingrid]',
  "L'Oréal": '[Loreal]',
}

// ============================================================
// PRÉ-PROCESSADOR DE X — 6 sons contextuais
// ============================================================

/**
 * Dicionário de palavras com X que o TTS pronuncia errado.
 * Mapeia a palavra completa para a versão com pronúncia correta.
 *
 * O X em português tem 6 sons possíveis:
 * - KS: táxi, sexo, complexo, perplexo, têxtil, axila, sintaxe
 * - CH: xarope, xaxim, xadrez, xampu, enxada, enxame, peixada, baixar
 * - Z: exército, exemplo, exercício, existir, exílio, exigir, exame
 * - SS: México, vexame, mexer, mexicano
 * - S: extensão, explicar, exportar, expressão, extraordinário
 * - Z (pós-vogal): exílio, existir, exótico
 */
const X_WORD_DICTIONARY: Record<string, string> = {
  // X = CH (som de "ch")
  'xarope': '[charope]',
  'Xarope': '[Charope]',
  'xaxim': '[chachim]',
  'Xaxim': '[Chachim]',
  'xadrez': '[chadrez]',
  'Xadrez': '[Chadrez]',
  'xampu': '[champu]',
  'Xampu': '[Champu]',
  'xavante': '[chavante]',
  'Xavante': '[Chavante]',
  'enxada': '[enchada]',
  'enxame': '[enchame]',
  'enxoval': '[enchoval]',
  'enxaqueca': '[enchaqueca]',
  'enxuto': '[enchuto]',
  'peixada': '[peichada]',
  'Peixada': '[Peichada]',
  'peixe': '[peiche]',
  'Peixe': '[Peiche]',
  'baixar': '[baichar]',
  'Baixar': '[Baichar]',
  'baixo': '[baicho]',
  'Baixo': '[Baicho]',
  'baixa': '[baicha]',
  'Baixa': '[Baicha]',
  'caxinguelê': '[cachinguelê]',
  'relaxar': '[relachar]',
  'Relaxar': '[Relachar]',
  'relaxamento': '[relachamento]',
  'Relaxamento': '[Relachamento]',
  'axila': '[achila]',
  'Axila': '[Achila]',

  // X = Z (som de "z" — ex- antes de vogal)
  'exército': '[ezército]',
  'Exército': '[Ezército]',
  'exemplo': '[ezemplo]',
  'Exemplo': '[Ezemplo]',
  'exercício': '[ezercício]',
  'Exercício': '[Ezercício]',
  'exigir': '[ezigir]',
  'Exigir': '[Ezigir]',
  'exílio': '[ezílio]',
  'Exílio': '[Ezílio]',
  'existir': '[ezistir]',
  'Existir': '[Ezistir]',
  'exame': '[ezame]',
  'Exame': '[Ezame]',
  'exato': '[ezato]',
  'Exato': '[Ezato]',
  'exceção': '[ezeção]',
  'Exceção': '[Ezeção]',
  'excluir': '[ezcluir]',
  'Excluir': '[Ezcluir]',
  'executar': '[ezecutar]',
  'Executar': '[Ezecutar]',
  'exibir': '[ezibir]',
  'Exibir': '[Ezibir]',
  'exótico': '[ezótico]',
  'Exótico': '[Ezótico]',
  'expor': '[ezpor]',
  'Expor': '[Ezpor]',
  'extensão': '[estensão]',
  'Extensão': '[Estensão]',
  'explicar': '[esplicar]',
  'Explicar': '[Esplicar]',
  'exportar': '[exportar]',
  'Exportar': '[Exportar]',
  'expressão': '[espressão]',
  'Expressão': '[Espressão]',
  'extraordinário': '[estraordinário]',
  'Extraordinário': '[Estraordinário]',
  'extrato': '[estrato]',
  'Extrato': '[Estrato]',
  'experiência': '[esperiência]',
  'Experiência': '[Esperiência]',
  'expresso': '[espresso]',
  'Expresso': '[Espresso]',
  'explosão': '[esplosão]',
  'Explosão': '[Esplosão]',
  'explorar': '[esplorar]',
  'Explorar': '[Esplorar]',
  'exposição': '[esposição]',
  'Exposição': '[Esposição]',
  'explícito': '[esplicito]',
  'Explícito': '[Esplicito]',
  'expectativa': '[espectativa]',
  'Expectativa': '[Espectativa]',
  'exíguo': '[ezíguo]',
  'Exíguo': '[Ezíguo]',

  // X = SS (som de "ss")
  'México': '[Méssico]',
  'mexicano': '[messicano]',
  'Mexicano': '[Messicano]',
  'mexicana': '[messicana]',
  'Mexicana': '[Messicana]',
  'vexame': '[vessame]',
  'Vexame': '[Vessame]',
  'mexer': '[messer]',
  'Mexer': '[Messer]',
  'mexida': '[messida]',
  'Mexida': '[Messida]',

  // X = KS (som de "ks")
  'táxi': '[tácsi]',
  'Táxi': '[Tácsi]',
  'sexo': '[sessso]',
  'Sexo': '[Sessso]',
  'complexo': '[complekso]',
  'Complexo': '[Complekso]',
  'perplexo': '[perplekso]',
  'Perplexo': '[Perplekso]',
  'têxtil': '[têkstil]',
  'Têxtil': '[Têkstil]',
  'sintaxe': '[sintakse]',
  'Sintaxe': '[Sintakse]',
  'ortodoxo': '[ortodokso]',
  'Ortodoxo': '[Ortodokso]',
  'paradoxo': '[paradokso]',
  'Paradoxo': '[Paradokso]',
  'nexus': '[neksus]',
  'fixo': '[fikso]',
  'Fixo': '[Fikso]',
  'fixar': '[fiksar]',
  'Fixar': '[Fiksar]',
  'maximizar': '[maksimizar]',
  'Maximizar': '[Maksimizar]',
  'máximo': '[máksimo]',
  'Máximo': '[Máksimo]',
  'mínimo': '[mínimo]',
  'taxa': '[taksa]',
  'Taxa': '[Taksa]',
  'oxigênio': '[oksijênio]',
  'Oxigênio': '[Oksijênio]',
  'tóxico': '[tóksico]',
  'Tóxico': '[Tóksico]',
  'toxina': '[toksina]',
  'Toxina': '[Toksina]',
  'intoxicação': '[intoksicação]',
  'Intoxicação': '[Intoksicação]',

  // Xangai — nome próprio, som de CH
  'Xangai': '[Xangai]',
}

/**
 * Pré-processa todas as ocorrências de X no texto,
 * substituindo palavras com X pela pronúncia correta.
 */
function preprocessX(text: string): string {
  let result = text

  // 1. Aplicar dicionário de palavras com X (maior precisão)
  for (const [word, pronunciation] of Object.entries(X_WORD_DICTIONARY)) {
    const escaped = word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
    result = result.replace(new RegExp(`\\b${escaped}\\b`, 'g'), pronunciation)
  }

  // 2. X restantes: regra geral contextual
  // X antes de consoante = S (ex: "extensão" → já coberto pelo dicionário, mas fallback)
  // X no final de sílaba antes de consoante
  result = result.replace(/\bx([bcdfghjklmnpqrstvwxyz])/gi, (match, consonant) => {
    const isUpper = match[0] === match[0].toUpperCase()
    return isUpper ? `S${consonant}` : `s${consonant}`
  })

  // 3. X entre vogais que não foi coberto = KS (fallback)
  result = result.replace(/([aeiouáàãâéèêíïóôõúü])x([aeiouáàãâéèêíïóôõúü])/gi, (match, v1, v2) => {
    return `${v1}ks${v2}`
  })

  return result
}

// ============================================================
// REGEX EXPANDIDO — TODOS OS PADRÕES PT-BR
// ============================================================

/**
 * Pipeline completa de otimização de pronúncia (regex, 0ms).
 *
 * Correções na ordem:
 * 1. Artigos após pontuação (elimina hesitação)
 * 1b. Artigos iniciais O/A antes de nomes próprios e títulos
 * 1c. Pré-processador de X (6 sons contextuais)
 * 2. Números por extenso (evita leitura literal)
 * 3. Valores monetários
 * 4. Porcentagens
 * 5. Horários completos (14h30, 08:30)
 * 6. Datas (15/03/2024)
 * 7. Telefones ((11) 99999-9999)
 * 8. Ordinais (1º, 2ª, 3º)
 * 9. Abreviações (do dicionário)
 * 10. Siglas/acrônimos (do dicionário)
 * 11. Estrangeirismos (do dicionário)
 * 12. Palavras problemáticas (do dicionário)
 * 13. URLs
 * 14. Emails
 * 15. Pontuação dupla e limpeza
 */
export function optimizePronunciation(text: string): string {
  let result = text

  // ---- 1. ARTIGOS APÓS PONTUAÇÃO (elimina hesitação do TTS) ----
  // ". O sistema" → ", o sistema" (troca ponto por vírgula = une frases)
  result = result.replace(/([.!?])\s+([OoAa])\s(?=[a-záàãâéèêíïóôõúüç])/g, ',$2 ')
  result = result.replace(/([.!?])\s+([Oo]s|[Aa]s|[Uu]m(?:[oa]s)?)\s(?=[a-záàãâéèêíïóôõúüç])/g, ',$2 ')

  // ---- 1b. ARTIGOS INICIAIS O/A ANTES DE NOMES PRÓPRIOS E TÍTULOS ----
  // O modelo ENGOLE o "O" antes de nomes próprios maiúsculos
  // "O Dr." → "[o] Doutor", "O Wolski" → "[o] [Volski]"
  // Padrão: O/A maiúsculo no início da frase ou após pontuação + palavra maiúscula
  result = result.replace(/\b([Oo])\s+(Dr\.|Dra\.|Sr\.|Sra\.|Prof\.|Profa\.|Gov\.|Emb\.|Cel\.|Maj\.|Gen\.|Min\.)/g, '[$1] $2')
  // Artigo antes de nome próprio (maiúscula após artigo isolado)
  result = result.replace(/(?:^|\n|[,;!?]\s*)([Oo])\s+([A-Z][a-záàãâéèêíïóôõúüç])/g, (match, artigo, name) => {
    return match.replace(`${artigo} ${name}`, `[${artigo}] ${name}`)
  })
  // Artigo "A" antes de nome próprio feminino
  result = result.replace(/(?:^|\n|[,;!?]\s*)([Aa])\s+([A-Z][a-záàãâéèêíïóôõúüç])/g, (match, artigo, name) => {
    return match.replace(`${artigo} ${name}`, `[${artigo}] ${name}`)
  })

  // ---- 1c. PRÉ-PROCESSADOR DE X (6 sons contextuais em PT-BR) ----
  // O modelo NÃO sabe qual som de X usar pelo contexto
  // Regras contextuais de pronúncia do X:
  //   - X antes de vogal tônica = KS (táxi, sexo, têxtil)
  //   - X após E = KS (complexo, sexo, têxtil, perplexo)
  //   - X antes de consonante = S (extensão, explicar, exportar)
  //   - X em palavras específicas = CH (xarope, xaxim, xadrez, enxada)
  //   - X em palavras específicas = Z (exército, exemplo, exercício, existir)
  //   - X em palavras específicas = SS (México, maxXico, vexame)
  // Implementado como função auxiliar abaixo
  result = preprocessX(result)

  // ---- 2. NÚMEROS GRANDES POR EXTENSO ----
  // Anos: "2024" → "[dois mil vinte e quatro]" (quando precedido por "ano" ou similar)
  result = result.replace(/(?:ano|Ano|ANO)\s+(\d{4})/g, (match, year) => {
    const y = parseInt(year)
    if (y >= 1000 && y <= 2100) return match.replace(year, `[${numberToWords(y)}]`)
    return match
  })

  // Números isolados grandes (1.000, 2.500, etc. — com ponto de milhar PT-BR)
  result = result.replace(/\b(\d{1,3}(?:\.\d{3})+)\b/g, (match) => {
    const n = parseInt(match.replace(/\./g, ''))
    if (n <= 999999999) return `[${numberToWords(n)}]`
    return match
  })

  // Números decimais: "3,5" → "[três vírgula cinco]"
  result = result.replace(/\b(\d+),(\d+)\b/g, (match, intPart, decPart) => {
    const n = parseInt(intPart)
    if (n > 0 && n <= 999) {
      const intWord = numberToWords(n)
      const decDigits = decPart.split('').map(d => numberToWords(parseInt(d))).join(' ')
      return `[${intWord} vírgula ${decDigits}]`
    }
    return match
  })

  // Números isolados pequenos (1-999) em contexto textual
  result = result.replace(/\b(\d{1,3})\b/g, (match, numStr) => {
    const n = parseInt(numStr)
    // Só converte se estiver em contexto textual (não dentro de colchetes, URLs, etc.)
    if (n > 0 && n <= 999) {
      // Verifica se está dentro de colchetes (já processado)
      const before = result.substring(Math.max(0, result.indexOf(match) - 1), result.indexOf(match))
      if (before === '[') return match
      return `[${numberToWords(n)}]`
    }
    return match
  })

  // ---- 3. VALORES MONETÁRIOS ----
  // R$ com valor completo: "R$ 1.599,90" → "[mil quinhentos e noventa e nove reais e noventa centavos]"
  result = result.replace(/R\$\s*([\d.,]+)/g, (match, val) => {
    return `[${currencyToWords(val)}]`
  })

  // Dólar: "$ 100" ou "US$ 100"
  result = result.replace(/(?:US\$|\$)\s*([\d.,]+)/g, (match, val) => {
    const clean = val.replace(/\./g, '').replace(',', '.')
    const n = parseFloat(clean)
    if (isNaN(n)) return match
    return `[${n === 1 ? 'um dólar' : numberToWords(Math.floor(n)) + ' dólares'}]`
  })

  // ---- 4. PORCENTAGENS ----
  result = result.replace(/(\d+)(?:\s*%|\s*por cento)/gi, (match, numStr) => {
    const n = parseInt(numStr)
    if (isNaN(n)) return match
    return `[${numberToWords(n)} por cento]`
  })

  // ---- 5. HORÁRIOS COMPLETOS ----
  // "14h30" ou "14h30min" → "[quatorze] horas e [trinta] minutos"
  result = result.replace(/(\d{1,2})h(\d{2})(?:min)?/gi, (match, hourStr, minStr) => {
    const h = parseInt(hourStr)
    const m = parseInt(minStr)
    if (h < 0 || h > 23 || m < 0 || m > 59) return match
    let text = `[${numberToWords(h)}] horas`
    if (m > 0) text += ` e [${numberToWords(m)}] minutos`
    return text
  })

  // "14h" simples → "[quatorze] horas"
  result = result.replace(/(\d{1,2})\s*h(?!\w)/gi, (match, numStr) => {
    const n = parseInt(numStr)
    if (isNaN(n) || n < 0 || n > 23) return match
    return `[${numberToWords(n)}] horas`
  })

  // "08:30" como horário → "[oito] horas e [trinta] minutos"
  result = result.replace(/\b(\d{1,2}):(\d{2})\b/g, (match, hourStr, minStr) => {
    const h = parseInt(hourStr)
    const m = parseInt(minStr)
    // Verifica se é horário (0-23h, 0-59min) e não data
    if (h >= 0 && h <= 23 && m >= 0 && m <= 59 && m > 0) {
      return `[${numberToWords(h)}] horas e [${numberToWords(m)}] minutos`
    }
    return match
  })

  // ---- 6. DATAS ----
  // "15/03/2024" → "[quinze de março de dois mil vinte e quatro]"
  const MONTHS: Record<string, string> = {
    '01': 'janeiro', '02': 'fevereiro', '03': 'março', '04': 'abril',
    '05': 'maio', '06': 'junho', '07': 'julho', '08': 'agosto',
    '09': 'setembro', '10': 'outubro', '11': 'novembro', '12': 'dezembro',
  }
  result = result.replace(/\b(\d{1,2})\/(\d{2})\/(\d{4})\b/g, (match, day, month, year) => {
    const d = parseInt(day)
    const m = MONTHS[month]
    const y = parseInt(year)
    if (d >= 1 && d <= 31 && m && y >= 1000) {
      return `[${numberToWords(d)} de ${m} de ${numberToWords(y)}]`
    }
    return match
  })

  // Data curta: "15/03" → "[quinze de março]"
  result = result.replace(/\b(\d{1,2})\/(\d{2})\b/g, (match, day, month) => {
    const d = parseInt(day)
    const m = MONTHS[month]
    if (d >= 1 && d <= 31 && m) {
      return `[${numberToWords(d)} de ${m}]`
    }
    return match
  })

  // ---- 7. TELEFONES ----
  // "(11) 99999-9999" → "[onze] [nove nove nove nove nove] [nove nove nove nove]"
  result = result.replace(/\((\d{2})\)\s*(\d{4,5})-?(\d{4})/g, (match, ddd, prefix, suffix) => {
    const dddWord = `[${numberToWords(parseInt(ddd))}]`
    const prefixDigits = prefix.split('').map(d => numberToWords(parseInt(d))).join(' ')
    const suffixDigits = suffix.split('').map(d => numberToWords(parseInt(d))).join(' ')
    return `${dddWord} [${prefixDigits}] [${suffixDigits}]`
  })

  // "11 99999-9999" → mesma lógica
  result = result.replace(/\b(\d{2})\s*(\d{4,5})-?(\d{4})\b/g, (match, ddd, prefix, suffix) => {
    const dddN = parseInt(ddd)
    if (dddN >= 11 && dddN <= 99) {
      const dddWord = `[${numberToWords(dddN)}]`
      const prefixDigits = prefix.split('').map(d => numberToWords(parseInt(d))).join(' ')
      const suffixDigits = suffix.split('').map(d => numberToWords(parseInt(d))).join(' ')
      return `${dddWord} [${prefixDigits}] [${suffixDigits}]`
    }
    return match
  })

  // ---- 8. ORDINAIS ----
  // "1º" → "[primeiro]", "2ª" → "[segunda]", etc.
  const ORDINALS_MASC: Record<string, string> = {
    '1': 'primeiro', '2': 'segundo', '3': 'terceiro', '4': 'quarto',
    '5': 'quinto', '6': 'sexto', '7': 'sétimo', '8': 'oitavo',
    '9': 'nono', '10': 'décimo',
  }
  const ORDINALS_FEM: Record<string, string> = {
    '1': 'primeira', '2': 'segunda', '3': 'terceira', '4': 'quarta',
    '5': 'quinta', '6': 'sexta', '7': 'sétima', '8': 'oitava',
    '9': 'nona', '10': 'décima',
  }

  result = result.replace(/(\d+)º/g, (match, num) => {
    if (ORDINALS_MASC[num]) return `[${ORDINALS_MASC[num]}]`
    return `[${numberToWords(parseInt(num))}ésimo]`
  })

  result = result.replace(/(\d+)ª/g, (match, num) => {
    if (ORDINALS_FEM[num]) return `[${ORDINALS_FEM[num]}]`
    return `[${numberToWords(parseInt(num))}ésima]`
  })

  // ---- 9-10-11. DICIONÁRIO (abreviações + siglas + estrangeirismos + problemáticas) ----
  for (const [word, pronunciation] of Object.entries(PRONUNCIATION_DICTIONARY)) {
    // Usar word boundary para não substituir substrings
    const escaped = word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
    // Para siglas (todas maiúsculas sem ponto), usa boundary exato
    if (/^[A-Z0-9]{2,}$/.test(word)) {
      result = result.replace(new RegExp(`\\b${escaped}\\b`, 'g'), pronunciation)
    } else if (word.endsWith('.')) {
      // Abreviações com ponto — manter o boundary com ponto
      result = result.replace(new RegExp(escaped, 'g'), pronunciation)
    } else {
      // Palavras normais — word boundary
      result = result.replace(new RegExp(`\\b${escaped}\\b`, 'gi'), (match) => {
        // Preservar capitalização do primeiro caractere
        const pron = pronunciation
        if (match[0] === match[0].toUpperCase() && match.length > 1) {
          // Tudo maiúsculo ou primeira maiúscula
          return pron
        }
        return pron
      })
    }
  }

  // ---- 12. URLs ----
  result = result.replace(/(https?:\/\/)([^\s]+)/gi, (match, protocol, domain) => {
    const spelled = domain.split('').map(c => {
      if (c === '.') return ' ponto '
      if (c === '/') return ' barra '
      if (c === '-') return ' traço '
      if (c === '_') return ' underline '
      if (c === ':') return ' dois pontos '
      if (c === '@') return ' arroba '
      if (c === '~') return ' til '
      return c
    }).join('')
    return `[${protocol.replace('https', 'agá tê tê pê és').replace('http', 'agá tê tê pê').replace('://', ' dois pontos barra barra')} ${spelled}]`
  })

  result = result.replace(/www\.([^\s]+)/gi, (match, domain) => {
    const spelled = domain.split('').map(c => {
      if (c === '.') return ' ponto '
      if (c === '/') return ' barra '
      if (c === '-') return ' traço '
      return c
    }).join('')
    return `[dabliu dabliu dabliu ponto ${spelled}]`
  })

  // ---- 13. EMAILS ----
  result = result.replace(/(\S+)@(\S+\.\S+)/g, (match, user, domain) => {
    const domainSpelled = domain.split('').map(c => {
      if (c === '.') return ' ponto '
      return c
    }).join('')
    return `[${user} arroba ${domainSpelled}]`
  })

  // ---- 14. LIMPEZA FINAL ----
  // Remover colchetes duplos: "[[texto]]" → "[texto]"
  result = result.replace(/\[\[([^\]]+)\]\]/g, '[$1]')

  // Remover colchetes ao redor de colchetes: "[[]]" → "[]"
  result = result.replace(/\[\[/g, '[')
  result = result.replace(/\]\]/g, ']')

  // Espaços múltiplos
  result = result.replace(/  +/g, ' ')

  return result
}

// ============================================================
// LLM FALLBACK (opcional, para termos não cobertos pelo regex)
// ============================================================

const LLM_SYSTEM_PROMPT = `Você é um agente especialista em otimização de pronúncia para TTS (text-to-speech) em português brasileiro.

Seu trabalho: analisar o texto e corrigir APENAS as palavras que o TTS pode pronunciar errado, usando colchetes [pronúncia correta].

## REGRAS OBRIGATÓRIAS:

1. **Nomes próprios incomuns**: Adicione pronúncia guia se necessário.
   - "Wolski" → "[Volski]"
   - "Xangai" → "[Xangai]"
   
2. **Termos técnicos/especializados** que o regex não cobriu:
   - Termos médicos, jurídicos, científicos
   - Nomes de medicamentos
   - Termos em outros idiomas não comuns

3. **Siglas e acrônimos não cobertos**:
   - Soletrar: "NASA" → "[êne á és é]"

## REGRAS DE NÃO INTERFERÊNCIA:

- NÃO altere palavras que já estão entre colchetes [ ] (já foram processadas)
- NÃO adicione vírgulas ou pontuação que não existia
- NÃO altere a estrutura das frases
- NÃO traduza palavras — apenas corrija pronúncia
- NÃO resuma ou encurte o texto de NENHUMA forma
- Mantenha TODOS os pontos finais, vírgulas, exclamações e interrogações EXATAMENTE onde estão

## FORMATO DE SAÍDA:
Responda APENAS com o texto corrigido. Nenhuma explicação.`

/**
 * Verifica se o texto provavelmente precisa de processamento LLM
 * (termos que o regex não consegue cobrir)
 */
export function needsLLMProcessing(text: string): boolean {
  // Se tem muitas palavras entre colchetes, o regex já trabalhou — skip
  const bracketCount = (text.match(/\[/g) || []).length
  const wordCount = text.split(/\s+/).length
  if (wordCount === 0) return false

  // Se mais de 50% das palavras já têm colchetes, provavelmente tá bom
  if (bracketCount / wordCount > 0.5) return false

  // Verificar indicadores de termos que regex não cobre
  const hasProperNouns = /[A-Z][a-z]{3,}[A-Z]/.test(text) // CamelCase
  const hasUnusualChars = /[àáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ]/i.test(text) === false && /[^\w\s.,;:!?()[\]{}@#$%&*+=\-\/\\]/.test(text)
  const hasLongUppercase = /\b[A-Z]{4,}\b/.test(text) // Siglas longas

  return hasProperNouns || hasUnusualChars || hasLongUppercase
}

export { LLM_SYSTEM_PROMPT }
