# Ampy — Main CTA-block · Teknisk dokumentation

**Fil:** `/Users/juliuscallahan/Desktop/Main_CTA/index.html` (låst slutversion, Version A rev 2)
**Målgrupp:** utvecklare (Chris) — WordPress/Bricks-integration
**Status:** produktionsklar, ägar-godkänd rendering (canon). Ändra aldrig en "tekniskt korrekt" fix som flyttar pixel-godkänd rendering utan ägar-grind.

---

## 1. Översikt

Main CTA-blocket är Ampys **primära konverteringsblock** — ett återanvändbart sektionsband som droppas in längst ner (eller mitt i) på flera landningssidor för att fånga besökaren i ett enda, entydigt nästa steg: **ringa en elektriker**.

**Jobbet blocket gör**
- **Ett enda konverteringsmål:** ring-CTA mot `tel:+46102657979`. Ingen formulärgren, ingen sekundär knapp, inget e-postfång. Blocket är medvetet en *ren telefon-CTA* — friktionen är noll och löftet ("inom 60 sekunder") är operativt.
- **Trygghet före klick:** guld-Google-betygsrad (5,0) + porträtt på en riktig, namngiven elektriker (Edvin) bär beviset som gör att kallringning känns tryggt.
- **Dygnet-runt-drift (365/24/7):** blocket är JS-fritt, degraderar rent om assets faller bort, och reserverar layout så att inget hoppar (CLS = 0). Det ska stå orört och fungera på tusentals sidvisningar utan tillsyn.

**Var det används**
Vertikal-agnostiskt CTA-band som återanvänds på flera landningssidor (service, laddbox, elcentral m.fl.). Levereras som Bricks-block via shortcode så samma instans kan klistras in var som helst utan ombyggnad.

**Grafisk identitet**
Gradient-rubrik "Prata med en elektriker inom 60 sekunder!", ren guld-Google-rad, Edvin-foto med premiumskugga, och två brand-vågor (en bakgrundsvåg bakom allt + en overlay-våg över fotots nedre hörn).

---

## 2. HTML — DOM-struktur, semantik, a11y-scaffolding

### DOM-träd (kommenterat)

```html
<section class="mcta">                          <!-- yttre band: sektions-bg + centrering -->
  <div class="mcta__card">                       <!-- vitt kort, 2-kolumnsgrid, overflow:hidden -->

    <!-- (1) BAKGRUNDSVÅG — dekorativ, bakom allt (z-index:0) -->
    <img class="mcta__bgwave" src="assets/Vector-3.svg"
         alt="" aria-hidden="true" width="652" height="273">

    <!-- (2) TEXT-KOLUMN -->
    <div class="mcta__text">
      <h2 class="mcta__h">                         <!-- rubrik: normal text + gradient-span -->
        Prata med en elektriker
        <span class="grad">inom 60<br class="br-d"> sekunder!</span>
      </h2>
      <p class="mcta__p">Känn dig trygg …</p>       <!-- stödtext, max 52ch -->

      <div class="mcta__action">                    <!-- CTA + betyg staplade vertikalt -->
        <div class="mcta__cta">
          <a class="btn-ring" href="tel:+46102657979">   <!-- ENDA CTA:n -->
            <span class="btn-ring__chip" aria-hidden="true">
              <svg>…lur-ikon…</svg>                  <!-- dekorativ, döljs för SR -->
            </span>
            Ring 010-265 79 79                       <!-- SYNLIGT nummer = tillgängligt namn -->
          </a>
        </div>

        <!-- proof-rad: HELA raden = EN länk till Google-recensioner -->
        <a class="g-row" href="https://www.google.com/maps/place/Ampy/…"
           target="_blank" rel="noopener noreferrer"
           aria-label="5,0 på Google – Ampys betyg, 5 av 5 stjärnor. Läs recensionerna (öppnas i ny flik)">
          <svg class="g-icon" aria-hidden="true">…färgad Google-G…</svg>
          <span class="g-label"><strong>5,0</strong> på Google</span>
          <span class="stars" aria-hidden="true">   <!-- 5 gula stjärnor, rent visuellt -->
            <svg>…</svg> ×5
          </span>
        </a>
      </div>
    </div>

    <!-- (3) MEDIA-KOLUMN -->
    <figure class="mcta__media">                    <!-- semantisk figur för porträttet -->
      <img class="photo" src="assets/edvin.webp"
           alt="Edvin, elektriker på Ampy"
           width="933" height="1400" fetchpriority="high">
      <img class="mcta__wave" src="assets/overlay.svg"
           alt="" aria-hidden="true" width="277" height="100">
    </figure>

  </div>
</section>
```

