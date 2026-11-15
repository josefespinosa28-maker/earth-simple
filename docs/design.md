---
name: Agro Daylight Minimalist
colors:
  surface: '#faf9f6'
  surface-dim: '#dbdad7'
  surface-bright: '#faf9f6'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f3f1'
  surface-container: '#efeeeb'
  surface-container-high: '#e9e8e5'
  surface-container-highest: '#e3e2e0'
  on-surface: '#1a1c1a'
  on-surface-variant: '#41493e'
  inverse-surface: '#2f312f'
  inverse-on-surface: '#f2f1ee'
  outline: '#717a6d'
  outline-variant: '#c0c9bb'
  surface-tint: '#2a6b2c'
  primary: '#00450d'
  on-primary: '#ffffff'
  primary-container: '#1b5e20'
  on-primary-container: '#90d689'
  inverse-primary: '#91d78a'
  secondary: '#77574d'
  on-secondary: '#ffffff'
  secondary-container: '#fed3c7'
  on-secondary-container: '#795950'
  tertiary: '#003a75'
  on-tertiary: '#ffffff'
  tertiary-container: '#00509f'
  on-tertiary-container: '#a6c5ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#acf4a4'
  primary-fixed-dim: '#91d78a'
  on-primary-fixed: '#002203'
  on-primary-fixed-variant: '#0c5216'
  secondary-fixed: '#ffdbd0'
  secondary-fixed-dim: '#e7bdb1'
  on-secondary-fixed: '#2c160e'
  on-secondary-fixed-variant: '#5d4037'
  tertiary-fixed: '#d6e3ff'
  tertiary-fixed-dim: '#a9c7ff'
  on-tertiary-fixed: '#001b3d'
  on-tertiary-fixed-variant: '#00468c'
  background: '#faf9f6'
  on-background: '#1a1c1a'
  surface-variant: '#e3e2e0'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 38px
    fontWeight: '700'
    lineHeight: 46px
    letterSpacing: -0.025em
  headline-xl-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 30px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  title-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
  title-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
rounded:
  sm: 0.375rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.25rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-md: 1.5rem
  gutter-lg: 2rem
  margin: 1.25rem
  margin-md: 2rem
  margin-lg: 3rem
  space-xs: 0.5rem
  space-sm: 0.75rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Aesthetic Direction: Modern Agrarian Minimalism

Design with **editorial restraint, generous whitespace, and whisper-quiet structure**. The interface translates NASA satellite observations (SMAP soil moisture and POWER climate feeds) into calm, clear, human Spanish guidance for agricultural producers.

- **Visual Philosophy:** Let content and whitespace do the work. Avoid heavy visual chrome, thick borders, oversized controls, or visual clutter.
- **Tone & Copy:** Clear, natural Spanish (e.g., *"Humedad en raíz superficial"* instead of technical aerospace jargon). Keep labels concise and scannable.
- **Strict Restraint Rules (Do NOT Violate):**
  - **Never** use borders thicker than `1px` on cards, inputs, or badges.
  - **Never** use brown (`#77574d` / `#5d4037`) as a border color for form inputs or standard containers.
  - **Never** inflate standard web controls (buttons and inputs must stay at a refined `42px–44px` height, not `56px`).

## Color Usage & Hierarchy

Use the palette with discipline—90% of the viewport should remain warm neutral canvas and crisp white surfaces, allowing the deep forest green to anchor key actions:

- **Canvas (`#faf9f6` — `surface`):** Primary page background. Soft warm off-white that eliminates harsh glare while keeping the layout airy.
- **Elevated Cards (`#ffffff` — `surface-container-lowest`):** Crisp white surfaces for forms, metric cards, and panels.
- **Primary Brand & Actions (`#1b5e20` — `primary-container` / `#00450d` — `primary`):** Used exclusively for main headings, primary buttons, active states, and key positive metrics.
- **Primary Text (`#1a1c1a` — `on-surface`) & Muted Copy (`#41493e` — `on-surface-variant`):** High-legibility dark charcoal and muted sage-gray for body text and descriptions.
- **Subtle Dividers & Borders (`#e3e2e0` — `surface-variant` / `#c0c9bb` — `outline-variant`):** Hairline `1px` borders to softly define boundaries without visual heaviness.
- **Telemetry & Satellite Accents (`#003a75` — `tertiary` on `#d6e3ff` — `tertiary-fixed`):** Reserved strictly for NASA SMAP/POWER satellite pills and telemetry callout banners.
- **Semantic Status Tones:**
  - *Optimal / Favorable:* Text `#00450d` on `#acf4a4` (or soft `#f4f3f1` with `#1b5e20` accent).
  - *Warning / Alert:* Text `#795950` on `#fed3c7` (`secondary-container`).
  - *Critical / Error:* Text `#93000a` on `#ffdad6` (`error-container`).

## Typography

Use **Plus Jakarta Sans** with a clean, contemporary web scale and clear typographic contrast between titles, body text, and metadata:

- **Headlines (`28px–38px`, Bold `700`, tight tracking `-0.02em`):** Colored in `#1b5e20` or `#1a1c1a` for strong, clean hierarchy.
- **Body Text (`14px–16px`, Regular `400`, line-height `1.6`):** Colored in `#41493e` for effortless reading.
- **Micro-labels & Metadata (`12px`, SemiBold `600`):** Used for uppercase kickers, units, and subtle status tags.
- **Numbers:** Use `tabular-nums` for percentages, radiation (`kWh/m²`), and timestamps.

## Layout, Spacing & Elevation

- **Container & Grid:** Centered container (`max-width: 1152px` / `max-w-6xl`) with generous outer padding (`24px`) and clean 12-column responsive grids (`gap: 24px–40px`).
- **Whitespace First:** Leave ample breathing room around headings, between form fields (`16px–20px`), and inside cards (`24px–32px`).
- **Featherweight Elevation:** Differentiate cards using `#ffffff` against `#faf9f6`, paired with a subtle `1px solid #e3e2e0` border and a very soft ambient shadow (`0 1px 3px rgba(26, 28, 26, 0.04)`).

## Components

- **Primary Buttons:** Solid `#1b5e20` fill (hover `#00450d`), `#ffffff` text, `14px` SemiBold text, `42px–44px` height, `8px` (`0.5rem`) or `9999px` (pill) radius, `0 20px` horizontal padding. No borders.
- **Secondary / Ghost Links:** Clean text link in `#1b5e20` or `#41493e` (hover `#1a1c1a`), or minimal `#ffffff` button with `1px solid #c0c9bb` border.
- **Cards:** `#ffffff` fill, `1px solid #e3e2e0` border, `16px` (`1rem`) border radius, `24px–32px` internal padding.
- **Form Inputs:** `42px–44px` height, `#ffffff` background, `1px solid #c0c9bb` border, `8px`–`12px` border radius, `14px` text. Focus state uses a clean `1.5px` border in `#1b5e20` with a subtle soft ring (`rgba(27, 94, 32, 0.12)`). Always place clean `13px–14px` Medium labels above inputs.
- **Checkboxes:** Standard `16px × 16px` (`rounded`) with `#1b5e20` checked fill and subtle `1px solid #c0c9bb` border when unchecked.
- **Pills & Badges:** Compact inline pills (`24px–28px` height, `11px–12px` SemiBold text, `9999px` radius) using soft fills (`#d6e3ff` with `#003a75` text for satellite tags; `#acf4a4` with `#00450d` text for optimal states) without heavy borders.
- **Telemetry Callouts:** Soft rounded banner (`12px` radius) with `#d6e3ff` background, subtle `1px solid #a9c7ff` border, `#003a75` text, and a small inline icon.