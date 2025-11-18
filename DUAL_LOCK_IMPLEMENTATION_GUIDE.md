# LIFE FREEDOM SYSTEM - DUAL-LOCK BELÖNINGSSYSTEM
## Uppdateringsguide & Ändringslogg

**Datum:** 2025-11-16  
**Version:** 2.0 - Dual-Lock Implementation  
**Ändrat av:** Claude (AI Assistent för Rickard)

---

## 🎯 PROBLEMSTÄLLNING

### Tidigare system:
- Belöningar låstes upp baserat ENDAST på poäng
- Poäng konverterades teoretiskt till kronor (t.ex. 500 poäng = 250 kr vid 0.5 kr/poäng)
- Användaren kunde se belöningar som "tillgängliga" trots att belöningskontot var tomt
- Risk för att lova sig själv belöningar man inte har råd med

### Exempel på problemet:
```
Scenario:
- Användaren har 500 totala poäng
- Teoretiskt värde: 500 × 0.5 = 250 kr
- FAKTISKT saldo på belöningskonto: 50 kr

PROBLEM:
✗ Systemet visar belöningar för 250 kr som "tillgängliga"
✗ Användaren kan bara faktiskt köpa för 50 kr
✗ Missmatch mellan expectation och reality
```

---

## 💡 LÖSNINGEN: DUAL-LOCK SYSTEMET

### Koncept:
**Två lås måste öppnas för att en belöning ska vara tillgänglig:**

1. **LÅS 1 - POÄNG:** Har användaren tillräckligt med FP+BP+SP?
2. **LÅS 2 - PENGAR:** Finns det tillräckligt med FAKTISKA pengar på belöningskontot?

### Ny logik:
```
if (poäng >= krävda_poäng AND saldo >= kostnad) {
    status = 'affordable'  // ✅ Kan lösas in
} else if (poäng < krävda_poäng) {
    status = 'locked_points'  // 🔒 Saknar poäng
} else {
    status = 'locked_money'  // 💸 Saknar pengar
}
```

---

## 📂 FILER SOM UPPDATERATS

### 1. **class-lfs-rewards.php** (HUVUDFIL)
**Plats:** `includes/class-lfs-rewards.php`  
**Status:** ✅ Komplett omskrivning med nya metoder

#### Nya metoder tillagda:
```php
// Hämta faktiskt saldo från belöningskonto (via transaktioner)
public function get_actual_reward_account_balance()

// Beräkna rekommenderad månatlig överföring baserat på livsfas
public function calculate_recommended_transfer()

// Hämta alla belöningar med detaljerad status (affordable/locked_points/locked_money)
public function get_affordable_rewards($level = null)

// Hitta dyraste belöning användaren faktiskt har råd med
public function get_most_expensive_affordable_reward()

// AJAX handler för belöningsbudget-status
public function ajax_get_reward_budget_status()

// AJAX handler för affordable rewards
public function ajax_get_affordable_rewards()
```

#### Uppdaterade metoder:
```php
// Säkerhetskontroll: kollar BÅDE poäng OCH pengar innan inlösen
public function redeem_reward($reward_id)  // UPPDATERAD MED DUAL-LOCK

// Skapar automatiskt en transaktion när belöning löses in
// Drar pengar från faktiska belöningskontot, inte bara teoretiskt
```

---

### 2. **class-lfs-financial.php** (HJÄLPMETOD)
**Plats:** `includes/class-lfs-financial.php`  
**Status:** ✅ En ny metod tillagd

#### Ny metod:
```php
// Hämta saldo för ett specifikt konto via namn
public function get_account_balance($account_name)

Exempel:
$balance = $financial->get_account_balance('Belöningskonto');
// Returns: 1250 (kr)
```

---

### 3. **rewards.php VIEW** (UI UPPDATERAD)
**Plats:** `admin/views/rewards.php`  
**Status:** 🔄 Behöver uppdateras (se nedan)

#### Ändringar som behövs:

**A. Lägg till Belöningsbudget Widget:**
```php
<!-- Ny sektion överst på sidan -->
<div class="lfs-reward-budget-widget">
    <h3>💳 Din belöningsbudget</h3>
    <div class="lfs-budget-stats">
        <div class="lfs-stat">
            <span class="label">Faktiskt saldo:</span>
            <span class="value"><?php echo $actual_balance; ?> kr</span>
        </div>
        <div class="lfs-stat">
            <span class="label">Rekommenderat/månad:</span>
            <span class="value"><?php echo $recommended; ?> kr</span>
        </div>
        <div class="lfs-stat">
            <span class="label">Dyraste du har råd med:</span>
            <span class="value"><?php echo $most_expensive; ?></span>
        </div>
    </div>
    
    <?php if ($deficit > 0): ?>
        <div class="lfs-warning">
            ⚠️ <?php echo $warning_message; ?>
        </div>
        <button class="lfs-btn-secondary" onclick="showTransferModal()">
            💰 Överför till belöningskonto
        </button>
    <?php endif; ?>
</div>
```