### Semantik och varje elements roll

| Element | Roll | Motivering |
|---|---|---|
| `<section class="mcta">` | Landmärke-sektion | Sant sektionsinnehåll; gör blocket urskiljbart i dokumentöversikten. |
| `<h2 class="mcta__h">` | Rubriknivå 2 | Blocket sitter *inuti* en sida med en `<h1>` högre upp → h2 är rätt nivå. Chris: justera nivån om blocket placeras i annan rubrikkontext. |
| `.grad`-span | Gradient-delen av rubriken | Ren `<span>` inuti h2 — semantiskt fortfarande en enda rubrik. |
| `<p class="mcta__p">` | Brödtext / löftesutveckling | — |
| `<a class="btn-ring" href="tel:…">` | Primär CTA | Äkta `tel:`-länk, inte en knapp med JS. Fungerar på mobil (ringer direkt), på desktop (öppnar app-dialog). |
| Synlig text "Ring 010-265 79 79" | Tillgängligt namn för CTA | Ligger som textnod direkt i `<a>` → blir länkens accessible name. |
| `<a class="g-row">` | Sekundär förtroendelänk | Hela raden är en enda länk till Google-recensionerna. |
| `<figure class="mcta__media">` | Bildkontext | `<figure>` för porträttet; ingen `<figcaption>` behövs (alt-texten bär informationen). |

### A11y-scaffolding (aria, alt, roller)

- **Dekorativa bilder döljs korrekt:** båda vågorna + lur-chip + Google-G + stjärnorna har `alt=""` **och** `aria-hidden="true"`. De är rent visuella och ska inte nå skärmläsare.
- **Porträttet bär meningsbärande alt:** `alt="Edvin, elektriker på Ampy"` — ger en riktig person åt beviset, inte generisk "bild".
- **CTA:ns namn kommer från synlig text** (Label-in-Name uppfyllt): en användare som säger "klicka Ring 010-265 79 79" träffar rätt.
- **Betygsradens `aria-label`** komprimerar den visuella informationen (5,0 · G-ikon · 5 stjärnor) till en läsbar mening och flaggar "öppnas i ny flik" eftersom länken har `target="_blank"`.
- **Ny flik hanteras säkert:** `rel="noopener noreferrer"` på båda ut-länkarna.
- **Ikonstorlek redundant satt** i både attribut och CSS (se §7).

---

## 3. CSS — arkitektur, tokens, typografi, färgkanon, foto, betygsrad

### 3.1 Namngivning (BEM)

Strikt BEM med block-prefix `mcta`:

```
.mcta            (block — yttre band)
.mcta__card      (element)
.mcta__bgwave / __text / __h / __p / __action / __cta / __media / __wave   (element)
.mcta__h .grad   (modifierande del av rubriken)
.btn-ring        (fristående komponent-block)
.btn-ring__chip  (element)
.g-row           (fristående komponent-block: Google-raden)
.br-d            (utility: desktop-radbrytning)
```

Två fristående komponent-block (`.btn-ring`, `.g-row`) lever avsiktligt utanför `mcta`-namnrymden — de är återanvändbara mönster. Allt övrigt är `mcta__*`-element.

### 3.2 Token-systemet

Alla färger och radier definieras på `:root` som CSS-variabler och används verbatim genom hela blocket:

