# Brand Guidelines v1.0

> Last updated: 2026-10-12
> Status: Active
> Brand concept: **Modern Eco** — calm forest tones, warm sand neutrals, generous space, restrained motion.

## Quick Reference

| Element | Value |
|---------|-------|
| Primary Color | #31583D |
| Secondary Color | #EADFC9 |
| Accent Color | #D97706 |
| Background | #FBFAF7 |
| Display Font | Plus Jakarta Sans |
| Body Font | Inter |
| Voice | Warm, encouraging, practical, never preachy |

---

## 1. Brand Concept

**EcoJourney** is a sustainable tourism platform for Indonesia: curated low-impact destinations, an Eco Score for every place, and a trip planner that builds lighter-footprint itineraries.

- **Tagline (EN):** Travel better. Leave a lighter footprint.
- **Tagline (ID):** Perjalanan lebih baik. Tinggalkan jejak yang lebih ringan.
- **Kicker (EN):** Travel responsibly · **(ID):** Berwisata secara bertanggung jawab
- **Positioning:** Smart Sustainable Tourism / Pariwisata Berkelanjutan Cerdas
- **Audience:** Eco-conscious travelers, domestic and international, browsing for meaningful destinations in Indonesia.
- **Languages:** Bahasa Indonesia and English — every string ships in both.

The brand never guilt-trips. It shows a better way and makes it easy to take.

---

## 2. Color Palette

### Primary Colors — Forest (brand spine)

| Name | Hex | Usage |
|------|-----|-------|
| Forest 50 | #F2F7F3 | Tinted backgrounds, checked radio cards |
| Forest 100 | #E1EEE4 | Light surfaces, avatar fills |
| Forest 200 | #C4DDC9 | Borders on tinted surfaces |
| Forest 300 | #9FC5A8 | Hover borders |
| Forest 400 | #70A47E | Input focus border, gradient text |
| Forest 500 | #4F8760 | Focus rings, secondary accents |
| Forest 600 | #3D6E4C | Badge gradients (light stop) |
| Forest 700 | #31583D | **Primary** — buttons, links, logo mark |
| Forest 800 | #294733 | Badge gradients (dark stop) |
| Forest 900 | #223B2B | Dark hero sections, headings |
| Forest 950 | #14241A | Deepest shadows |

### Secondary Colors — Sand (warm neutrals)

| Name | Hex | Usage |
|------|-----|-------|
| Sand 50 | #FCFAF5 | Subtle warm surfaces |
| Sand 100 | #F5EFE2 | Aside panels, info cards |
| Sand 200 | #EADFC9 | Decorative blobs, secondary button |
| Sand 300 | #DDC9A8 | Secondary button hover |

### Accent Colors

| Name | Hex | Usage |
|------|-----|-------|
| Sun Amber | #D97706 | Admin badge, warm highlights |

### Neutral & Text

| Name | Hex | Usage |
|------|-----|-------|
| Cream | `#FBFAF7` | Page background |
| Surface | `#FFFFFF` | Cards, forms |
| Text | `#17201B` | Body text (15.98:1 on cream) |
| Muted | `#56635B` | Secondary text (6.04:1 on cream) |
| Border | `#E7ECE8` | Hairlines, card borders |
| Border Strong | `#D4DED6` | Card hover border |

### Accent & Semantic

| State | Hex | Usage |
|-------|-----|-------|
| Sun Amber | `#D97706` | Admin badge, warm highlights |
| Success | `#10B981` / emerald | Live dot, confirmations |
| Error | `#E11D48` / rose | Destructive (remove favorite), validation |
| Info | `#0EA5E9` | Informational (sparing) |

### Accessibility Rules

- Body and secondary text: minimum 4.5:1 contrast in both light surfaces and dark heroes.
- On dark forest (`#223B2B`) sections, muted text uses `white/60` or lighter — never `white/50` for small text.
- Interactive icons and control borders: minimum 3:1 against adjacent color.
- Focus ring: `0 0 0 4px rgba(79,135,96,0.22)` — must stay visible on every control.

---

## 3. Typography

### Font Stack

```css
--font-heading: 'Plus Jakarta Sans', sans-serif;  /* display: headings, numbers */
--font-body: 'Inter', sans-serif;                  /* body, UI */
--font-mono: 'JetBrains Mono', monospace;          /* code, Eco Score raw */
--font-display: var(--font-heading);
--font-sans: var(--font-body);
```

Loaded from Google Fonts: Inter 400–700, Plus Jakarta Sans 500–800.

### Type Scale

