'use client'

import { useState, useEffect, Suspense } from 'react'
import { useRouter, useSearchParams } from 'next/navigation'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Lock, AudioWaveform, Loader2, Mail, Eye, EyeOff, ArrowRight, Shield, Settings2 } from 'lucide-react'
import { toast } from 'sonner'
import Image from 'next/image'

function AdminLoginForm() {
  const router = useRouter()
  const searchParams = useSearchParams()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [mounted, setMounted] = useState(false)

  useEffect(() => {
    setMounted(true)
  }, [])

  // Verificar se já está logado
  useEffect(() => {
    fetch('/api/auth/verify').then(res => res.json()).then(data => {
      if (data.authenticated) {
        router.push('/admin')
      }
    }).catch(() => {})
  }, [router])

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!email.trim() || !password.trim()) {
      toast.error('Preencha email e senha')
      return
    }

    setLoading(true)
    try {
      const res = await fetch('/api/auth', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      })

      const data = await res.json()

      if (res.ok && data.success) {
        toast.success('Login realizado!')
        const from = searchParams.get('from') || '/admin'
        router.push(from)
      } else {
        toast.error(data.error || 'Email ou senha incorretos')
      }
    } catch {
      toast.error('Erro de conexão')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex flex-col lg:flex-row">
      {/* ====== LADO ESQUERDO - Branding Admin ====== */}
      <div className="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-zinc-900">
        {/* Background */}
        <div className="absolute inset-0">
          <div className="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-500/10 blur-3xl animate-pulse" />
          <div className="absolute bottom-[-15%] left-[-5%] w-[400px] h-[400px] rounded-full bg-cyan-500/10 blur-3xl animate-pulse" style={{ animationDelay: '1s' }} />
          <div
            className="absolute inset-0 opacity-[0.03]"
            style={{
              backgroundImage: `linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px)`,
              backgroundSize: '50px 50px',
            }}
          />
        </div>

        {/* Content */}
        <div className="relative z-10 flex flex-col justify-center px-12 xl:px-20">
          <div
            className={`transition-all duration-1000 ease-out ${
              mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
            }`}
          >
            {/* Logo */}
            <div className="flex items-center gap-4 mb-10">
              <div className="w-16 h-16 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 flex items-center justify-center">
                <Image src="/logo.svg" alt="VozPro" width={40} height={40} />
              </div>
              <div>
                <h1 className="text-3xl font-bold text-white tracking-tight">VozPro</h1>
                <div className="flex items-center gap-1.5 mt-0.5">
                  <Shield className="w-3.5 h-3.5 text-emerald-400" />
                  <p className="text-emerald-400 text-sm font-medium">Painel Administrativo</p>
                </div>
              </div>
            </div>

            <div className="max-w-lg">
              <h2 className="text-4xl xl:text-5xl font-bold text-white leading-tight mb-6">
                Gerencie suas
                <span className="block text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-cyan-300">
                  vozes e configurações
                </span>
              </h2>
              <p className="text-lg text-slate-400 leading-relaxed mb-12">
                Acesse o painel de administração para gerenciar vozes, trilhas, usuários e configurações do sistema.
              </p>
            </div>

            {/* Feature cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
              {[
                { icon: AudioWaveform, title: 'Gestão de Vozes', desc: 'Cadastre e organize vozes' },
                { icon: Settings2, title: 'Configurações', desc: 'Personalize o sistema' },
                { icon: Shield, title: 'Usuários', desc: 'Controle de acesso' },
                { icon: Lock, title: 'Segurança', desc: 'Dados protegidos' },
              ].map((feature, i) => (
                <div
                  key={i}
                  className={`flex items-start gap-3 p-4 rounded-xl bg-white/[0.03] backdrop-blur-sm border border-white/[0.06] transition-all duration-700 ${
                    mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'
                  }`}
                  style={{ transitionDelay: `${300 + i * 100}ms` }}
                >
                  <div className="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                    <feature.icon className="w-4.5 h-4.5 text-emerald-400" />
                  </div>
                  <div>
                    <p className="text-sm font-semibold text-white">{feature.title}</p>
                    <p className="text-xs text-slate-500">{feature.desc}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* ====== LADO DIREITO - Login Form ====== */}
      <div className="flex-1 flex items-center justify-center bg-slate-950 px-6 py-12 lg:px-12 xl:px-20 relative">
        <div className="absolute inset-0 lg:hidden">
          <div className="absolute top-[-20%] right-[-20%] w-[400px] h-[400px] rounded-full bg-emerald-600/10 blur-3xl" />
        </div>

        <div className={`w-full max-w-md relative z-10 transition-all duration-700 ease-out ${
          mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'
        }`}>
          {/* Mobile logo */}
          <div className="lg:hidden flex items-center gap-3 mb-10">
            <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
              <Image src="/logo.svg" alt="VozPro" width={28} height={28} />
            </div>
            <div>
              <h1 className="text-xl font-bold text-white">VozPro Admin</h1>
              <p className="text-slate-500 text-xs">Painel Administrativo</p>
            </div>
          </div>

          {/* Header */}
          <div className="mb-8">
            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-4">
              <Shield className="w-3.5 h-3.5 text-emerald-400" />
              <span className="text-xs font-medium text-emerald-400">Acesso restrito</span>
            </div>
            <h2 className="text-2xl font-bold text-white mb-2">Login Admin</h2>
            <p className="text-slate-400">
              Apenas administradores podem acessar este painel
            </p>
          </div>

          {/* Form */}
          <form onSubmit={handleLogin} className="space-y-5">
            <div className="space-y-2">
              <Label htmlFor="email" className="text-sm font-medium text-slate-300">Email</Label>
              <div className="relative group">
                <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500 group-focus-within:text-emerald-400 transition-colors" />
                <Input
                  id="email"
                  type="email"
                  placeholder="admin@email.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="pl-11 h-12 bg-slate-900/80 border-slate-800 text-white placeholder:text-slate-600 rounded-xl focus:border-emerald-500/50 focus:ring-emerald-500/20 transition-all text-[15px]"
                  autoFocus
                  autoComplete="email"
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="password" className="text-sm font-medium text-slate-300">Senha</Label>
              <div className="relative group">
                <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500 group-focus-within:text-emerald-400 transition-colors" />
                <Input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  placeholder="Senha de administrador"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="pl-11 pr-11 h-12 bg-slate-900/80 border-slate-800 text-white placeholder:text-slate-600 rounded-xl focus:border-emerald-500/50 focus:ring-emerald-500/20 transition-all text-[15px]"
                  autoComplete="current-password"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
                >
                  {showPassword ? <EyeOff className="w-4.5 h-4.5" /> : <Eye className="w-4.5 h-4.5" />}
                </button>
              </div>
            </div>

            <Button
              type="submit"
              disabled={loading}
              className="w-full h-12 bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all duration-300 text-[15px] mt-2"
            >
              {loading ? (
                <>
                  <Loader2 className="w-4.5 h-4.5 mr-2 animate-spin" />
                  Verificando...
                </>
              ) : (
                <>
                  Acessar Painel
                  <ArrowRight className="w-4.5 h-4.5 ml-2" />
                </>
              )}
            </Button>
          </form>

          <div className="mt-10 pt-6 border-t border-slate-800/50">
            <p className="text-center text-xs text-slate-600">
              OmniVoice Admin &copy; {new Date().getFullYear()} — Acesso autorizado apenas
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default function AdminLoginPage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen flex items-center justify-center bg-slate-950">
        <div className="text-slate-500">Carregando...</div>
      </div>
    }>
      <AdminLoginForm />
    </Suspense>
  )
}