```css
:root{
  --teal:hsl(171 100% 33%);        /* Ampy teal (#00a991-ekvivalent) — primär accent */
  --teal-bright:hsl(171 95% 41%);  /* ljusare teal — gradientens vänstra stopp */
  --blue:hsl(189 43% 56%);         /* brand-blå — gradientens högra stopp */
  --midnight:hsl(237 69% 12%);     /* Ampy midnight (#090b32) — rubrik + fokusring */
  --ink:#363636;                   /* brödtextfärg */
  --section-bg:#f5f9ff;            /* ljus sektionsyta bakom kortet */
  --radius-l:clamp(16px, 1.4vw, 20px);  /* stor radie — kort + foto */
  --radius-m:16px;                 /* medelradie — CTA-knapp */
}
```

**Koppling till Ampy ap-tokens:** dessa lokala variabler är standalone-speglar av de kanoniska `ap*`-tokens i `ampy-design-system` (`--teal` ↔ Ampy teal-token, `--midnight` ↔ Ampy midnight-token, radius-skalan ↔ ap-radius). Vid Bricks-portning ska de mappas mot de riktiga `ap*`-globala variablerna så att en enda token-sanning styr — se §8. Ampy-tokens vinner alltid; dessa lokala värden får aldrig sätta paletten självständigt i produktion.

### 3.3 Typografi

- **Font:** `'Outfit'` (Google Fonts, vikterna 300;400;500;600;700;800 laddade). I produktion **self-hostas** Outfit (se §8) — CDN-länken är endast för denna fristående prototyp.
- **62,5 % root-trick:** `html{font-size:62.5%}` gör att `1rem = 10px`, så all rem-matematik blir läsbar (`1.6rem = 16px`). Body sätter tillbaka `font-weight:300` som bastyngd.
- **Viktledd hierarki (ingen fetstil-spam):** rubrik `500`, brödtext `300`, CTA `500`, "5,0" `700`, "på Google" ärver `400`. Tyngd bär hierarkin, inte storlek ensam.
- **Clamp-skala (fluid typografi):**

```css
.mcta__h{ font-size:clamp(2.8rem, 1.25vw + 2.4rem, 4rem); }   /* 28 → 40px */
.mcta__p{ font-size:clamp(1.6rem, .21vw + 1.53rem, 1.8rem); } /* 16 → 18px */
.btn-ring{ font-size:1.6rem; }                                /* 16px fast */
.g-row{ font-size:1.5rem; }                                   /* 15px */
```

Mellanvärdet i clamp har en `vw`-term **plus** ett rem-golv, vilket ger mjuk skalning mellan brytpunkterna utan att någonsin gå under min eller över max.

### 3.4 Färg- och gradientkanon

Gradient-rubriken använder text-clip:

```css
.mcta__h .grad{
  color:var(--teal-bright);                        /* fallback om clip ej stöds */
  background:linear-gradient(90deg,var(--teal-bright) 15%,var(--blue) 85%);
  -webkit-background-clip:text;background-clip:text;
  -webkit-text-fill-color:transparent;
  -webkit-box-decoration-break:clone;box-decoration-break:clone; /* jämn gradient över radbrytning */
  font-weight:inherit;
}
```

Gradienten går teal-bright → blue, 15 %–85 % (mjuka kanter). `box-decoration-break:clone` gör att gradienten renderas jämnt även när spannet bryts över två rader (annars får Safari en "söm" vid radbrytningen).

**Trippel-fallback** (gradient-texten får aldrig försvinna) — se §7.

CTA:ns gradient är en separat brand-gradient:

```css
background:linear-gradient(120deg,#b6f2ff 0%,#5eb1bf 100%);  /* ljus-cyan → dämpad teal-blå */
```

### 3.5 Foto-porträttet (border + skugga-tekniken)

```css
.mcta__media .photo{
  width:80%;height:auto;
  aspect-ratio:320/366;object-fit:cover;object-position:50% 46%;
  border-radius:var(--radius-l);
  border:1px solid rgba(255,255,255,.55);            /* hårfin ljusring */
  box-shadow:0 18px 40px -20px rgba(9,11,50,.26);    /* mjuk riktad midnatt-fallskugga */
}
```

Två detaljer, medvetet valda:

