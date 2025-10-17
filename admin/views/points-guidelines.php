<?php
/**
 * Points Guidelines View
 * 
 * File location: admin/views/points-guidelines.php
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap lfs-points-guidelines">
    <h1>📊 Poängriktlinjer - Life Freedom System</h1>
    
    <div class="lfs-guidelines-intro">
        <p class="lfs-lead">
            Detta dokument hjälper dig att poängsätta dina aktiviteter konsekvent och rättvist. 
            Använd dessa riktlinjer som utgångspunkt och anpassa efter din situation.
        </p>
    </div>
    
    <!-- Navigation -->
    <div class="lfs-guidelines-nav">
        <a href="#principles" class="lfs-nav-btn">Principer</a>
        <a href="#fp-guide" class="lfs-nav-btn">Freedom Points</a>
        <a href="#bp-guide" class="lfs-nav-btn">Balance Points</a>
        <a href="#sp-guide" class="lfs-nav-btn">Stability Points</a>
        <a href="#special" class="lfs-nav-btn">Specialfall</a>
        <a href="#quality" class="lfs-nav-btn">Kvalitetsskala</a>
        <a href="#bonuses" class="lfs-nav-btn">Bonusar</a>
        <a href="#mistakes" class="lfs-nav-btn">Vanliga fel</a>
        <a href="#quick-ref" class="lfs-nav-btn">Snabbreferens</a>
    </div>
    
    <!-- Principles -->
    <div id="principles" class="lfs-guidelines-section">
        <h2>🎯 Grundprinciper</h2>
        
        <div class="lfs-principles-grid">
            <div class="lfs-principle-card">
                <h3>📈 Impact</h3>
                <p>Hur mycket driver aktiviteten dig mot dina mål?</p>
            </div>
            <div class="lfs-principle-card">
                <h3>💪 Svårighetsgrad</h3>
                <p>Hur mycket ansträngning och disciplin krävs?</p>
            </div>
            <div class="lfs-principle-card">
                <h3>⏱️ Tidsåtgång</h3>
                <p>Längre aktiviteter får generellt mer poäng</p>
            </div>
            <div class="lfs-principle-card">
                <h3>🎯 Strategisk betydelse</h3>
                <p>Hur viktig är aktiviteten för din övergång?</p>
            </div>
        </div>
        
        <div class="lfs-important-rules">
            <h3>Viktiga regler:</h3>
            <ul class="lfs-rules-list">
                <li><strong>Kvalitet över kvantitet</strong> - 2h fokuserat Deep Work > 4h distraherat arbete</li>
                <li><strong>Ärlighet</strong> - Om du fuskar med poäng fuskar du bara dig själv</li>
                <li><strong>Konsistens</strong> - Använd samma poäng för samma aktivitet varje gång</li>
                <li><strong>Anpassa</strong> - Detta är DINA riktlinjer, gör dem till dina egna!</li>
            </ul>
        </div>
    </div>
    
    <!-- FP Guidelines -->
    <div id="fp-guide" class="lfs-guidelines-section lfs-fp-section">
        <h2>🚀 Freedom Points (FP) - Din väg till frihet</h2>
        <p class="lfs-section-intro">Aktiviteter som driver ditt företagande och autonomi framåt</p>
        
        <div class="lfs-points-category">
            <h3>🏆 Höga FP-värden (70-100 FP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Deep Work på eget projekt 2h+</strong></td>
                        <td class="lfs-points-cell">70 FP</td>
                        <td>Fokuserat, ingen distraktion</td>
                    </tr>
                    <tr>
                        <td><strong>Deep Work på eget projekt 3h+</strong></td>
                        <td class="lfs-points-cell">90 FP</td>
                        <td>Exceptionellt långt fokus</td>
                    </tr>
                    <tr>
                        <td><strong>Bloggpost 1500+ ord</strong></td>
                        <td class="lfs-points-cell">80 FP</td>
                        <td>Högkvalitativt innehåll</td>
                    </tr>
                    <tr>
                        <td><strong>YouTube-video (komplett)</strong></td>
                        <td class="lfs-points-cell">100 FP</td>
                        <td>Script + inspelning + redigering</td>
                    </tr>
                    <tr>
                        <td><strong>Podcastavsnitt</strong></td>
                        <td class="lfs-points-cell">90 FP</td>
                        <td>Prep + inspelning + redigering</td>
                    </tr>
                    <tr>
                        <td><strong>Levererat färdigt projekt</strong></td>
                        <td class="lfs-points-cell">100 FP</td>
                        <td>Till kund/publik</td>
                    </tr>
                    <tr>
                        <td><strong>Stängt affär (signerat kontrakt)</strong></td>
                        <td class="lfs-points-cell">150 FP</td>
                        <td>🎉 Stor milstolpe!</td>
                    </tr>
                    <tr>
                        <td><strong>Kundmöte/demo genomfört</strong></td>
                        <td class="lfs-points-cell">80 FP</td>
                        <td>Aktivt försäljningsarbete</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>💼 Medelhöga FP-värden (40-70 FP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Kontaktat 5-10 potentiella kunder</strong></td>
                        <td class="lfs-points-cell">60 FP</td>
                        <td>Cold outreach</td>
                    </tr>
                    <tr>
                        <td><strong>Följt upp på leads</strong></td>
                        <td class="lfs-points-cell">50 FP</td>
                        <td>5+ personer</td>
                    </tr>
                    <tr>
                        <td><strong>Kodat ny feature 1-2h</strong></td>
                        <td class="lfs-points-cell">60 FP</td>
                        <td>Produktutveckling</td>
                    </tr>
                    <tr>
                        <td><strong>Newsletter till lista</strong></td>
                        <td class="lfs-points-cell">55 FP</td>
                        <td>Kvalitativt innehåll</td>
                    </tr>
                    <tr>
                        <td><strong>LinkedIn-networking 30-60 min</strong></td>
                        <td class="lfs-points-cell">40 FP</td>
                        <td>Aktivt, inte passivt scrollande</td>
                    </tr>
                    <tr>
                        <td><strong>Community-deltagande</strong></td>
                        <td class="lfs-points-cell">40 FP</td>
                        <td>Hjälpt andra, delat kunskap</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>📚 Lägre FP-värden (20-40 FP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Online-kurs/tutorial 1h</strong></td>
                        <td class="lfs-points-cell">35 FP</td>
                        <td>Aktivt lärande</td>
                    </tr>
                    <tr>
                        <td><strong>Läst branschbok 30 min</strong></td>
                        <td class="lfs-points-cell">25 FP</td>
                        <td>Relevant för ditt område</td>
                    </tr>
                    <tr>
                        <td><strong>Bokföring/fakturering</strong></td>
                        <td class="lfs-points-cell">25 FP</td>
                        <td>Nödvändig administration</td>
                    </tr>
                    <tr>
                        <td><strong>Uppdaterat hemsida</strong></td>
                        <td class="lfs-points-cell">30 FP</td>
                        <td>Underhåll</td>
                    </tr>
                    <tr>
                        <td><strong>Veckoplanering för projekt</strong></td>
                        <td class="lfs-points-cell">30 FP</td>
                        <td>Strategisk planering</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- BP Guidelines -->
    <div id="bp-guide" class="lfs-guidelines-section lfs-bp-section">
        <h2>⚖️ Balance Points (BP) - Din hållbarhet</h2>
        <p class="lfs-section-intro">Aktiviteter som håller dig frisk, närvarande och i balans</p>
        
        <div class="lfs-points-category">
            <h3>💪 Fysisk Hälsa (25-40 BP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Promenad 30 min</strong></td>
                        <td class="lfs-points-cell">20 BP</td>
                        <td>Lätt aktivitet</td>
                    </tr>
                    <tr>
                        <td><strong>Promenad 45-60 min</strong></td>
                        <td class="lfs-points-cell">25 BP</td>
                        <td>Längre promenad</td>
                    </tr>
                    <tr>
                        <td><strong>Hemmaträning 30 min</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Strukturerad träning</td>
                    </tr>
                    <tr>
                        <td><strong>Gymträning 45-60 min</strong></td>
                        <td class="lfs-points-cell">35 BP</td>
                        <td>Standard träningspass</td>
                    </tr>
                    <tr>
                        <td><strong>Löpning 30+ min</strong></td>
                        <td class="lfs-points-cell">35 BP</td>
                        <td>Kardio</td>
                    </tr>
                    <tr>
                        <td><strong>HIIT/intensiv träning</strong></td>
                        <td class="lfs-points-cell">40 BP</td>
                        <td>Hög intensitet</td>
                    </tr>
                    <tr>
                        <td><strong>Yoga/stretching 30+ min</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Flexibilitet & balans</td>
                    </tr>
                    <tr>
                        <td><strong>Sport med vänner</strong></td>
                        <td class="lfs-points-cell">40 BP</td>
                        <td>Fysisk + social</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>🧠 Mental Hälsa & Återhämtning (15-35 BP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Meditation 10-20 min</strong></td>
                        <td class="lfs-points-cell">25 BP</td>
                        <td>Daglig meditation</td>
                    </tr>
                    <tr>
                        <td><strong>Journaling 15 min</strong></td>
                        <td class="lfs-points-cell">20 BP</td>
                        <td>Reflektion</td>
                    </tr>
                    <tr>
                        <td><strong>Läsa för nöje 30 min</strong></td>
                        <td class="lfs-points-cell">25 BP</td>
                        <td>Inte jobbrelater</td>
                    </tr>
                    <tr>
                        <td><strong>Digital detox 2h+</strong></td>
                        <td class="lfs-points-cell">35 BP</td>
                        <td>Ingen skärmtid</td>
                    </tr>
                    <tr>
                        <td><strong>Powernap 15-20 min</strong></td>
                        <td class="lfs-points-cell">15 BP</td>
                        <td>Kortare vila</td>
                    </tr>
                    <tr>
                        <td><strong>3+ arbetspauser tagna</strong></td>
                        <td class="lfs-points-cell">20 BP</td>
                        <td>Under arbetsdagen</td>
                    </tr>
                    <tr>
                        <td><strong>Sovit 7+ timmar</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>God nattsömn</td>
                    </tr>
                    <tr>
                        <td><strong>Sovit 8+ timmar</strong></td>
                        <td class="lfs-points-cell">35 BP</td>
                        <td>Optimal sömn</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>❤️ Relationer & Socialt (20-40 BP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Kvalitetstid med Camilla (aktivitet)</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Bio, promenad, etc</td>
                    </tr>
                    <tr>
                        <td><strong>Bara vara närvarande med Camilla</strong></td>
                        <td class="lfs-points-cell">25 BP</td>
                        <td>Bara vara tillsammans</td>
                    </tr>
                    <tr>
                        <td><strong>Middag utan skärmar</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Fokuserad tid</td>
                    </tr>
                    <tr>
                        <td><strong>Date night hemma</strong></td>
                        <td class="lfs-points-cell">35 BP</td>
                        <td>Planerad kvalitetstid</td>
                    </tr>
                    <tr>
                        <td><strong>Leka/hänga med Elias</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Tid med din son</td>
                    </tr>
                    <tr>
                        <td><strong>Samtal med familj/vänner 30+ min</strong></td>
                        <td class="lfs-points-cell">20 BP</td>
                        <td>Telefon eller IRL</td>
                    </tr>
                    <tr>
                        <td><strong>Träffa vänner</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Fika, middag, etc</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>🏠 Hem & Livskvalitet (15-30 BP)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Städa lägenhet</strong></td>
                        <td class="lfs-points-cell">25 BP</td>
                        <td>Grundlig städning</td>
                    </tr>
                    <tr>
                        <td><strong>Laga hälsosam mat</strong></td>
                        <td class="lfs-points-cell">20 BP</td>
                        <td>Självlagad mat</td>
                    </tr>
                    <tr>
                        <td><strong>Meal prep för veckan</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Planering + tillagning</td>
                    </tr>
                    <tr>
                        <td><strong>Handla mat</strong></td>
                        <td class="lfs-points-cell">15 BP</td>
                        <td>Nödvändig uppgift</td>
                    </tr>
                    <tr>
                        <td><strong>Organisera/decluttera</strong></td>
                        <td class="lfs-points-cell">25 BP</td>
                        <td>Skapa ordning</td>
                    </tr>
                    <tr>
                        <td><strong>Hobby/kreativitet 1h</strong></td>
                        <td class="lfs-points-cell">30 BP</td>
                        <td>Musik, konst, bygga</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- SP Guidelines -->
    <div id="sp-guide" class="lfs-guidelines-section lfs-sp-section">
        <h2>🛡️ Stability Points (SP) - Din ekonomiska trygghet</h2>
        <p class="lfs-section-intro">Aktiviteter som bygger din ekonomiska grund</p>
        
        <div class="lfs-points-category">
            <h3>💼 Heltidsjobb (Temporärt men nödvändigt)</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Deep work 2h på jobbet</strong></td>
                        <td class="lfs-points-cell">30 SP</td>
                        <td>Fokuserat arbete</td>
                    </tr>
                    <tr>
                        <td><strong>Fokuserad tid 1h</strong></td>
                        <td class="lfs-points-cell">15 SP</td>
                        <td>Bra arbete</td>
                    </tr>
                    <tr>
                        <td><strong>Snabb uppgift 15-30 min</strong></td>
                        <td class="lfs-points-cell">10 SP</td>
                        <td>Mindre uppgift</td>
                    </tr>
                    <tr>
                        <td><strong>Större uppgift 1-2h</strong></td>
                        <td class="lfs-points-cell">25 SP</td>
                        <td>Substantiell uppgift</td>
                    </tr>
                    <tr>
                        <td><strong>Projektleverans</strong></td>
                        <td class="lfs-points-cell">50 SP</td>
                        <td>Milestone på jobbet</td>
                    </tr>
                    <tr class="lfs-highlight-row">
                        <td><strong>Arbetat hemifrån</strong></td>
                        <td class="lfs-points-cell">40 SP</td>
                        <td>⭐ Viktigt för din övergång!</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>💰 Ekonomisk Disciplin</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Följt budget idag</strong></td>
                        <td class="lfs-points-cell">10 SP</td>
                        <td>Daglig disciplin</td>
                    </tr>
                    <tr>
                        <td><strong>Följt budget hela veckan</strong></td>
                        <td class="lfs-points-cell">50 SP</td>
                        <td>Veckoprestation</td>
                    </tr>
                    <tr>
                        <td><strong>Följt budget hela månaden</strong></td>
                        <td class="lfs-points-cell">100 SP</td>
                        <td>🎉 + Bonus!</td>
                    </tr>
                    <tr>
                        <td><strong>Sagt nej till impulsköp</strong></td>
                        <td class="lfs-points-cell">20 SP</td>
                        <td>Smart beslut</td>
                    </tr>
                    <tr>
                        <td><strong>Jämfört priser/använt rabatt</strong></td>
                        <td class="lfs-points-cell">10 SP</td>
                        <td>Ekonomisk smarts</td>
                    </tr>
                    <tr>
                        <td><strong>Budgetplanering för månad</strong></td>
                        <td class="lfs-points-cell">40 SP</td>
                        <td>30 min planering</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>💵 Sparande & Investering</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Sparat pengar</strong></td>
                        <td class="lfs-points-cell">1 SP per 100 kr</td>
                        <td>Löpande sparande</td>
                    </tr>
                    <tr>
                        <td><strong>Överföring till buffert</strong></td>
                        <td class="lfs-points-cell">1 SP per 100 kr</td>
                        <td>Bygger trygghet</td>
                    </tr>
                    <tr>
                        <td><strong>Investerat i fonder</strong></td>
                        <td class="lfs-points-cell">1 SP per 100 kr</td>
                        <td>Långsiktigt sparande</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>💸 Inkomst från Egna Projekt</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Fick betalt från kund</strong></td>
                        <td class="lfs-points-cell">50 SP per 1000 kr</td>
                        <td>Projektinkomst</td>
                    </tr>
                    <tr>
                        <td><strong>Första betalning från ny kund</strong></td>
                        <td class="lfs-points-cell">100 SP</td>
                        <td>🎉 Milstolpe!</td>
                    </tr>
                    <tr>
                        <td><strong>Återkommande intäkt (abo)</strong></td>
                        <td class="lfs-points-cell">70 SP/mån</td>
                        <td>Passiv inkomst</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="lfs-points-category">
            <h3>🚫 Läckor & Budget</h3>
            
            <table class="lfs-points-table">
                <thead>
                    <tr>
                        <th>Aktivitet</th>
                        <th>Poäng</th>
                        <th>Noteringar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Inga läckor denna vecka</strong></td>
                        <td class="lfs-points-cell">30 SP</td>
                        <td>Håller budgeten</td>
                    </tr>
                    <tr>
                        <td><strong>Inga läckor denna månad</strong></td>
                        <td class="lfs-points-cell">100 SP</td>
                        <td>🎉 Stor prestation!</td>
                    </tr>
                    <tr>
                        <td><strong>Inga läckor 3 månader i rad</strong></td>
                        <td class="lfs-points-cell">400 SP</td>
                        <td>🏆 + Stor belöning!</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Special Cases -->
    <div id="special" class="lfs-guidelines-section">
        <h2>🎯 Specialfall & Kombinationer</h2>
        
        <div class="lfs-special-examples">
            <div class="lfs-special-card">
                <h3>Networking-lunch med potentiell kund</h3>
                <div class="lfs-points-breakdown">
                    <span class="lfs-badge lfs-badge-fp">60 FP</span> Affärsutveckling<br>
                    <span class="lfs-badge lfs-badge-bp">20 BP</span> Social + paus från jobbet<br>
                    <strong>Total: 80 poäng</strong>
                </div>
            </div>
            
            <div class="lfs-special-card">
                <h3>Löpning + business-podcast</h3>
                <div class="lfs-points-breakdown">
                    <span class="lfs-badge lfs-badge-fp">20 FP</span> Lärande<br>
                    <span class="lfs-badge lfs-badge-bp">35 BP</span> Träning<br>
                    <strong>Total: 55 poäng</strong>
                </div>
            </div>
            
            <div class="lfs-special-card">
                <h3>Deep work på eget projekt hemma (hela dagen)</h3>
                <div class="lfs-points-breakdown">
                    <span class="lfs-badge lfs-badge-fp">90 FP</span> Mycket produktivt arbete<br>
                    <span class="lfs-badge lfs-badge-bp">25 BP</span> Hemarbete ger balans<br>
                    <strong>Total: 115 poäng</strong>
                </div>
            </div>
            
            <div class="lfs-special-card">
                <h3>Kvalitetstid med Camilla + framtidsplanering</h3>
                <div class="lfs-points-breakdown">
                    <span class="lfs-badge lfs-badge-fp">20 FP</span> Planering<br>
                    <span class="lfs-badge lfs-badge-bp">30 BP</span> Kvalitetstid<br>
                    <strong>Total: 50 poäng</strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quality Scale -->
    <div id="quality" class="lfs-guidelines-section">
        <h2>📈 Skala poäng efter kvalitet</h2>
        
        <div class="lfs-quality-examples">
            <h3>Deep Work-exempel:</h3>
            <table class="lfs-quality-table">
                <thead>
                    <tr>
                        <th>Kvalitet</th>
                        <th>Beskrivning</th>
                        <th>Poäng (2h)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="lfs-quality-poor">
                        <td><strong>Dålig session</strong></td>
                        <td>Många distraktioner, låg energi, lite gjort</td>
                        <td>50 FP (-20 FP)</td>
                    </tr>
                    <tr class="lfs-quality-normal">
                        <td><strong>Normal session</strong></td>
                        <td>Bra fokus, några avbrott, bra framsteg</td>
                        <td>70 FP (standard)</td>
                    </tr>
                    <tr class="lfs-quality-excellent">
                        <td><strong>Exceptionell session</strong></td>
                        <td>Flow-state, stora framsteg, noll distraktioner</td>
                        <td>90 FP (+20 FP)</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>Träningsexempel:</h3>
            <table class="lfs-quality-table">
                <thead>
                    <tr>
                        <th>Kvalitet</th>
                        <th>Beskrivning</th>
                        <th>Poäng (45 min)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="lfs-quality-poor">
                        <td><strong>Låg intensitet</strong></td>
                        <td>Gick på gymmet men slappade av</td>
                        <td>20 BP (-15 BP)</td>
                    </tr>
                    <tr class="lfs-quality-normal">
                        <td><strong>Normal träning</strong></td>
                        <td>Standard pass, bra ansträngning</td>
                        <td>35 BP (standard)</td>
                    </tr>
                    <tr class="lfs-quality-excellent">
                        <td><strong>Intensiv träning</strong></td>
                        <td>Fokuserad, pressade gränser, nytt PR</td>
                        <td>45 BP (+10 BP)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Bonuses -->
    <div id="bonuses" class="lfs-guidelines-section">
        <h2>🎲 Bonuspoäng & Multipliers</h2>
        
        <div class="lfs-bonus-grid">
            <div class="lfs-bonus-card">
                <h3>🔥 Streak-bonusar</h3>
                <ul>
                    <li>7 dagar i rad: <strong>+50 poäng</strong></li>
                    <li>30 dagar i rad: <strong>+200 poäng</strong></li>
                    <li>90 dagar i rad: <strong>+500 poäng</strong></li>
                </ul>
            </div>
            
            <div class="lfs-bonus-card">
                <h3>✨ Perfekta dagar</h3>
                <ul>
                    <li>Alla tre poängtyperna (FP+BP+SP): <strong>+20 poäng</strong></li>
                    <li>100+ poäng på en dag: <strong>+30 poäng</strong></li>
                    <li>150+ poäng på en dag: <strong>+50 poäng</strong></li>
                </ul>
            </div>
            
            <div class="lfs-bonus-card">
                <h3>📅 Vecko-bonusar</h3>
                <ul>
                    <li>Nådde alla tre veckomål: <strong>+100 poäng</strong></li>
                    <li>Arbetade hemifrån 4+ dagar: <strong>+50 SP</strong></li>
                    <li>Tränade 3+ gånger: <strong>+30 BP</strong></li>
                </ul>
            </div>
            
            <div class="lfs-bonus-card">
                <h3>🏆 Månads-bonusar</h3>
                <ul>
                    <li>Följt budget hela månaden: <strong>+50 SP</strong></li>
                    <li>Inga läckor: <strong>+100 SP</strong></li>
                    <li>Nådde veckomål alla 4 veckor: <strong>+200 poäng</strong></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Mistakes -->
    <div id="mistakes" class="lfs-guidelines-section">
        <h2>🚨 Vanliga misstag att undvika</h2>
        
        <div class="lfs-mistakes-grid">
            <div class="lfs-mistake-card">
                <h3>❌ Överpoängsättning</h3>
                <p class="lfs-wrong">"Kollade LinkedIn 10 min" → 50 FP</p>
                <p class="lfs-right">✅ Rätt: 10-15 FP eller inget alls</p>
            </div>
            
            <div class="lfs-mistake-card">
                <h3>❌ Dubbelpoängsättning</h3>
                <p class="lfs-wrong">Samma aktivitet loggas två gånger</p>
                <p class="lfs-right">✅ Rätt: En aktivitet för hela sessionen</p>
            </div>
            
            <div class="lfs-mistake-card">
                <h3>❌ Poäng för passivitet</h3>
                <p class="lfs-wrong">"Tittade på YouTube om business" → 40 FP</p>
                <p class="lfs-right">✅ Rätt: 15-20 FP (passivt lärande = lägre)</p>
            </div>
            
            <div class="lfs-mistake-card">
                <h3>❌ Inflationspoäng</h3>
                <p class="lfs-wrong">Gradvis höjer poäng för att få fler belöningar</p>
                <p class="lfs-right">✅ Rätt: Konsistens är nyckeln!</p>
            </div>
        </div>
        
        <div class="lfs-right-thinking">
            <h3>✅ Rätt sätt att tänka:</h3>
            <ol class="lfs-thinking-list">
                <li>Drev denna aktivitet mig verkligen framåt mot målen?</li>
                <li>Krävde den disciplin och fokus?</li>
                <li>Skulle jag vara stolt över att dela denna prestation?</li>
                <li>Känns poängen rättvis jämfört med andra aktiviteter?</li>
            </ol>
        </div>
    </div>
    
    <!-- Quick Reference -->
    <div id="quick-ref" class="lfs-guidelines-section lfs-quick-ref">
        <h2>📋 Snabbreferens</h2>
        
        <div class="lfs-ref-grid">
            <div class="lfs-ref-column">
                <h3>🚀 Freedom Points</h3>
                <table class="lfs-compact-table">
                    <tr>
                        <td>Deep Work 2h</td>
                        <td><strong>70 FP</strong></td>
                    </tr>
                    <tr>
                        <td>Innehåll skapat</td>
                        <td><strong>80 FP</strong></td>
                    </tr>
                    <tr>
                        <td>Kundkontakt</td>
                        <td><strong>60 FP</strong></td>
                    </tr>
                    <tr>
                        <td>Leverans</td>
                        <td><strong>100 FP</strong></td>
                    </tr>
                    <tr>
                        <td>Online-kurs 1h</td>
                        <td><strong>35 FP</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="lfs-ref-column">
                <h3>⚖️ Balance Points</h3>
                <table class="lfs-compact-table">
                    <tr>
                        <td>Träning</td>
                        <td><strong>35 BP</strong></td>
                    </tr>
                    <tr>
                        <td>Kvalitetstid</td>
                        <td><strong>30 BP</strong></td>
                    </tr>
                    <tr>
                        <td>Meditation</td>
                        <td><strong>25 BP</strong></td>
                    </tr>
                    <tr>
                        <td>Hemmauppgifter</td>
                        <td><strong>25 BP</strong></td>
                    </tr>
                    <tr>
                        <td>Pauser (3+)</td>
                        <td><strong>20 BP</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="lfs-ref-column">
                <h3>🛡️ Stability Points</h3>
                <table class="lfs-compact-table">
                    <tr>
                        <td>Arbetat hemifrån</td>
                        <td><strong>40 SP</strong> ⭐</td>
                    </tr>
                    <tr>
                        <td>Deep Work (jobb)</td>
                        <td><strong>30 SP</strong></td>
                    </tr>
                    <tr>
                        <td>Budget följd/dag</td>
                        <td><strong>10 SP</strong></td>
                    </tr>
                    <tr>
                        <td>Budget följd/vecka</td>
                        <td><strong>50 SP</strong></td>
                    </tr>
                    <tr>
                        <td>Projektinkomst (per 1000 kr)</td>
                        <td><strong>50 SP</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="lfs-guidelines-footer">
        <div class="lfs-remember-box">
            <h3>💡 Kom ihåg</h3>
            <p>
                Detta är DITT system. Använd dessa riktlinjer som utgångspunkt, 
                men anpassa efter vad som fungerar för DIG. 
                <strong>Konsistens och ärlighet är viktigare än perfekta poängvärden!</strong>
            </p>
        </div>
        
        <div class="lfs-actions">
            <a href="<?php echo admin_url('admin.php?page=life-freedom-system'); ?>" class="button button-primary button-hero">
                Tillbaka till Dashboard
            </a>
            <a href="<?php echo admin_url('edit.php?post_type=lfs_activity_tpl'); ?>" class="button button-secondary button-hero">
                Hantera aktivitetsmallar
            </a>
        </div>
    </div>
</div>

<style>
.lfs-points-guidelines {
    max-width: 1400px;
    margin: 0 auto;
}

.lfs-guidelines-intro {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px;
    border-radius: 8px;
    margin: 20px 0 30px;
}

.lfs-guidelines-intro .lfs-lead {
    font-size: 18px;
    margin: 0;
    color: rgba(255,255,255,0.95);
}

.lfs-guidelines-nav {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    position: sticky;
    top: 32px;
    z-index: 100;
}

.lfs-nav-btn {
    padding: 8px 16px;
    background: #f8f9fa;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
    font-size: 14px;
}

.lfs-nav-btn:hover {
    background: #3498db;
    color: #fff;
    transform: translateY(-2px);
}

.lfs-guidelines-section {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.lfs-guidelines-section h2 {
    margin: 0 0 20px;
    font-size: 28px;
    padding-bottom: 15px;
    border-bottom: 3px solid #3498db;
}

.lfs-fp-section h2 {
    border-bottom-color: #3498db;
}

.lfs-bp-section h2 {
    border-bottom-color: #2ecc71;
}

.lfs-sp-section h2 {
    border-bottom-color: #f39c12;
}

.lfs-section-intro {
    font-size: 16px;
    color: #666;
    margin: 0 0 25px;
    font-style: italic;
}

.lfs-principles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 25px 0;
}

.lfs-principle-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.lfs-principle-card h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.lfs-important-rules {
    background: #e8f5e9;
    padding: 25px;
    border-radius: 8px;
    margin-top: 25px;
}

.lfs-rules-list {
    margin: 15px 0 0;
    padding-left: 20px;
}

.lfs-rules-list li {
    margin: 10px 0;
    line-height: 1.6;
}

.lfs-points-category {
    margin: 30px 0;
}

.lfs-points-category h3 {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 6px;
    margin: 0 0 15px;
    font-size: 20px;
}

.lfs-points-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0 30px;
}

.lfs-points-table thead {
    background: #333;
    color: #fff;
}

.lfs-points-table th,
.lfs-points-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.lfs-points-table tbody tr:hover {
    background: #f8f9fa;
}

.lfs-points-cell {
    font-weight: 700;
    color: #3498db;
    text-align: right;
    white-space: nowrap;
}

.lfs-fp-section .lfs-points-cell {
    color: #3498db;
}

.lfs-bp-section .lfs-points-cell {
    color: #2ecc71;
}

.lfs-sp-section .lfs-points-cell {
    color: #f39c12;
}

.lfs-highlight-row {
    background: #fff3cd !important;
    font-weight: 600;
}

.lfs-special-examples {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 25px 0;
}

.lfs-special-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-top: 4px solid #9b59b6;
}

.lfs-special-card h3 {
    margin: 0 0 15px;
    font-size: 16px;
}

.lfs-points-breakdown {
    line-height: 1.8;
}

.lfs-quality-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.lfs-quality-table th,
.lfs-quality-table td {
    padding: 15px;
    text-align: left;
    border: 1px solid #ddd;
}

.lfs-quality-table thead {
    background: #333;
    color: #fff;
}

.lfs-quality-poor {
    background: #ffebee;
}

.lfs-quality-normal {
    background: #e3f2fd;
}

.lfs-quality-excellent {
    background: #e8f5e9;
}

.lfs-bonus-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin: 25px 0;
}

.lfs-bonus-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 25px;
    border-radius: 8px;
}

.lfs-bonus-card h3 {
    margin: 0 0 15px;
    color: #fff;
}

.lfs-bonus-card ul {
    margin: 0;
    padding: 0 0 0 20px;
}

.lfs-bonus-card li {
    margin: 10px 0;
    color: rgba(255,255,255,0.95);
}

.lfs-mistakes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin: 25px 0;
}

.lfs-mistake-card {
    background: #fff3cd;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
}

.lfs-mistake-card h3 {
    margin: 0 0 15px;
    color: #856404;
}

.lfs-wrong {
    color: #e74c3c;
    margin: 10px 0;
}

.lfs-right {
    color: #27ae60;
    margin: 10px 0;
    font-weight: 600;
}

.lfs-right-thinking {
    background: #e8f5e9;
    padding: 25px;
    border-radius: 8px;
    margin-top: 25px;
}

.lfs-thinking-list {
    margin: 15px 0 0;
    padding-left: 20px;
}

.lfs-thinking-list li {
    margin: 10px 0;
    line-height: 1.6;
}

.lfs-quick-ref {
    background: #f8f9fa;
}

.lfs-ref-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.lfs-ref-column h3 {
    margin: 0 0 15px;
    padding: 15px;
    background: #fff;
    border-radius: 6px;
    text-align: center;
}

.lfs-compact-table {
    width: 100%;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
}

.lfs-compact-table tr {
    border-bottom: 1px solid #eee;
}

.lfs-compact-table tr:last-child {
    border-bottom: none;
}

.lfs-compact-table td {
    padding: 12px 15px;
}

.lfs-compact-table td:last-child {
    text-align: right;
    font-weight: 700;
    color: #3498db;
}

.lfs-guidelines-footer {
    margin-top: 40px;
}

.lfs-remember-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 25px;
}

.lfs-remember-box h3 {
    margin: 0 0 15px;
    color: #fff;
}

.lfs-remember-box p {
    font-size: 18px;
    margin: 0;
    color: rgba(255,255,255,0.95);
    line-height: 1.6;
}

.lfs-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .lfs-guidelines-nav {
        position: static;
    }
    
    .lfs-principles-grid,
    .lfs-special-examples,
    .lfs-bonus-grid,
    .lfs-mistakes-grid,
    .lfs-ref-grid {
        grid-template-columns: 1fr;
    }
}

/* Print styles */
@media print {
    .lfs-guidelines-nav,
    .lfs-actions {
        display: none;
    }
    
    .lfs-guidelines-section {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Smooth scroll
    $('.lfs-nav-btn').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 100
        }, 500);
    });
});
</script>