# Styles

Folder ini berisi semua file CSS dan konfigurasi styling.

## File Structure

```
styles/
├── input.css             # Tailwind input directives
├── style.css             # Custom CSS (compiled Tailwind)
├── tailwind.config.js    # Tailwind configuration
├── globals.css           # Global styles
├── layouts.css           # Layout-specific styles
├── components.css        # Component styles
└── utils.css             # Utility functions & helpers
```

## Setup Tailwind CSS

```bash
npm install -D tailwindcss
npx tailwindcss init
npm run build:css
```

## Development Workflow

Untuk development dengan watch mode:
```bash
npm run watch:css
```

## Struktur CSS

- **globals.css**: Reset, variables, base styles
- **layouts.css**: Header, footer, main layout styles
- **components.css**: Reusable component styles
- **utils.css**: Helper classes dan mixins

## Penamaan Class Convention

- Gunakan kebab-case: `.button-primary`, `.card-header`
- Prefixes untuk state: `.is-active`, `.is-disabled`
- Gunakan Tailwind utilities sebisa mungkin
