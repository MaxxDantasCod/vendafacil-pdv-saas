<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>VendaFácil PDV — O PDV completo que sua loja precisa</title>
<meta name="description" content="PDV online completo para varejo brasileiro. Venda mais rápido, controle estoque por grade, emita NFC-e e SAT, receba PIX e gerencie o financeiro.">
<style>
:root{
  --bg:#0a0f1c;
  --bg-elev:#111827;
  --card:rgba(17,24,39,.7);
  --border:rgba(255,255,255,.08);
  --border-2:rgba(255,255,255,.14);
  --text:#e5e7eb;
  --muted:#9ca3af;
  --muted-2:#6b7280;
  --emerald:#10b981;
  --emerald-2:#059669;
  --emerald-glow:rgba(16,185,129,.25);
  --blue:#3b82f6;
  --blue-glow:rgba(59,130,246,.25);
  --radius:16px;
  --shadow:0 10px 40px rgba(0,0,0,.45);
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Helvetica Neue",Arial,"Noto Sans",sans-serif;
  background:var(--bg);
  color:var(--text);
  line-height:1.6;
  -webkit-font-smoothing:antialiased;
  text-rendering:optimizeLegibility;
}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}
.container{max-width:1120px;margin:0 auto;padding:0 24px}

/* Background */
.bg{
  position:fixed;inset:0;z-index:-2;overflow:hidden;
}
.grid-bg{
  position:absolute;inset:-1px;
  background-image:
    linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);
  background-size:72px 72px;
  mask-image:radial-gradient(ellipse at 50% 0%, black 30%, transparent 70%);
}
.glow{
  position:absolute;filter:blur(80px);opacity:.7;pointer-events:none;
}
.glow-1{width:560px;height:560px;left:-120px;top:-180px;background:radial-gradient(circle at center,rgba(16,185,129,.35),transparent 60%)}
.glow-2{width:520px;height:520px;right:-140px;top:10%;background:radial-gradient(circle at center,rgba(59,130,246,.3),transparent 60%)}
.glow-3{width:600px;height:600px;left:20%;bottom:-300px;background:radial-gradient(circle at center,rgba(16,185,129,.18),transparent 60%)}

