# Main_CTA

Ampys **Main CTA-block** — hela designarbetet, från 1:1-klon av det live blocket till
den finslipade redesignen. Blockets ENDA call-to-action är **Ring** (ingen "Kostnadsfri
rådgivning" eller "Prata med en expert" i själva blocket).

## Slutgiltigt block
- **[`index.html`](index.html)** — den färdiga designen (ring-only). Öppna direkt i
  webbläsare; assets ligger i [`assets/`](assets/) (Edvin-foto + de två vågorna).

Innehåll: gradient-rubrik "Prata med en elektriker **inom 60 sekunder!**", brödtext,
**Ring-CTA** (blå gradient, vit chip med puls-ring), Google-betyg-badge, Edvin-foto med
brand-vågor. Responsiv: desktop två-kolumn (chip vänster), mobil staplad (bild överst,
centrerad rubrik/paragraf, fullbredds-knapp text vänster / ikon höger).

## Designhistorik
[`design-history/`](design-history/) bevarar hela resan:
- `baseline-klon/` — 1:1-klon av det nuvarande live-blocket på ampy.se (körbar baslinje).
- `redesign/va/` — huvudriktningen "Ring direkt" (versionen **med** både Ring + rådgivning,
  som referens; slutblocket ovan kör endast Ring).
- `redesign/vb/`, `redesign/vc/` — divergenta riktningar (proof-först / utan gradient).
- `redesign/cta-lab/` — 5 UX-agent-varianter av ring-knappen.
- `redesign/cta-textfarg/` — A/B navy vs vit knapptext.
- `main-cta.bricks.json` — verbatim Bricks-export från live.

## Kanon (slutblocket)
- Rubrik: Outfit 500, midnight + teal→blå gradient (`--color-25 → --color-7`).
- Ring-CTA: blå gradient `#b6f2ff → #5eb1bf`, mjuk navy-text, radius 16, puls-ring 2,8 s.
- Bild: Edvin (bröstbild, ampy-loggan synlig), aspect 320/385.
- Tokens: `html{font-size:62.5%}` (1rem = 10px), Ampy-palett verbatim.

Ring-CTA:n finns även som fristående, återanvändbar komponent i repot **CTA-website**
(`ring-cta/`).

## Öppna punkter (före skarp drift)
- **[GAP]** Verifiera att samtal faktiskt besvaras < 60 sekunder innan "inom 60 sekunder"-
  löftet + ring-först går live.
- Ägar-pick: knapptextfärg (navy vs vit, se `design-history/redesign/cta-textfarg/`).
- Ägar-beslut: gradient-rubrikens kontrast (~2,1–2,5:1) — behåll varumärkes-look eller
  mörka stoppen för WCAG. Idag = ägar-godkänd live-rendering.
- Bricks-mappning för Chris.