**B. Uppdatera belöningskort med nya statuser:**
```php
<!-- Tidigare: bara 'available' eller 'pending' -->
<!-- Nytt: 'affordable', 'locked_points', 'locked_money', 'locked_both' -->

<div class="lfs-reward-card <?php echo 'lfs-' . $reward['status']; ?>">
    <?php if ($reward['status'] === 'affordable'): ?>
        <button class="lfs-btn-primary lfs-redeem-btn">
            🎁 Lös in
        </button>
    
    <?php elseif ($reward['status'] === 'locked_points'): ?>
        <div class="lfs-lock-badge lfs-lock-points">
            🔒 <?php echo $reward['lock_reason']; ?>
        </div>
    
    <?php elseif ($reward['status'] === 'locked_money'): ?>
        <div class="lfs-lock-badge lfs-lock-money">
            💸 <?php echo $reward['lock_reason']; ?>
        </div>
        <div class="lfs-hint">
            <?php echo $reward['hint']; ?>
        </div>
    
    <?php endif; ?>
</div>
```

---

## 🎨 CSS-ÄNDRINGAR (admin.css)

Lägg till dessa stilar:

```css
/* Belöningsbudget Widget */
.lfs-reward-budget-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.lfs-budget-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 15px 0;
}

.lfs-budget-stats .lfs-stat {
    background: rgba(255,255,255,0.1);
    padding: 12px;
    border-radius: 6px;
}

.lfs-budget-stats .label {
    display: block;
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 5px;
}

.lfs-budget-stats .value {
    display: block;
    font-size: 24px;
    font-weight: bold;
}

/* Låsta belöningar - olika färger för olika anledningar */
.lfs-reward-card.lfs-locked_points {
    opacity: 0.6;
    border-left: 4px solid #3498db; /* Blå = poäng */
}

.lfs-reward-card.lfs-locked_money {
    opacity: 0.7;
    border-left: 4px solid #e74c3c; /* Röd = pengar */
}

.lfs-reward-card.lfs-locked_both {
    opacity: 0.5;
    border-left: 4px solid #95a5a6; /* Grå = båda */
}

.lfs-lock-badge {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 13px;
    margin-top: 10px;
}

.lfs-lock-badge.lfs-lock-points {
    background: #ebf5fb;
    color: #2980b9;
}

.lfs-lock-badge.lfs-lock-money {
    background: #fadbd8;
    color: #c0392b;
}

.lfs-hint {
    font-size: 12px;
    font-style: italic;
    opacity: 0.8;
    margin-top: 8px;
}
```

---

## ⚡ JAVASCRIPT-ÄNDRINGAR (admin.js)

Lägg till nya AJAX-handlers:

```javascript
// Hämta belöningsbudget-status
function loadRewardBudgetStatus() {
    jQuery.post(ajaxurl, {
        action: 'lfs_get_reward_budget_status',
        nonce: lfs_vars.nonce
    }, function(response) {
        if (response.success) {
            updateBudgetWidget(response.data);
        }
    });
}

// Uppdatera budget-widget med live data
function updateBudgetWidget(data) {
    jQuery('#lfs-actual-balance').text(
        number_format(data.budget.current_balance, 0, ',', ' ') + ' kr'
    );
    jQuery('#lfs-recommended-monthly').text(
        number_format(data.budget.recommended_monthly, 0, ',', ' ') + ' kr'
    );
    
    if (data.most_expensive_affordable) {
        jQuery('#lfs-most-expensive').text(data.most_expensive_affordable.title);
    } else {
        jQuery('#lfs-most-expensive').text('Inga belöningar tillgängliga');
    }
}

// Visa snabbmodal för överföring till belöningskonto
function showTransferModal() {
    const amount = prompt('Hur mycket vill du överföra till belöningskonto?');
    
    if (!amount || isNaN(amount)) return;
    
    jQuery.post(ajaxurl, {
        action: 'lfs_create_transaction',
        nonce: lfs_vars.nonce,
        title: 'Överföring till belöningskonto',
        amount: amount,
        category: 'transfer',
        from_account: jQuery('#from-account-select').val(), // Du behöver en dropdown
        to_account: beloning_account_id, // Hårdkodat eller hämtat dynamiskt
        budget_followed: true,
        date: new Date().toISOString().split('T')[0]
    }, function(response) {
        if (response.success) {
            alert('✅ ' + amount + ' kr överfört! Du får ' + (amount / 100) + ' SP bonus.');
            location.reload();
        } else {
            alert('❌ Fel: ' + response.data);
        }
    });
}
```

---

## 🔄 DATAFLÖDE I DUAL-LOCK SYSTEMET

