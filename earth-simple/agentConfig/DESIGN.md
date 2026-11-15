---
name: Agro Daylight High-Contrast
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
    fontSize: 44px
    fontWeight: '800'
    lineHeight: 52px
    letterSpacing: -0.02em
  headline-xl-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 26px
    fontWeight: '700'
    lineHeight: 34px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  title-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 30px
  title-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '500'
    lineHeight: 30px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '700'
    lineHeight: 24px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 22px
    letterSpacing: 0.02em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-md: 1.5rem
  gutter-lg: 2rem
  margin: 1rem
  margin-md: 2rem
  margin-lg: 3rem
  space-xs: 0.5rem
  space-sm: 0.75rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system is tailored specifically for agricultural producers, agronomists, and field technicians operating under direct sunlight in rural Mexico. It bridges complex NASA orbital observations (SMAP soil moisture and POWER solar/meteorological feeds) into tangible, actionable agrarian advice.

The visual style blends **High-Contrast Pragmatism** with **Warm Organic Tactility**:
- **Tone:** Grounded, respectful, straightforward, and deeply reliable. Avoid patronizing simplification while eschewing detached academic and aerospace terminology (e.g., replace "Volumetric Soil Water at 0-5cm" with "Humedad en raíz superficial").
- **Lighting Adaptation:** Optimized for glare, harsh solar illumination, and high-luminance field contexts where standard UI fades.
- **Physicality:** Large touch targets designed for gloved hands or soiled fingers on field-grade devices, featuring clear structural borders and tactile feedback cues.

## Colors

The palette balances the deep organic vitality of Mexican agriculture with the urgent clarity required for environmental metrics:

- **Primary (`#1B5E20` - Bosque Fértil):** Dominates primary actions, headers, positive states, and navigation anchors. Provides extreme contrast against light surfaces.
- **Secondary (`#5D4037` - Tierra Negra):** Anchors secondary controls, metadata tags, structural borders, and soil context elements.
- **Tertiary (`#1565C0` - Cielo Órbita):** Reserved specifically for satellite-derived insights, rainfall predictions, SMAP radar data highlights, and telemetry sources. Light tint (`#E3F2FD`) serves as surface fill for satellite callouts.
- **Canvas Neutral (`#FAF9F6` - Crema Campo):** Reduces glare under harsh direct sun compared to blinding pure white, reducing eye fatigue during noon field audits.
- **Card Surface (`#FFFFFF` - Blanco Puro):** Elevated above the cream backdrop to delineate interactive modules crisply.
- **Critical Status Tones:**
  - *Success (`#2E7D32`):* Optimal moisture, safe planting windows.
  - *Warning (`#E65100` / `#F57F17`):* Drought watch, high evapotranspiration alerts.
  - *Danger (`#C62828`):* Frost risk, extreme heat spikes, zero soil water threshold.

All text combinations against their backgrounds strictly conform to WCAG AAA contrast ratios (minimum 7:1) for daylight legibility.

## Typography

The design system establishes a baseline reading experience starting at **18px** for all standard running body copy to ensure uncompromised visibility in outdoor environments without requiring zooming.

- **Typeface Selection:** Plus Jakarta Sans provides generous counter-spaces, wide apertures, and robust geometric letterforms that remain crisp on low-cost Android displays under high glare.
- **Visual Weight:** Avoid weights below `400` (Regular). Default copy leans into `500` (Medium) for body copy and `700` (Bold) for critical labels to prevent strokes from washing out under intense daylight.
- **Numerical Formatting:** Temperature, millimeter rainfall, and percentage moisture markers use tabular styling with explicit spacing to prevent misreading critical agronomic thresholds.

## Layout & Spacing

Field usability dictates an uncompromising touch-first spacing architecture:

