# Home page completa - Studioamore.it

Di seguito trovi i testi originali, la struttura pronta e gli asset grafici per costruire la home su WordPress + Astra + Elementor (free).

## Asset grafici creati
- `assets/hero-desktop-studioamore-1440x520.svg` (hero desktop 1440x520)
- `assets/hero-mobile-studioamore-1000x800.svg` (hero mobile 1000x800)
- `assets/banner-iscrizione-promozioni-1200x800.svg`
- `assets/banner-ricarica-web-1200x800.svg`

## Struttura home (in ordine)
1. **Logo**: carica il logo fornito in "Aspetto > Personalizza > Header Builder".
2. **Immagini di testata**: usa hero desktop/mobile come sfondo responsive della prima sezione Elementor.
3. **Slogan**:
   - Titolo: `Cartomanzia telefonica sincera, chiara, immediata`
   - Sottotitolo: `Consulti professionali sull'amore e sulle relazioni, disponibili ogni giorno.`
4. **Prima volta 2€ 10 minuti**:
   - `Nuovi clienti: prova il servizio con 2€ per 10 minuti.`
5. **Ricarica Web (testo)**:
   - `Ricarica online in modo sicuro e accedi subito al consulto.`
6. **Stato Cartomanti (iframe)**: inserisci widget HTML con il codice iframe fornito.
7. **Numero 899 (testo)**:
   - `Disponibile anche accesso tramite numero dedicato 899 (dove previsto dal tuo piano telefonico).`
8. **Servizio Appuntamenti**:
   - `Prenota il tuo consulto su fascia oraria preferita e parla con la cartomante che scegli.`
9. **Iscrizione Promozioni (immagine 1200x800)**:
   - Usa `banner-iscrizione-promozioni-1200x800.svg`.
10. **Titolo e descrizione servizio**:
   - Titolo: `Un servizio affidabile, riservato e orientato alle risposte`
   - Testo: `Studio Amore offre consulti telefonici in privato per amore, ritorni e scelte personali. Ogni lettura è svolta da professionisti selezionati con approccio chiaro e rispettoso.`
11. **Ricarica Web (immagine 1200x800)**:
   - Usa `banner-ricarica-web-1200x800.svg`.
12. **Footer (testo)**:
   - `Studioamore.it · Cartomanzia telefonica 5€ 20 minuti · Numero servizio 050 80627`

## Codice iframe pronto (punto 6)
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

## Note rapide Elementor/Astra
- Font consigliato: `Playfair Display` (titoli) + `Lato` (testi).
- Palette: oro `#f0c46a`, fondo `#090909`, testo chiaro `#f5ecd6`.
- Bottoni CTA consigliati:
  - `Chiama ora 050 80627`
  - `Attiva offerta 2€ / 10 min`
  - `Ricarica Web`