/* Header */
.header{
  position:sticky;top:0;z-index:40;
  backdrop-filter:saturate(140%) blur(14px);
  background:rgba(10,15,28,.7);
  border-bottom:1px solid var(--border);
}
.nav{
  display:flex;align-items:center;gap:28px;height:72px;
}
.logo{
  display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:-.02em;font-size:18px;
}
.logo-icon{
  width:32px;height:32px;border-radius:10px;
  background:linear-gradient(135deg,var(--emerald),var(--blue));
  display:grid;place-items:center;
  box-shadow:0 0 24px var(--emerald-glow), inset 0 1px 0 rgba(255,255,255,.15);
}
.nav-links{display:flex;gap:26px;margin-left:8px}
.nav-links a{color:var(--muted);font-size:14.5px;font-weight:500;transition:.2s}
.nav-links a:hover{color:var(--text)}
.nav-actions{display:flex;gap:10px;margin-left:auto;align-items:center}
.btn{ display:inline-flex;align-items:center;justify-content:center;gap:8px; font-weight:600; font-size:14.5px; padding:10px 16px; border-radius:12px; border:1px solid transparent; transition:.2s; white-space:nowrap; cursor:pointer }
.btn-ghost{color:#d1d5db;background:transparent;border-color:transparent}
.btn-ghost:hover{background:rgba(255,255,255,.06);color:#fff}
.btn-primary{
  background:var(--emerald); color:#052e24; border-color:rgba(255,255,255,.08);
  box-shadow:0 6px 20px var(--emerald-glow), inset 0 1px 0 rgba(255,255,255,.2);
}
.btn-primary:hover{background:var(--emerald-2); transform:translateY(-1px); box-shadow:0 10px 28px var(--emerald-glow)}
.btn-secondary{
  background:rgba(255,255,255,.06); color:#fff; border-color:var(--border);
  backdrop-filter:blur(8px);
}
.btn-secondary:hover{background:rgba(255,255,255,.1); border-color:var(--border-2)}
.btn-lg{padding:13px 20px;font-size:15.5px;border-radius:14px}

.menu-toggle{display:none;width:40px;height:40px;border-radius:10px;border:1px solid var(--border);background:rgba(255,255,255,.04);place-items:center;cursor:pointer}
.menu-toggle:hover{background:rgba(255,255,255,.08)}
.mobile{display:none}

/* Hero */
.hero{padding:88px 0 40px;position:relative;text-align:center}
.badge{
  display:inline-flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;
  background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); color:#86efac;
  font-size:12.5px;font-weight:600; letter-spacing:.01em; margin-bottom:20px;
}
.badge-dot{width:6px;height:6px;border-radius:50%;background:#10b981;box-shadow:0 0 10px #10b981}
h1{
  font-size:clamp(38px,6vw,66px); line-height:1.02; letter-spacing:-.03em; font-weight:900;
  margin:0 0 18px;
  background:linear-gradient(180deg,#fff 0%, #c7d2e0 100%);
  -webkit-background-clip:text;background-clip:text;color:transparent;
}
.hero p{
  max-width:740px;margin:0 auto 32px;color:var(--muted);font-size:18px;
}
.hero-cta{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-bottom:56px}

/* Mockup */
.mockup-wrap{max-width:1040px;margin:0 auto;position:relative}
.mockup{
  background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,.02));
  border:1px solid var(--border-2); border-radius:24px; padding:14px;
  box-shadow:var(--shadow), inset 0 1px 0 rgba(255,255,255,.06);
  backdrop-filter:blur(10px);
}
.mockup-top{
  display:flex;align-items:center;gap:12px;padding:8px 10px 14px;border-bottom:1px solid var(--border);
}
.dots{display:flex;gap:6px}
.dots span{width:10px;height:10px;border-radius:50%;background:rgba(255,255,255,.18)}
.mockup-title{margin-left:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11.5px;color:var(--muted-2)}
.mockup-body{
  display:grid;grid-template-columns:1.45fr .95fr;gap:14px;
  background:#0b1220;border-radius:16px;margin-top:10px;padding:14px;min-height:380px;
  border:1px solid rgba(255,255,255,.04)
}
.panel{
  background:var(--bg-elev); border:1px solid var(--border); border-radius:14px; padding:14px;
}
.search{
  display:flex;align-items:center;gap:8px;background:#0a0f1c;border:1px solid var(--border);
  border-radius:10px;padding:10px 12px;color:var(--muted-2);font-size:13px;margin-bottom:12px
}
.products{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.prod{
  background:#0c1322;border:1px solid var(--border);border-radius:12px;padding:10px;
  transition:.2s; cursor:default;
}
.prod:hover{transform:translateY(-2px);border-color:rgba(16,185,129,.35);box-shadow:0 6px 18px rgba(0,0,0,.3)}
.prod-img{height:54px;border-radius:8px;background:linear-gradient(135deg,rgba(59,130,246,.35),rgba(16,185,129,.35));margin-bottom:8px}
.prod-name{font-size:12px;color:#d1d5db;font-weight:600}
.prod-price{font-size:11px;color:var(--muted)}
.cart-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.cart-tag{font-size:11px;color:#86efac;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);padding:4px 8px;border-radius:999px}
.items{display:flex;flex-direction:column;gap:8px;margin-bottom:12px}
.item{display:flex;align-items:center;justify-content:space-between;background:#0a0f1c;border:1px solid var(--border);border-radius:10px;padding:8px 10px;font-size:12.5px}
.item small{color:var(--muted)}
.total{display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,rgba(16,185,129,.18),rgba(16,185,129,.06));border:1px solid rgba(16,185,129,.3);border-radius:12px;padding:12px 14px;margin:12px 0 10px}
.total strong{font-size:20px;letter-spacing:-.01em}
.pay{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.pay-btn{padding:10px;border-radius:10px;font-weight:700;font-size:13px;border:1px solid var(--border);background:rgba(255,255,255,.04);color:#fff;cursor:default}
.pay-btn.pix{background:rgba(16,185,129,.15);border-color:rgba(16,185,129,.35);color:#a7f3d0}

/* Sections */
.section{padding:88px 0}
.section-head{text-align:center;max-width:760px;margin:0 auto 44px}
.eyebrow{color:#86efac;font-weight:700;font-size:13px;letter-spacing:.12em;text-transform:uppercase;margin-bottom:10px}
.h2{font-size:clamp(28px,4vw,42px);line-height:1.1;letter-spacing:-.02em;margin:0 0 12px;font-weight:800}
.sub{color:var(--muted);font-size:17px}

.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.card{
  background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:22px;
  backdrop-filter:blur(8px); transition:.25s; position:relative; overflow:hidden;
}
.card:hover{transform:translateY(-3px);border-color:var(--border-2);box-shadow:0 12px 30px rgba(0,0,0,.35)}
.card:before{
  content:"";position:absolute;inset:-1px;background:radial-gradient(400px circle at var(--x,50%) var(--y,50%), rgba(16,185,129,.15), transparent 40%);opacity:0;transition:.3s;pointer-events:none
}
.card:hover:before{opacity:1}
.icon{
  width:40px;height:40px;border-radius:12px;display:grid;place-items:center;margin-bottom:14px;
  background:linear-gradient(135deg,rgba(16,185,129,.2),rgba(59,130,246,.2));
  border:1px solid rgba(255,255,255,.08); color:#a7f3d0;
}
.card h3{margin:0 0 6px;font-size:17px;letter-spacing:-.01em}
.card p{margin:0;color:var(--muted);font-size:14.5px}

/* Segments */
.segments{padding:40px 0 20px}
.pills{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-top:18px}
.pill{
  padding:10px 14px;border-radius:999px;background:rgba(255,255,255,.04);border:1px solid var(--border);
  color:#d1d5db;font-size:14px;font-weight:500;transition:.2s
}
.pill:hover{background:rgba(255,255,255,.08);border-color:var(--border-2);transform:translateY(-1px)}

/* Pricing */
.pricing{padding-top:72px}
.pricing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;align-items:stretch;margin-top:34px}
.price-card{
  background:var(--card);border:1px solid var(--border);border-radius:20px;padding:26px;
  backdrop-filter:blur(8px);display:flex;flex-direction:column;position:relative
}
.price-card.featured{
  border-color:rgba(16,185,129,.5);box-shadow:0 0 0 1px rgba(16,185,129,.2) inset, 0 20px 50px rgba(16,185,129,.15);
  transform:translateY(-4px)
}
.price-badge{
  position:absolute;top:14px;right:14px;font-size:11px;font-weight:700;color:#052e24;
  background:#10b981;padding:5px 9px;border-radius:999px;letter-spacing:.02em
}
.price-name{font-weight:700;font-size:16px;margin-bottom:6px}
.price-value{font-size:38px;font-weight:900;letter-spacing:-.02em;margin:6px 0}
.price-value small{font-size:14px;color:var(--muted);font-weight:600}
.price-desc{color:var(--muted);font-size:14px;margin-bottom:18px;min-height:40px}
.features{list-style:none;margin:0 0 22px;padding:0;display:flex;flex-direction:column;gap:10px}
.features li{display:flex;gap:9px;align-items:flex-start;font-size:14px;color:#d1d5db}
.check{width:18px;height:18px;border-radius:50%;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.35);display:grid;place-items:center;flex:0 0 18px;margin-top:2px;color:#86efac}
.price-cta{margin-top:auto}

/* Final CTA */
.final{
  margin:80px 0 0;position:relative;overflow:hidden;
  border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  background:
    radial-gradient(600px 200px at 50% -20%, rgba(16,185,129,.25), transparent 70%),
    linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,0));
}
.final-inner{text-align:center;padding:72px 24px}
.final h3{font-size:clamp(28px,4vw,40px);letter-spacing:-.02em;margin:0 0 10px;font-weight:900}
.final p{color:var(--muted);margin:0 0 24px;font-size:17px}

/* Footer */
footer{padding:56px 0 40px;color:#9aa3b2}
.foot-grid{display:grid;grid-template-columns:1.2fr .8fr .8fr .8fr;gap:28px;margin-bottom:36px}
.foot-brand .logo{margin-bottom:12px}
.foot-desc{font-size:14px;color:var(--muted);max-width:320px}
.foot-title{font-weight:700;color:#e5e7eb;margin-bottom:12px;font-size:14px}
.foot-links{display:flex;flex-direction:column;gap:9px}
.foot-links a{color:#9aa3b2;font-size:14px}
.foot-links a:hover{color:#e5e7eb}
.foot-bottom{display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:18px;border-top:1px solid var(--border);font-size:13px;color:#7d8596}
.badges{display:flex;gap:8px;flex-wrap:wrap}
.badge-mini{padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.04);border:1px solid var(--border);font-size:12px}

/* Responsive */
@media (max-width:1024px){
  .mockup-body{grid-template-columns:1fr}
  .products{grid-template-columns:repeat(4,1fr)}
}
@media (max-width:900px){
  .grid-3{grid-template-columns:repeat(2,1fr)}
  .pricing-grid{grid-template-columns:1fr;max-width:520px;margin-left:auto;margin-right:auto}
  .price-card.featured{transform:none}
  .foot-grid{grid-template-columns:1fr 1fr}
}
@media (max-width:768px){
  .nav-links{display:none}
  .nav-actions.desktop{display:none}
  .menu-toggle{display:grid;margin-left:auto}
  .mobile{display:block;position:absolute;inset:72px 0 auto 0;background:rgba(10,15,28,.98);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:16px 24px;transform-origin:top;transform:scaleY(0);opacity:0;pointer-events:none;transition:.2s}
  .mobile.open{transform:scaleY(1);opacity:1;pointer-events:auto}
  .mobile a{display:block;padding:12px 0;color:#d1d5db;border-bottom:1px solid rgba(255,255,255,.06)}
  .mobile .btns{display:flex;gap:10px;margin-top:12px}
  .hero{padding-top:64px}
  .products{grid-template-columns:repeat(2,1fr)}
}
@media (max-width:640px){
  .container{padding:0 18px}
  .grid-3{grid-template-columns:1fr}
  .hero p{font-size:16px}
  .mockup{padding:10px;border-radius:18px}
  .mockup-body{padding:10px}
  .foot-grid{grid-template-columns:1fr}
  .foot-bottom{flex-direction:column;align-items:flex-start}
}
</style>
</head>
<body>
<div class="bg">
  <div class="grid-bg"></div>
  <div class="glow glow-1"></div>
  <div class="glow glow-2"></div>
  <div class="glow glow-3"></div>
</div>

<header class="header">
  <div class="container nav">
    <a class="logo" href="/">
      <span class="logo-icon" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#052e24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9h18M7 3v6M17 3v6M5 21h14a2 2 0 0 0 2-2V9H3v10a2 2 0 0 0 2 2Z"/>
        </svg>
      </span>
      VendaFácil
    </a>
    <nav class="nav-links">
      <a href="#recursos">Recursos</a>
      <a href="#planos">Planos</a>
      <a href="#segmentos">Segmentos</a>
    </nav>
    <div class="nav-actions desktop">
      <a href="{{ route('login') }}" class="btn btn-ghost">Entrar</a>
      <a href="{{ route('register') }}" class="btn btn-primary">Criar conta grátis</a>
    </div>
    <button class="menu-toggle" id="menuBtn" aria-label="Abrir menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>
  <div class="mobile" id="mobileNav">
    <a href="#recursos">Recursos</a>
    <a href="#planos">Planos</a>
    <a href="#segmentos">Segmentos</a>
    <div class="btns">
      <a href="{{ route('login') }}" class="btn btn-secondary" style="flex:1">Entrar</a>
      <a href="{{ route('register') }}" class="btn btn-primary" style="flex:1">Criar conta</a>
    </div>
  </div>
</header>

<main>
  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <div class="badge"><span class="badge-dot"></span> Novo: Emissão NFC-e automática</div>
      <h1>O PDV completo que sua loja precisa</h1>
      <p>Venda mais rápido, controle estoque, emita NFC-e e gerencie o financeiro em um só lugar. 100% online, sem instalação.</p>
      <div class="hero-cta">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Começar grátis por 30 dias</a>
        <a href="#demo" class="btn btn-secondary btn-lg">Ver demonstração</a>
      </div>

      <div class="mockup-wrap" id="demo" aria-label="Prévia do PDV">
        <div class="mockup">
          <div class="mockup-top">
            <div class="dots"><span></span><span></span></div>
            <div class="mockup-title">vendafacil.app • Caixa 01</div>
          </div>
          <div class="mockup-body">
            <div class="panel">
              <div class="search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4-4"/></svg>
                Buscar produto, código de barras ou SKU...
              </div>
              <div class="products">
                <div class="prod"><div class="prod-img"></div><div class="prod-name">Camiseta Básica</div><div class="prod-price">R$ 39,90</div></div>
                <div class="prod"><div class="prod-img"></div><div class="prod-name">Tênis Casual</div><div class="prod-price">R$ 199,90</div></div>
                <div class="prod"><div class="prod-img"></div><div class="prod-name">Boné Snapback</div><div class="prod-price">R$ 59,90</div></div>
                <div class="prod"><div class="prod-img"></div><div class="prod-name">Meia (3 pares)</div><div class="prod-price">R$ 29,90</div></div>
                <div class="prod"><div class="prod-img"></div><div class="prod-name">Mochila</div><div class="prod-price">R$ 129,90</div></div>
                <div class="prod"><div class="prod-img"></div><div class="prod-name">Garrafa Térmica</div><div class="prod-price">R$ 79,90</div></div>
              </div>
            </div>
            <div class="panel">
              <div class="cart-head">
                <strong style="font-size:14px">Venda #1247</strong>
                <span class="cart-tag">Aberta</span>
              </div>
              <div class="items">
                <div class="item"><span>Camiseta Básica <small>x2</small></span><strong>R$ 79,80</strong></div>
                <div class="item"><span>Boné Snapback <small>x1</small></span><strong>R$ 59,90</strong></div>
                <div class="item"><span>Meia (3 pares) <small>x1</small></span><strong>R$ 29,90</strong></div>
              </div>
              <div class="total"><span style="color:#a7f3d0;font-size:13px">Total</span><strong>R$ 169,60</strong></div>
              <div class="pay">
                <button class="pay-btn pix">PIX</button>
                <button class="pay-btn">Cartão</button>
                <button class="pay-btn">Dinheiro</button>
                <button class="pay-btn">Crediário</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="section" id="recursos">
    <div class="container">
      <div class="section-head">
        <div class="eyebrow">Tudo que você precisa</div>
        <h2 class="h2">Venda mais rápido. Controle de verdade.</h2>
        <p class="sub">Feito para o varejo brasileiro, com NFC-e, PIX e estoque por grade.</p>
      </div>

      <div class="grid-3">
        <article class="card">
          <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2M4 7h16M4 7v10a2 2 0 0 0 2 2h3m7-12v0M9 15h4m-4 4h8M9 21h6"/></svg>
          </div>
          <h3>PDV Frente de Caixa</h3>
          <p>Venda em segundos com busca inteligente, leitor de código de barras e atalhos. Funciona no computador, tablet ou celular.</p>
        </article>

        <article class="card">
          <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
          </div>
          <h3>Controle de Estoque por Grade</h3>
          <p>Gerencie tamanho, cor e variações. Baixa automática, alerta de estoque mínimo, inventário e transferência entre lojas.</p>
        </article>

        <article class="card">
          <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/><path d="m9 15 2 2 4-4"/></svg>
          </div>
          <h3>Emissão NFC-e e SAT</h3>
          <p>Emita NFC-e direto no PDV com contingência automática. Suporte a SAT Fiscal para São Paulo. Integração com SEFAZ.</p>
        </article>

        <article class="card">
          <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 16l3-3 4 4 5-7"/></svg>
          </div>
          <h3>Financeiro Completo</h3>
          <p>Contas a pagar e receber, fluxo de caixa, DRE, conciliação bancária e centros de custo. Veja seu lucro real.</p>
        </article>

        <article class="card">
          <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
          </div>
          <h3>Multi-forma de Pagamento (PIX)</h3>
          <p>PIX QR dinâmico, cartão, dinheiro, crediário e múltiplos pagamentos na mesma venda. Troco automático e comprovante.</p>
        </article>

        <article class="card">
          <div class="icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20V10M18 20V4M6 20v-6"/></svg>
          </div>
          <h3>Dashboard em tempo real</h3>
          <p>Acompanhe vendas, ticket médio e produtos mais vendidos ao vivo. Metas por vendedor e relatórios por período.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- SEGMENTS -->
  <section class="segments" id="segmentos">
    <div class="container" style="text-align:center">
      <h3 class="h2" style="font-size:28px">Para todo tipo de varejo</h3>
      <div class="pills">
        <span class="pill">Mini Mercado</span>
        <span class="pill">Pet Shop</span>
        <span class="pill">Moda e Vestuário</span>
        <span class="pill">Papelaria</span>
        <span class="pill">Materiais de Construção</span>
        <span class="pill">Cosméticos</span>
        <span class="pill">Acessórios</span>
        <span class="pill">Conveniência</span>
      </div>
    </div>
  </section>

  <!-- PRICING -->
  <section class="section pricing" id="planos">
    <div class="container">
      <div class="section-head">
        <div class="eyebrow">Planos simples</div>
        <h2 class="h2">Comece grátis. Evolua quando vender mais.</h2>
        <p class="sub">Sem taxa de implantação. Cancele quando quiser. Suporte em português.</p>
      </div>

      <div class="pricing-grid">
        <div class="price-card">
          <div class="price-name">Gratuito</div>
          <div class="price-value">R$0 <small>/mês</small></div>
          <p class="price-desc">Para testar e vender pouco.</p>
          <ul class="features">
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Até 30 vendas/mês</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> 1 usuário</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> PDV básico</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Suporte por e-mail</li>
          </ul>
          <div class="price-cta"><a href="{{ route('register') }}" class="btn btn-secondary" style="width:100%">Começar grátis</a></div>
        </div>

        <div class="price-card featured">
          <span class="price-badge">Mais popular</span>
          <div class="price-name">Pro</div>
          <div class="price-value">R$49 <small>/mês</small></div>
          <p class="price-desc">Para lojas que vendem todo dia.</p>
          <ul class="features">
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Vendas ilimitadas</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> 3 usuários</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Estoque por grade</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> NFC-e ilimitada</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Financeiro completo</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> PIX integrado</li>
          </ul>
          <div class="price-cta"><a href="{{ route('register') }}" class="btn btn-primary" style="width:100%">Assinar Pro</a></div>
        </div>

        <div class="price-card">
          <div class="price-name">Enterprise</div>
          <div class="price-value">R$99 <small>/mês</small></div>
          <p class="price-desc">Para redes e operação avançada.</p>
          <ul class="features">
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Tudo do Pro</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Usuários ilimitados</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Multi-loja</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> SAT Fiscal</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> API e integrações</li>
            <li><span class="check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span> Suporte WhatsApp prioritário</li>
          </ul>
          <div class="price-cta"><a href="{{ route('register') }}" class="btn btn-secondary" style="width:100%">Falar com vendas</a></div>
        </div>
      </div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section class="final">
    <div class="final-inner container">
      <h3>Pronto para vender mais rápido?</h3>
      <p>Crie sua conta em 2 minutos. Sem cartão. Cancele quando quiser.</p>
      <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Criar conta grátis</a>
    </div>
  </section>
</main>

<footer>
  <div class="container">
    <div class="foot-grid">
      <div class="foot-brand">
        <div class="logo">
          <span class="logo-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#052e24" stroke-width="2.5" stroke-linecap="round"><path d="M3 9h18M7 3v6M17 3v6M5 21h14a2 2 0 0 0 2-2V9H3v10a2 2 0 0 0 2 2Z"/></svg></span>
          VendaFácil
        </div>
        <p class="foot-desc">PDV online completo para o varejo brasileiro. Rápido, estável e com NFC-e.</p>
      </div>
      <div>
        <div class="foot-title">Produto</div>
        <div class="foot-links">
          <a href="#recursos">Recursos</a>
          <a href="#planos">Planos</a>
          <a href="#demo">Demonstração</a>
          <a href="{{ route('login') }}">Entrar</a>
        </div>
      </div>
      <div>
        <div class="foot-title">Empresa</div>
        <div class="foot-links">
          <a href="#">Sobre</a>
          <a href="#">Blog</a>
          <a href="#">Carreiras</a>
          <a href="#">Contato</a>
        </div>
      </div>
      <div>
        <div class="foot-title">Legal</div>
        <div class="foot-links">
          <a href="#">Termos de Uso</a>
          <a href="#">Privacidade</a>
          <a href="#">LGPD</a>
          <a href="#">Segurança</a>
        </div>
      </div>
    </div>
    <div class="foot-bottom">
      <div>© 2025 VendaFácil PDV. Todos os direitos reservados. Feito no Brasil.</div>
      <div class="badges">
        <span class="badge-mini">100% Online</span>
        <span class="badge-mini">NFC-e</span>
        <span class="badge-mini">PIX</span>
        <span class="badge-mini">LGPD</span>
      </div>
    </div>
  </div>
</footer>

<script>
  // Mobile menu toggle
  const btn = document.getElementById('menuBtn');
  const nav = document.getElementById('mobileNav');
  btn && btn.addEventListener('click', () => nav.classList.toggle('open'));

  // Subtle card hover light effect
  document.querySelectorAll('.card').forEach(card=>{
    card.addEventListener('pointermove', e=>{
      const r = card.getBoundingClientRect();
      card.style.setProperty('--x', (e.clientX - r.left) + 'px');
      card.style.setProperty('--y', (e.clientY - r.top) + 'px');
    });
  });
</script>
</body>
</html>