| Element | Desktop | Mobile | Weight | Tracking | Notes |
|---------|---------|--------|--------|----------|-------|
| H1 hero | 60–72px | 36–48px | 800 | -0.04em | Gradient span for second line |
| H2 section | 36–40px | 30–32px | 800 | -0.02em | Preceded by section kicker |
| H3 card | 20px | 20px | 800 | -0.02em | Destination titles |
| Body | 16–18px | 16px | 400 | 0 | Line height 1.7–1.8 |
| Small | 14px | 14px | 400–600 | 0 | Descriptions, labels |
| Caption | 12px | 12px | 500–800 | 0 | Muted meta text |
| Kicker | 11px | 11px | 800 | 0.16em | Uppercase, forest color |

### Rules

- All headings: `text-wrap: balance`, `letter-spacing: -0.02em` (hero `-0.04em`).
- Section kicker pattern: uppercase 11px/800 + 18px gradient dash prefix.
- Numbers (stats, Eco Score): display font, extrabold, forest-900.

---

## 4. Logo Usage

### Mark

A white stroked leaf (heart-shaped outline with midrib and two veins), stroke 1.8, inside a forest-700 rounded-2xl tile (40×40, radius 16px). Used in header and footer.

```
viewBox 0 0 24 24 · stroke currentColor · stroke-width 1.8
M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z
M12 21V9 · M12 14c-2.2-1.4-3.7-3.1-4.5-5 · M12 12c1.8-1.2 3.3-2.7 4.2-4.5
```

### Variants

| Variant | Use Case |
|---------|----------|
| Mark + wordmark (horizontal) | Header, documents |
| Mark only (tile) | Favicon, app icon, footer |
| Wordmark only | Where mark is already visible |

### Assets

| File | Size | Purpose |
|------|------|---------|
| `public/images/logo-mark.svg` | 40×40 tile | App icon, social avatar |
| `public/images/logo-horizontal.svg` | 212×48 | Header pattern, documents, email (light backgrounds) |
| `public/images/logo-horizontal-reversed.svg` | 212×48 | White wordmark for forest-900 / photos |
| `public/images/logo-stacked.svg` | 200×116 | Square spaces, splash, presentations |
| `public/images/logo-stacked-reversed.svg` | 200×116 | White tile + white wordmark for dark surfaces |
| `public/images/logo-leaf.svg` | 24×24, `currentColor` | Mono leaf for dark/colored surfaces |
| `public/favicon.svg` | 40×40 tile | Browser tab (referenced in spa.blade.php) |
| `public/logo-preview.html` | — | Internal visual QA gallery (7 checks) |

Notes:
- SVGs with `<text>` (horizontal, stacked) render the wordmark in Plus Jakarta Sans when the font is loaded; standalone `<img>` use falls back to system-ui. For pixel-critical exports, inline the lockup in the DOM (header pattern) or convert text to outlines before print.
- `logo-leaf.svg` inherits `color` — set `color: #FFF` on dark surfaces, `#31583D` on light.
- Legacy PNG icons (`icon-192.png`, `icon-512.png`, etc.) remain for PWA manifest only.

### Clear Space & Minimum Size

- Clear space: height of the mark tile on all sides.
- Minimum: 24px tile digital; wordmark 120px wide.

### Don'ts

- Don't rotate, skew, recolor outside the palette, or add effects.
- Don't use the leaf glyph as a substitute for UI icons — UI icons follow the icon rules below.
- Don't place the mark on photos without the solid tile or sufficient contrast.
- Don't use `logo-horizontal.svg` (dark wordmark) on forest-900 or photos — use the `-reversed` variant; white wordmark #FFFFFF needs ≥4.5:1 against the surface.

---

## 5. Voice & Tone

### Brand Personality

| Trait | Description |
|-------|-------------|
| **Encouraging** | Shows a better way without guilt-tripping or preaching |
| **Practical** | Action-first: real steps, real Eco Scores, working planner |
| **Warm** | Human and inviting, like a knowledgeable local friend |
| **Honest** | Transparent about data and limits; never greenwashing |

### Core Attributes

| Attribute | Description |
|-----------|-------------|
| **Curated** | Every destination is reviewed for real sustainability practice |
| **Transparent** | Eco Score shows conservation, community, and visitor management |
| **Bilingual** | Full ID + EN experience, never half-translated |
| **Actionable** | Inspiration always ends in a plan: explore, save, or build an itinerary |

### Tone by Context