1. **`border` istället för `inset box-shadow` för ljusringen.** En `inset box-shadow` renderas **inte** på `<img>`-element i flera webbläsare. En 1px halvtransparent vit `border` ger samma "premiumkant" pålitligt.
2. **Negativ spread (`-20px`) på fallskuggan** håller sidfotavtrycket smalt så att skuggan ryms inom kortets padding (kortet har `overflow:hidden`).

**Varför `overflow:hidden` togs bort på `.mcta__media`** (kommentar i koden, rad 165–167): media-behållaren hade tidigare `overflow:hidden`, vilket **klippte fotots fallskugga**. Den togs bort eftersom (a) fotot bär sin egen `border-radius` så det behöver ingen klippande förälder, och (b) overlay-vågen ligger nu `bottom:0` och inom media horisontellt → ingen 1px-blödning som behöver klippas. Kortet (`.mcta__card`) behåller sin `overflow:hidden` för vågorna; media-behållaren gör det inte.

**Croppen:** `object-position:50% 46%` ramar in från huvudtopp (~16 % luft ovanför) ner till Ampy-loggan på tröjan (~86 %) — samma framing på alla enheter.

### 3.6 Betygsraden (`.g-row`)

Medveten "ingen box"-design (ägar-önskan): en **ren inline-rad under CTA:n**, ingen container/ram/bakgrund.

```css
.g-row{
  display:inline-flex;align-items:center;flex-wrap:wrap;gap:10px;
  min-height:44px;padding:6px 0;              /* 44px = full träffyta */
  font-size:1.5rem;font-weight:400;color:var(--midnight);line-height:1;
}
.g-row:hover .g-label{text-decoration:underline;text-underline-offset:3px}  /* diskret länkaffordans */
.g-row strong{font-weight:700}               /* "5,0" fet, "på Google" ärver 400 */
.g-row .stars svg{width:15px;height:15px;fill:#FBBC04}  /* Google-gula stjärnor */
```

Innehåll i ordning: **färgad Google-G · "5,0 på Google" · fem gula stjärnor**. Hela raden är en länk med 44px träffyta; underline visas bara på hover för diskret länkkänsla.

---

## 4. Responsiv strategi

Fyra regimer, var och en införd för att lösa en konkret bugg:

### >1024px — desktop (bas)
Grid `1fr 400px`, gap `clamp(24–48px)`, kort-padding `clamp(28–48px)` vertikalt / `clamp(28–72px)` horisontellt. Textkolumn flexar, media-kolumn fast 400px.

**Rubrik-`<br>`-tröskeln ≥1300px:**
```css
.br-d{display:none}
@media (min-width:1300px){.br-d{display:inline}}
```
Den forcerade radbrytningen ("…inom 60" / "sekunder!") aktiveras **endast** på bred desktop (≥1300px) där vänsterkolumnen säkert rymmer rad 1. Under 1300px sköter naturlig radbrytning det utan orphan (verifierat: 1120px ger rent 2-radigt "…inom" / "60 sekunder!"). **Varför:** en fast `<br>` på smalare desktop hade skapat en trerads-rubrik med hängande ord.

### 768–1024px — tablett (FLUID kolumn)
```css
grid-template-columns:minmax(0,1fr) clamp(260px,34vw,320px);
.mcta__h{font-size:clamp(2.6rem,2.2vw + 1.2rem,3.2rem)}  /* lugnare rubrik */
.mcta__media{min-height:0}
```
**Varför:** en fast bildkolumn (tidigare 340px) svälte textkolumnen på iPad Mini/Air så att rubriken blev **trerad**. Den fluida `clamp(260–320px)`-kolumnen (aldrig bredare än ~34vw) ger texten andrum och tar ner rubriken till lugnare storlek. Löser "iPad 3-rads-rubrik"-buggen. `min-height:0` släpper desktop-höjdkravet så kortet inte blir onödigt högt.

