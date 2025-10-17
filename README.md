# Life Freedom System - WordPress Plugin

Ett holistiskt poäng- och belöningssystem för att uppnå frihet och autonomi i livet.

## 📋 Innehållsförteckning

- [Om pluginen](#om-pluginen)
- [Funktioner](#funktioner)
- [Installation](#installation)
- [Mappstruktur](#mappstruktur)
- [Konfiguration](#konfiguration)
- [Användning](#användning)
- [Support](#support)

## 🎯 Om pluginen

Life Freedom System är ett WordPress-plugin som hjälper dig att:
- Spåra och belöna aktiviteter som driver dig mot dina mål
- Balansera arbete, egna projekt, träning och relationer
- Hantera din ekonomi och bygga stabilitet
- Gå från heltidsjobb till egen företagare med autonomi

## ✨ Funktioner

### Poängsystem
- **Freedom Points (FP)** - För aktiviteter som driver företagande och autonomi
- **Balance Points (BP)** - För aktiviteter som håller dig frisk och närvarande
- **Stability Points (SP)** - För aktiviteter som bygger ekonomisk trygghet

### Custom Post Types
- **Aktiviteter** - Spåra alla dina dagliga aktiviteter
- **Projekt** - Hantera dina egna projekt och företag
- **Belöningar** - Definiera och lös in belöningar
- **Transaktioner** - Ekonomisk spårning
- **Milstolpar** - Sätt och uppnå stora mål

### Dashboard
- Översikt över alla poäng och progress
- Interaktiva grafer med Chart.js
- Snabbloggning av aktiviteter
- Veckomål och streak-spårning
- Tillgängliga belöningar

### Ekonomi
- Kontostatus för alla dina konton
- Månadsöversikter
- Läckage-detektering (när du bryter budgeten)
- Automatisk SP-beräkning från ekonomiska händelser

### Belöningar
- Gratis och ekonomiska belöningar
- Nivåbaserat system (Nivå 0-4)
- Poängkrav för varje belöning
- Historik över inlösta belöningar

## 🚀 Installation

### Förutsättningar
- WordPress 5.8 eller senare
- PHP 7.4 eller senare
- **MetaBox.io plugin** (obligatoriskt!)
  - Du måste ha MetaBox + alla extensions installerade
  - Ladda ner från: https://metabox.io

### Steg-för-steg installation

1. **Skapa plugin-mappen:**
   ```bash
   cd wp-content/plugins/
   mkdir life-freedom-system
   cd life-freedom-system
   ```

2. **Skapa mappstrukturen:**
   ```
   life-freedom-system/
   ├── life-freedom-system.php
   ├── includes/
   │   ├── class-lfs-meta-boxes.php
   │   ├── class-lfs-calculations.php
   │   ├── class-lfs-dashboard.php
   │   ├── class-lfs-rewards.php
   │   └── class-lfs-financial.php
   ├── admin/
   │   └── views/
   │       ├── dashboard.php
   │       ├── rewards.php
   │       ├── financial.php
   │       └── settings.php
   ├── assets/
   │   ├── css/
   │   │   ├── admin.css
   │   │   └── frontend.css
   │   └── js/
   │       ├── admin.js
   │       └── frontend.js
   └── languages/
   ```

3. **Kopiera alla filer till rätt platser enligt strukturen ovan**

4. **Installera MetaBox.io:**
   - Gå till WordPress Admin → Plugins → Add New
   - Sök efter "Meta Box"
   - Installera och aktivera Meta Box + alla tillgängliga extensions

5. **Aktivera pluginen:**
   - Gå till WordPress Admin → Plugins
   - Hitta "Life Freedom System"
   - Klicka på "Activate"

6. **Kontrollera att allt fungerar:**
   - Du ska nu se "Freedom System" i WordPress admin-menyn
   - Klicka på det för att komma till dashboarden

## 📁 Mappstruktur

```
life-freedom-system/
│
├── life-freedom-system.php          # Huvudfil - registrerar allt
│
├── includes/                         # Kärnfunktionalitet
│   ├── class-lfs-meta-boxes.php    # MetaBox konfigurationer
│   ├── class-lfs-calculations.php  # Poängberäkningar
│   ├── class-lfs-dashboard.php     # Dashboard logik
│   ├── class-lfs-rewards.php       # Belöningssystem
│   └── class-lfs-financial.php     # Ekonomisk spårning
│
├── admin/                            # Admin-gränssnitt
│   └── views/
│       ├── dashboard.php            # Dashboard-vy
│       ├── rewards.php              # Belöningssida
│       ├── financial.php            # Ekonomisida
│       └── settings.php             # Inställningar
│
├── assets/                           # CSS och JavaScript
│   ├── css/
│   │   ├── admin.css               # Admin-styling
│   │   └── frontend.css            # Frontend-styling
│   └── js/
│       ├── admin.js                # Admin JavaScript
│       └── frontend.js             # Frontend JavaScript
│
└── languages/                        # Översättningar (framtida)
```

## ⚙️ Konfiguration

### Första gången du använder pluginen

1. **Gå till Settings:**
   - Freedom System → Inställningar

2. **Välj din livsfas:**
   - **Survival** - Du har fortfarande heltidsjobb och kämpar ekonomiskt
   - **Stabilisering** - Egna projekt börjar ge inkomst
   - **Autonomi** - Du har lämnat heltidsjobbet och är självständig

3. **Sätt dina veckomål:**
   - FP-mål: 400-600 per vecka
   - BP-mål: 250-400 per vecka
   - SP-mål: 300-500 per vecka

4. **Konfigurera ekonomi:**
   - Ange din månadsinkomst
   - Sätt belöningskonto-procent (2-10% beroende på fas)

### Skapa standardaktiviteter

Du kan skapa fördefinierade aktiviteter som templates. Exempel:

**Deep Work (Eget projekt):**
- FP: 70
- Kategori: Arbete
- Typ: Deep Work
- Kontext: Eget projekt

**Träning:**
- BP: 35
- Kategori: Träning
- Typ: Träning

**Arbetat hemifrån:**
- SP: 40
- Kategori: Arbete
- Kontext: Heltidsjobb

### Skapa dina konton

Gå till Transaktioner → Konton och skapa:
- Hyra & Fasta utgifter
- Mat & Hem
- Elias Vardagspott
- Oförutsett
- Sparande & Investering
- Resor & Semester
- Belöningskonto

### Skapa belöningar

Exempel på belöningar att skapa:

**Nivå 0 - Gratis (0-50 poäng):**
- Gaming session guilt-free (30 BP)
- Netflix-kväll (40 BP)
- Siesta (25 BP)

**Nivå 1 - Daglig (50-100 kr, 50-100 poäng):**
- Fika på café (60 poäng, 50 kr)
- Godis (40 poäng, 30 kr)
- Sätt över 50 kr till belöningskonto (50 poäng)

**Nivå 2 - Vecka (100-300 kr, 150-300 poäng):**
- Middag ute (200 poäng, 250 kr)
- Ny bok (150 poäng, 150 kr)

**Nivå 3 - Månad (500-2000 kr, 400-800 poäng):**
- Dagsutflykt (500 poäng, 800 kr)
- Massage (600 poäng, 600 kr)

**Nivå 4 - Milstolpe (2000+ kr):**
- Baseras på specifika milstolpar

## 📖 Användning

### Daglig användning

1. **Morgon:**
   - Öppna dashboarden
   - Se dina veckomål
   - Planera dagen

2. **Under dagen:**
   - Logga aktiviteter direkt när du gör dem
   - Använd snabbloggning för vanliga aktiviteter
   - Eller lägg till manuellt via Aktiviteter → Add New

3. **Kväll:**
   - Gör en daglig avstämning
   - Se vad du uppnått
   - Planera nästa dag

### Veckovis

1. **Söndag kväll/Måndag morgon:**
   - Granska veckan som gick
   - Kontrollera om du nådde dina mål
   - Lös in eventuella belöningar
   - Sätt mål för kommande vecka

### Månadsvis

1. **Månadsskifte:**
   - Gå till Ekonomi-sidan
   - Granska månadsöversikt
   - Kontrollera om budget följts
   - Uppdatera kontosaldon
   - Fira om inga läckor!

### Tips för framgång

- **Var konsekvent** - Logga aktiviteter varje dag
- **Var ärlig** - Ge rätt poäng för aktiviteter
- **Balansera** - Se till att få både FP, BP och SP
- **Fira framsteg** - Lös in belöningar när du förtjänat dem
- **Justera** - Anpassa systemet efter dina behov

## 🔧 Felsökning

### Pluginen aktiveras inte
- Kontrollera att MetaBox.io är installerat och aktiverat
- Kontrollera PHP-version (minst 7.4)
- Kontrollera WordPress-version (minst 5.8)

### Dashboarden visar inga grafer
- Kontrollera att Chart.js laddas (öppna browser console)
- Töm cache
- Kontrollera att du har aktiviteter med datum

### Poäng uppdateras inte
- Kontrollera att aktiviteten är Published (inte Draft)
- Kontrollera att datum är satt
- Försök spara aktiviteten igen

### MetaBox fält visas inte
- Kontrollera att alla MetaBox extensions är aktiverade
- Gå till Settings → Meta Box → Tilllägg
- Aktivera alla tillgängliga extensions

## 🎨 Anpassning

### Ändra färger

Redigera `assets/css/admin.css`:

```css
/* FP färg */
.lfs-card-fp .lfs-points-number {
    color: #3498db; /* Ändra här */
}

/* BP färg */
.lfs-card-bp .lfs-points-number {
    color: #2ecc71; /* Ändra här */
}

/* SP färg */
.lfs-card-sp .lfs-points-number {
    color: #f39c12; /* Ändra här */
}
```

### Lägga till egna poängtyper

1. Redigera `includes/class-lfs-meta-boxes.php`
2. Lägg till nytt fält i `activity_points_meta_box()`
3. Uppdatera `class-lfs-calculations.php` för beräkningar

### Lägga till egna aktivitetsmallar

Redigera `includes/class-lfs-dashboard.php` i metoden `get_activity_templates()`:

```php
array(
    'name' => __('Min egen aktivitet', 'life-freedom-system'),
    'fp' => 50,
    'bp' => 20,
    'sp' => 10,
    'category' => 'Min kategori',
    'type' => 'Min typ',
    'context' => 'Min kontext',
),
```

## 🐛 Kända problem

- Inga kända buggar för tillfället

## 🔮 Framtida funktioner

- [ ] Export till CSV/Excel
- [ ] Email-rapporter
- [ ] Mobile app
- [ ] API för externa integrationer
- [ ] Mer avancerade statistik
- [ ] Team/familje-funktioner
- [ ] Gamification (badges, achievements)

## 📝 Changelog

### Version 1.0.0 (2025-10-16)
- Första release
- Alla grundläggande funktioner
- Dashboard med Chart.js
- Belöningssystem
- Ekonomisk spårning
- MetaBox integration

## 👨‍💻 Support

För support eller frågor:
- Öppna en issue på GitHub
- Kontakta plugin-utvecklaren

## 📄 Licens

GPL v2 or later

## 🙏 Tack till

- MetaBox.io för det fantastiska meta box-ramverket
- Chart.js för grafbiblioteket
- WordPress-communityn

---

**Byggd med ❤️ för att hjälpa dig uppnå frihet och autonomi**