| Context | Tone | Example |
|---------|------|---------|
| Hero / marketing | Aspirational, short | "Travel better. Leave a lighter footprint." |
| CTAs | Verb-first, sentence case | Explore Destinations · Plan my journey |
| Descriptions | Warm, concrete | "Discover meaningful destinations…" |
| Eco messaging | Factual, never shaming | "Community + conservation" |
| Errors | Calm, solution-first | "Try another search or clear filters." |
| Empty states | Inviting | "Explore destinations and save places you would love to visit." |
| Success | Brief | "Thank you for your review!" |

### Vocabulary

| Use | Avoid |
|-----|-------|
| footprint, lighter impact | "zero impact", "perfectly green" |
| curated, thoughtful | "exclusive", "premium" |
| community-led, local guides | "authentic experience" (overused) |
| Eco Score (with numbers) | Unbacked superlatives |
| conserve, protect | "save the planet" |

### Prohibited Phrases

| Phrase |
|--------|
| "zero impact" / "perfectly sustainable" |
| "save the planet" |
| "authentic experience" |
| "premium eco" / "luxury green" |
| "seamless" / "revolutionary" / "best-in-class" |
| "don't destroy" / "stop destroying" |

### Bilingual Rules

- Every user-facing string exists in `lang/id.json` and `lang/en.json`.
- Indonesian uses polite "Anda" for forms; CTA verbs stay imperative but soft.
- Keep key terms consistent: destinasi/destination, Eco Score/Skor Eco, Perjalanan/perjalanan.

---

## 6. Imagery & Icons

### Photography

- Natural, soft daylight; real landscapes and communities of Indonesia.
- Warm greens and sand tones harmonize with the palette; avoid heavy teal/orange filters.
- People in context, never staged "voluntourism" poses.

### Icons

- Outline only, 24px grid, stroke 1.8, round caps and joins, currentColor.
- One icon family per hierarchy level; decorative icons `aria-hidden="true"`.
- No emoji, dingbats (✦ ✓ ♡ ★), or text glyphs as icons — SVG only.
- Brand leaf is reserved for the logo; UI uses standard outline icons.

---

## 7. Design Components

### Buttons

| Type | Style |
|------|-------|
| Primary | Pill 999px, vertical gradient `#3F7350→#31583D→#2C5038`, inner top highlight, white text |
| Light | White, hairline border, forest-dark text |
| Outline | 1.5px forest-300 border, fills forest on hover |
| Secondary | Sand background, forest-dark text |

Hover: `translateY(-2px)` + layered shadow. Active: back to 0. Focus: 4px forest ring.

### Cards

| Type | Radius | Shadow |
|------|--------|--------|
| Destination | 24px | sm → xl on hover, `translateY(-6px)` |
| Soft / info | 28px | md |
| Aside / panel | 32px | none or sand fill |

### Radius & Spacing

- Radius scale: 12 / 16 / 20 / 24 / 28 / 32 / pill.
- Spacing rhythm: 4/8 base — section gaps 48–96px, card padding 20–40px.

### Motion

| Token | Value | Use |
|-------|-------|-----|
| Ease out | `cubic-bezier(0.22,1,0.36,1)` | Entries, arrivals |
| Ease in | `cubic-bezier(0.5,0,0.75,0)` | Exits (faster than entry) |
| Fast | 150ms | Hover, color |
| Base | 250ms | Cards, transforms |
| Slow | 400–600ms | Reveals, page transitions |

Respect `prefers-reduced-motion` globally. No infinite decorative animation — loaders only.

---

## 8. AI Image Generation

### Base Prompt Template

```
Modern eco tourism visual for EcoJourney: lush Indonesian rainforest and coastline
in soft natural daylight, calm forest green (#31583D) and warm sand (#EADFC9) tones,
airy negative space, editorial travel photography, no text, no logos
```

### Style Keywords

| Category | Keywords |
|----------|----------|
| **Lighting** | soft daylight, golden hour, diffused |
| **Mood** | calm, hopeful, grounded, inviting |
| **Composition** | centered subject, rule of thirds, generous negative space |
| **Treatment** | natural color, gentle contrast, film-like grain minimal |
| **Aesthetic** | modern editorial, organic, uncluttered |

### Visual Mood Descriptors

- calm and grounded
- hopeful, never solemn
- warm natural daylight
- organic textures, uncluttered frames

### Visual Don'ts

| Avoid | Reason |
|-------|--------|
| Heavy HDR, neon, cyberpunk | Clashes with calm eco tone |
| Crowded tourist traps | Undermines "thoughtful travel" |
| Stocky staged poses | Feels inauthentic |
| Text baked into images | Breaks localization |

---

## Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-10-12 | Initial guidelines — Modern Eco concept, forest/sand palette, bilingual voice |
