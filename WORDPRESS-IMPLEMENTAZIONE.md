# Studioamore.it - Guida rapida implementazione Home in WordPress + Astra + Elementor

Questa consegna include una homepage completa (testi + struttura + asset grafici originali) pronta da replicare in Elementor.

## File inclusi
- `index.html` (anteprima completa della home)
- `styles.css` (stili)
- `assets/logo-studioamore.svg`
- `assets/hero-desktop-1440x520.svg`
- `assets/hero-mobile-1000x800.svg`
- `assets/banner-promozioni-1200x800.svg`
- `assets/banner-ricarica-1200x800.svg`

## Setup consigliato su WordPress
1. Carica tutti i file in **Media Library** (SVG solo se consentito dal sito; in alternativa converti in PNG/WebP).
2. Crea la pagina **Home** con Elementor (layout Full Width).
3. Ricrea le sezioni nell'ordine:
   1) Logo
   2) Hero desktop/mobile
   3) Slogan
   4) Blocco "Prima volta 2€ 10 minuti"
   5) Blocco "Ricarica Web"
   6) Widget HTML con iframe stato cartomanti
   7) Blocco Numero 899
   8) Blocco Appuntamenti
   9) Banner Iscrizione Promozioni
   10) Titolo + descrizione servizio
   11) Banner Ricarica Web
   12) Footer

## HTML iframe (punto 6)
In Elementor usa widget **HTML** e incolla:

```html
<div style="text-align: center;">
 <iframe 
 id="pbxFrame"
 src="https://pbx.cartomanzia.it/realtime400.php" 
 title="Cartomanti Online"
 scrolling="no"
 style="width:600px; max-width:100%; height:140px; border:0;"
 frameborder="0">
 </iframe>
</div>

<script>
(function(){
 window.addEventListener("message", function(e){
 if(!e.data || e.data.type !== "pbxHeight") return;
 var f = document.getElementById("pbxFrame");
 if(!f) return;
 var h = parseInt(e.data.height,10);
 if(h && h > 80){
 f.style.height = h + "px";
 }
 });
})();
</script>
```

## Nota marketing/SEO
- H1 consigliato: `Cartomanzia Telefonica Economica: 5€ per 20 minuti`.
- Meta title: `Studio Amore | Cartomanzia Telefonica 5€ 20 Minuti`.
- CTA primaria: `Chiama ora 050 80627`.