### ≤767px — mobil (stackat)
```css
.mcta__card{grid-template-columns:1fr;gap:20px;padding:20px 20px 28px}
.mcta__bgwave{display:none}                    /* bakgrundsvågen bort i trångt läge */
.mcta__media{order:-1;align-self:center;justify-content:center;
             width:min(92%,360px);margin-inline:auto}   /* bild ÖVERST, med tak */
.mcta__media .photo{width:100%}
.mcta__wave{width:60%}
```
**Bild-taket `min(92%,360px)`** sätts på **containern**, inte motivet. **Varför:** utan tak blev bilden **gigantisk mellan 450–767px** (fullbreddsporträtt fyllde halva skärmen). Taket kapar den till max 360px (och 92 % på riktigt små) och centrerar den. Vågen är barn till containern → linjerar då mot bildkanten istället för att sticka ut.

Mobil-layout i övrigt: bild `order:-1` (överst), rubrik + paragraf + CTA + betyg centrerade, CTA fullbredd, knappen vänder till **text vänster / ikon höger** (`flex-direction:row-reverse; justify-content:space-between`) för att matcha live-blockets grammatik.

### ≤360px — mycket små telefoner
```css
.mcta__h{font-size:2.2rem}
```
Ett hårt rubrikgolv (22px) så att rubriken inte spränger layouten på de smalaste enheterna.

---

## 5. Animationer

### Puls-ringen (`mctaRing`)
En expanderande vit ring runt lur-chipet — den enda kontinuerliga rörelsen, som subtilt signalerar "ring nu":

```css
.btn-ring__chip::after{
  content:"";position:absolute;inset:0;border-radius:50%;
  box-shadow:0 0 0 0 rgba(255,255,255,.65);
  animation:mctaRing 2.8s cubic-bezier(.4,0,.4,1) infinite;
}
@keyframes mctaRing{
  0%  {box-shadow:0 0 0 0  rgba(255,255,255,.65)}
  55% {box-shadow:0 0 0 9px rgba(255,255,255,0)}
  100%{box-shadow:0 0 0 0  rgba(255,255,255,0)}
}
```
Cykel 2,8s. Pulsen animerar **enbart `box-shadow`** (inte `width`/`transform` av layoutbärande element) → den triggar ingen reflow och kostar nära noll. **24/7-notering:** eftersom det är en ren box-shadow-puls på ett litet element utan layoutpåverkan är den säker att köra oändligt; när blocket scrollas off-screen pausar webbläsaren ändå animationen (ingen batteri-/CPU-svans). Se §9 för ägar-gated `content-visibility`/pulse-optimering.

### Hover-lyft
```css
.btn-ring:hover{ transform:translateY(-1.5px); filter:saturate(1.08) brightness(1.02); box-shadow:…förstärkt… }
.btn-ring:active{ transform:translateY(0); }
transition:transform .16s ease, box-shadow .16s ease, filter .16s ease;
```
Knappen lyfter 1,5px och skuggan djupnar på hover; trycks ned på `:active`.

### prefers-reduced-motion-täckning (fullständig)
```css
@media (prefers-reduced-motion:reduce){
  .btn-ring__chip::after{animation:none}          /* pulsen av */
  .btn-ring,.btn-ring:hover{transition:none;transform:none}  /* lyft + transition av */
}
```
Både pulsen och hover-lyftet stängs av för användare som begärt reducerad rörelse — ingen kvarvarande rörelse.

---

## 6. Tillgänglighet — WCAG-status

| Kriterium | Status | Implementation |
|---|---|---|
| **Fokus synligt (2.4.7)** | PASS | `focus-visible:outline:3px solid var(--midnight);outline-offset:3px` på både CTA och g-row. Midnight ger **≥3:1** fokuskontrast mot ljus yta — medvetet valt över `--blue` som bara gav ~2,5:1. |
| **Label in Name (2.5.3)** | PASS | CTA:ns synliga text "Ring 010-265 79 79" **är** dess tillgängliga namn. g-row:s `aria-label` inleds med den synliga texten "5,0 på Google". |
| **Träffytor (2.5.8)** | PASS | CTA `min-height:58px`; g-row `min-height:44px` + `padding:6px 0`. Båda ≥44px. |
| **Reducerad rörelse (2.3.3)** | PASS | Full täckning, se §5. |
| **Forced colors / högkontrast** | PASS | `@media (forced-colors:active)`: CTA får `border:1px solid ButtonText` (affordansen överlever när gradienten strippas); gradient-rubriken faller till `CanvasText`. |
| **Print** | PASS | `@media print`: gradient-rubriken faller till fast `#090b32`. |
| **Dekorativt brus döljs** | PASS | Vågor, chip, G-ikon, stjärnor: `aria-hidden="true"`. |
| **Icke-textkontrast, brödtext** | PASS | `--ink #363636` och `--midnight` mot vitt/ljust ≥ 4,5:1. |