```
1. ANVÄNDARE LOGGAR AKTIVITET
   ↓
2. POÄNG SPARAS (FP, BP, SP)
   ↓
3. BELÖNINGSSIDA LADDAS
   ↓
4. LFS_Rewards::get_affordable_rewards() KÖRS
   ├─→ LOCK 1: Hämtar current_points från Calculations
   └─→ LOCK 2: Hämtar actual_balance från Financial
   ↓
5. VARJE BELÖNING UTVÄRDERAS:
   if (points >= required AND balance >= cost) → 'affordable'
   else if (points < required) → 'locked_points'
   else → 'locked_money'
   ↓
6. UI VISAR BELÖNINGAR MED KORREKT STATUS
   ↓
7. ANVÄNDARE KLICKAR "LÖS IN"
   ↓
8. redeem_reward() KÖRS
   ├─→ SÄKERHETSKONTROLL: Dubbelkollar BÅDA kriterierna
   ├─→ Drar av poäng
   └─→ Skapar transaktion som drar pengar från belöningskontot
   ↓
9. UPPDATERAD DATA SKICKAS TILLBAKA
```

---

## 📊 EXEMPELSCENARIO

### Före uppdatering:
```
Användare:
- 800 totala poäng
- Teoretiskt värde: 800 × 0.5 = 400 kr
- Faktiskt saldo belöningskonto: 75 kr

Systemet visar:
✅ "Middag ute" (250 kr, 200 poäng) - TILLGÄNGLIG
✅ "Ny bok" (150 kr, 150 poäng) - TILLGÄNGLIG
✅ "Fika på café" (50 kr, 60 poäng) - TILLGÄNGLIG

PROBLEM: Användaren har bara 75 kr!
```

### Efter uppdatering:
```
Användare:
- 800 totala poäng
- Faktiskt saldo belöningskonto: 75 kr

Systemet visar:
🔒 "Middag ute" (250 kr, 200 poäng) 
    💸 Saknar 175 kr på belöningskontot
    
🔒 "Ny bok" (150 kr, 150 poäng)
    💸 Saknar 75 kr på belöningskontot
    
✅ "Fika på café" (50 kr, 60 poäng)
    🎁 LÖS IN

Widget visar:
"Överför 175 kr för att komma i fas"
"Rekommenderat: 300 kr/månad (5% av inkomst, Stabilisering-fas)"
```

---

## ✅ IMPLEMENTERINGSCHECKLISTA

### Steg 1: Uppdatera PHP-klasser
- [ ] Byt ut `includes/class-lfs-rewards.php` mot uppdaterad version
- [ ] Uppdatera `includes/class-lfs-financial.php` med ny metod
- [ ] Testa att metoderna fungerar via PHP-konsol eller debug

### Steg 2: Uppdatera View
- [ ] Modifiera `admin/views/rewards.php` med nya sektioner
- [ ] Lägg till belöningsbudget-widget
- [ ] Uppdatera belöningskort med nya statuser

### Steg 3: Uppdatera CSS
- [ ] Lägg till nya stilar i `assets/css/admin.css`
- [ ] Testa visuellt i WordPress admin

### Steg 4: Uppdatera JavaScript
- [ ] Lägg till nya AJAX-handlers i `assets/js/admin.js`
- [ ] Implementera modal för snabböverföring
- [ ] Testa att AJAX-calls fungerar

### Steg 5: Testa systemet
- [ ] Logga aktiviteter för att få poäng
- [ ] Kontrollera att belöningar visar rätt status
- [ ] Testa att lösa in en belöning
- [ ] Verifiera att transaktion skapas
- [ ] Kontrollera att saldo uppdateras korrekt

---

## 🐛 FELSÖKNING

### Problem: Belöningar visar fortfarande gammal status
**Lösning:** Rensa WordPress cache eller tryck Ctrl+F5 i browser

### Problem: get_account_balance() ger fel
**Kontrollera:**
1. Att du har ett konto med "belöning" i namnet (case-insensitive)
2. Att kontot är korrekt taxonomi-term i 'lfs_account'

### Problem: Transaktionen skapas inte vid inlösen
**Kontrollera:**
1. Att belöningskonto term ID hittas korrekt
2. Att $financial->create_transaction() körs utan fel
3. Kolla error_log för PHP-fel

### Problem: Rekommenderad överföring visar 0 kr
**Kontrollera:**
1. Att 'lfs_monthly_income' är satt i wp_options
2. Att 'lfs_current_phase' är satt korrekt

---

## 🚀 FRAMTIDA FÖRBÄTTRINGAR

### Version 2.1 (Förslag):
- [ ] Automatisk överföring vid månadsskifte
- [ ] Notifikationer när belöningskonto är lågt
- [ ] Graf som visar belöningskonto-historik
- [ ] "Spara till belöning" - sätt mål för specifik belöning

### Version 2.2 (Förslag):
- [ ] Gruppbelöningar (dela kostnad med andra användare)
- [ ] Belönings-streaks (bonus vid konsekutiva inlösningar)
- [ ] Integration med externa belöningsappar (ex. Swish)

---

## 📞 SUPPORT

Om du stöter på problem:
1. Kontrollera felloggarna: `wp-content/debug.log`
2. Aktivera WP_DEBUG i `wp-config.php`
3. Testa metoderna direkt via PHP:
   ```php
   $rewards = LFS_Rewards::get_instance();
   $balance = $rewards->get_actual_reward_account_balance();
   var_dump($balance);
   ```

---

**Skapad av:** Claude (Anthropic)  
**För:** Rickard @ Tipping Point  
**Datum:** 2025-01-16