- **Touch Target Safeguards:** Minimum interactive boundary size is `56px × 56px` across all buttons, inputs, and interactive cards.
- **Grid Structure:** A responsive single-column layout on mobile devices (`< 600px`), shifting to a 6-column grid on tablets (`600px - 1024px`), and an 8-column centralized grid on desktop/kiosk setups (`> 1024px`).
- **Rhythm:** Generous card padding (`space-lg` / 24px) insulates text from card boundaries, preventing accidental edge touches and giving clear visual separation when viewing tilted screens under direct sun.

## Elevation & Depth

Standard diffuse dropshadows disappear under harsh ambient outdoor light. Depth is communicated primarily through **solid tonal layering and deliberate high-contrast outlines**:

- **Card Elevation:** Pure white (`#FFFFFF`) surfaces sit directly on the warm cream canvas (`#FAF9F6`), framed with a crisp `2px solid #E0DED7` outline to prevent edge blooming.
- **Interactive Focus & Hover:** Elements acquire a distinct bottom-weight border (`3px solid #1B5E20`) or a crisp high-contrast border offset rather than subtle shadow expansions.
- **Alert & Satellite Modules:** Important callouts use tinted container surfaces (e.g., `#E3F2FD` for NASA SMAP observations) paired with a solid saturated border (`2px solid #1565C0`) to cleanly anchor them within view.

## Shapes

The interface utilizes a balanced `0.5rem` (`8px`) border radius base, stepping up to `1rem` (`16px`) on major dashboard cards and containers:

- **Corner Definition:** Avoids extreme sharp corners (`0px`) that feel severe or clinical, as well as oversized pill shapes (`3rem`) which waste vital readable space on cards and touch targets.
- **Touch Affirmation:** Rounded cards create distinct visual islands, making it immediately intuitive which cluster of data belongs together on rough terrain checks.

## Components

### Buttons & Interactive Controls
- **Primary Buttons:** High-contrast solid `#1B5E20` fill with bold `#FFFFFF` text. Minimum height `56px`. Padding `0 24px`. Border radius `8px`. Active state introduces a `#0D3311` press state.
- **Secondary Buttons:** Crisp white fill `#FFFFFF` with a `2px solid #5D4037` border and `#5D4037` text.
- **Satellite Action Buttons:** `#1565C0` fill with `#FFFFFF` text, accompanied by an icon indicating real-time satellite sync.

### Cards & Metric Panels
- **Field Data Cards:** Built with `#FFFFFF` background, `2px solid #E0DED7` perimeter, and `16px` inner radius. Each metric display contains:
  1. A clear natural Spanish descriptor (e.g., "Humedad en suelo hondo").
  2. Large numerical telemetry (e.g., "34%").
  3. A human status tag (e.g., "Nivel adecuado para siembra").
  4. Time of satellite pass in simple language (e.g., "Actualizado hoy a las 8:30 AM por satélite SMAP").

### Status Chips & Badges
- Minimum height `36px` to ensure touchability when dismissible.
- Background colors paired strictly with dark high-contrast text:
  - *Favorable:* Fill `#E8F5E9`, text `#1B5E20`, border `1.5px solid #2E7D32`.
  - *Alerta:* Fill `#FFF3E0`, text `#E65100`, border `1.5px solid #E65100`.
  - *Riesgo:* Fill `#FFEBEE`, text `#C62828`, border `1.5px solid #C62828`.
  - *Dato Satelital:* Fill `#E3F2FD`, text `#0D47A1`, border `1.5px solid #1565C0`.

### Form Inputs & Checkboxes
- **Inputs:** Height `56px`, `#FFFFFF` background, `2px solid #5D4037` border. Focus ring switches to `3px solid #1B5E20` with no blur. Label permanently visible above the input, never relying on low-contrast placeholders.
- **Checkboxes & Radios:** Scaled to `28px × 28px` with clear checkmarks and high-contrast checked states for swift toggling in the field.

### NASA Satellite Telemetry Banners
- Dedicated card styling for SMAP/POWER sync. Employs an `#E3F2FD` background with a left accent bar (`6px solid #1565C0`) and explicit indicators detailing satellite pass freshness in plain terminology.