### KÄND flagga (ägar-accepterad)
**Gradient-rubrikens textkontrast ≈ 2:1** mot det vita kortet — under WCAG AA:s 4,5:1 för stor text (3:1). Detta är en **medvetet ägar-accepterad varumärkes-rendering**: gradienten (teal-bright → blue) är en central brand-signatur och prioriteras framför strikt AA på just detta dekorativa rubrik-span. Mildrande faktorer: (a) rubriktexten är stor och fet-ish (500), (b) den icke-gradient-delen av rubriken ("Prata med en elektriker") är full midnight-kontrast, (c) all funktionell text (CTA, betyg, brödtext) klarar AA. Följ Ampys "approved rendering is canon"-regel: ändra inte gradienten för att jaga kontrast utan ägar-grind.

---

## 7. Robusthet / 24-7-drift

Blocket är byggt för att stå orört på tusentals sidvisningar utan tillsyn.

### CLS-reservation (Cumulative Layout Shift = 0)
Varje bild har **explicit `width`+`height`** i attribut:
```html
<img class="photo" … width="933" height="1400" fetchpriority="high">
<img class="mcta__bgwave" … width="652" height="273">
<img class="mcta__wave" … width="277" height="100">
```
Plus **`aspect-ratio:320/366`** på fotot och **`min-height:366px`** på media-behållaren (desktop). Tillsammans reserverar de porträttets footprint innan bilden laddat → inget hopp, ingen CLS. `fetchpriority="high"` på porträttet prioriterar hämtning eftersom det är blockets tyngsta visuella ankare.

### Asset-fel-degradering
- Faller en **våg** bort (SVG saknas) syns inget trasigt — de är `aria-hidden`, absolut­positionerade och rent dekorativa; layouten står.
- Faller **porträttet** bort behåller `<figure>` reserverad höjd (aspect-ratio + min-height); alt-texten "Edvin, elektriker på Ampy" beskriver vad som saknas.

### Gradient trippel-fallback (rubriken försvinner aldrig)
```css
/* 1. Inbyggd fallback: color satt INNAN clip → syns om text-fill inte träffar */
.mcta__h .grad{color:var(--teal-bright); …}
/* 2. @supports: webbläsare utan background-clip:text får solid teal */
@supports not ((-webkit-background-clip:text) or (background-clip:text)){
  .mcta__h .grad{-webkit-text-fill-color:currentColor;background:none;color:var(--teal-bright)}
}
/* 3. forced-colors + print: currentColor / CanvasText / #090b32 */
```
Om bakgrundsgrafiken av någon anledning nollas kan gradient-texten aldrig bli osynlig (transparent fill mot ingen bakgrund) — den faller alltid till en solid färg.

### JS-fritt
Noll JavaScript. Inget att ladda, inget att krascha, ingen hydration. CTA:n är en äkta `tel:`-länk, betygsraden en äkta `<a>`. Fungerar med JS avstängt.

### tel:-korrekthet
`href="tel:+46102657979"` — E.164-format med landsnummer (+46), inledande 0 borttaget. Synligt visas det nationella formatet "010-265 79 79". Ringer korrekt från mobil och desktop-dialers globalt.

---

## 8. Integration — WordPress / Bricks

### Shortcode
Blocket levereras som shortcode och droppas in på valfri landningssida:
```
[ampy_main_cta]
```
Registreras via FluentSnippets (PHP-snippet) enligt Ampys 3-fils-leveranskontrakt (CSS / PHP+HTML / JS — här utan JS eftersom blocket är JS-fritt). Shortcode gör att samma instans kan återanvändas på flera sidor utan ombyggnad.

### Self-hostad Outfit
CDN-länken (`fonts.googleapis.com`) i denna prototyp **byts mot self-hostad Outfit** i produktion (woff2, `font-display:swap`) per Ampys FluentSnippets-regler — inga externa font-anrop live.

### Wrapper-scopad CSS
All CSS måste scopas under en wrapper-klass så att Bricks/temat inte läcker in och blocket inte läcker ut. Byt namnrymden `.mcta` → **`.ampy-mcta`** (eller wrappa allt i `.ampy-mcta`):
```css
.ampy-mcta .mcta__card{…}
.ampy-mcta .btn-ring{…}
```
`rem→px` vid behov om temat inte garanterar 62,5 %-root (Bricks sätter ofta annan root) — konvertera rem-värdena till px, eller sätt `.ampy-mcta{font-size:...}` och använd `em`.

### Assets
`assets/Vector-3.svg`, `assets/overlay.svg`, `assets/edvin.webp` läggs i mediabiblioteket / temamapp; sökvägar byts till WordPress-URL:er (eller ACF/dynamiska).

### ACF-fält (rekommenderat för redigerbarhet)
| Fält | Typ | Innehåll |
|---|---|---|
| `mcta_heading` | text | "Prata med en elektriker" |
| `mcta_heading_grad` | text | "inom 60 sekunder!" |
| `mcta_body` | textarea | brödtext |
| `mcta_phone_e164` | text | `+46102657979` |
| `mcta_phone_display` | text | `010-265 79 79` |
| `mcta_rating` | text | `5,0` |
| `mcta_reviews_url` | url | Google-recensionslänk |
| `mcta_photo` | image | Edvin-porträtt (behåll 933×1400 / aspect 320:366-crop) |

### Bricks paste-JSON
En Bricks paste-JSON av blocket **finns** i leveranspaketet (Picasso `components/main-cta/`) — klistras direkt in i Bricks-editorn som ett strukturerat block istället för rå HTML. Chris kan välja: shortcode (enklast, en rad) eller paste-JSON (fullt redigerbart i Bricks). Verifiera token-mappning mot de globala `ap*`-variablerna vid inklistring.

---

## 9. [GAP] & öppna beslut

| # | Post | Status | Åtgärd |
|---|---|---|---|
| 1 | **"5,0 på Google" måste vara aktuellt** | ✅ ÄGAR-BEKRÄFTAT | Candour-grinden kräver att betyget är sant och nuvarande på Google vid publicering. Ägaren har bekräftat 5,0 som aktuellt. Om betyget rör sig: uppdatera `mcta_rating` + `aria-label` + antal stjärnor, annars dra raden. Aldrig hårdkoda ett inaktuellt betyg. |
| 2 | **"inom 60 sekunder"-löftet operativt** | ✅ ÄGAR-BEKRÄFTAT | Löftet är ett operativt åtagande, inte marknadsföringsfloskel — ägaren har bekräftat att telefonen bemannas så att 60-sekunderslöftet håller. Candour-grinden godkänd. |
| 3 | **Gradient-rubrikens kontrast (~2:1)** | ✅ ÄGAR-ACCEPTERAD | Se §6. Varumärkes-rendering prioriterad; ändra inte utan ägar-grind. |
| 4 | **`content-visibility` / pulse-optimering** | ⏳ ÄGAR-GATED | Möjlig mikro-optimering: `content-visibility:auto` på kortet + ev. pausa `mctaRing` explicit off-screen. Pulsen kostar redan nära noll (box-shadow, browser pausar off-screen), så detta är låg prioritet och rör pixel-godkänd rendering → kräver ägar-grind innan aktivering. |

**Kvar för Chris (rent tekniskt, ej candour):** wrapper-scopa CSS till `.ampy-mcta`, self-hosta Outfit, byt asset-sökvägar till WordPress-URL:er, koppla ACF-fälten, verifiera `tel:`-numret mot den bemannade linjen, och bekräfta 62,5 %-root eller konvertera rem→px i Bricks-kontexten.
