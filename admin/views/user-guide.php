<?php
/**
 * User Guide View
 * 
 * File location: admin/views/user-guide.php
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap lfs-user-guide">
    <h1>📖 Life Freedom System - Användarguide</h1>
    
    <div class="lfs-guide-nav">
        <a href="#intro" class="lfs-nav-link">Introduktion</a>
        <a href="#quick-start" class="lfs-nav-link">Snabbstart</a>
        <a href="#points" class="lfs-nav-link">Poängsystemet</a>
        <a href="#activities" class="lfs-nav-link">Aktiviteter</a>
        <a href="#rewards" class="lfs-nav-link">Belöningar</a>
        <a href="#economy" class="lfs-nav-link">Ekonomi</a>
        <a href="#tips" class="lfs-nav-link">Tips & Tricks</a>
    </div>
    
    <!-- Introduction -->
    <div id="intro" class="lfs-guide-section">
        <h2>🎯 Välkommen till Life Freedom System</h2>
        
        <div class="lfs-guide-intro">
            <p class="lfs-lead">
                Life Freedom System är ett kraftfullt verktyg som hjälper dig att bygga livet du drömmer om - 
                ett liv med frihet, autonomi och balans.
            </p>
            
            <div class="lfs-intro-grid">
                <div class="lfs-intro-card">
                    <span class="lfs-intro-icon">🚀</span>
                    <h3>Från anställd till egen företagare</h3>
                    <p>Systemet är designat för att hjälpa dig göra övergången från heltidsjobb till att driva egna projekt på heltid.</p>
                </div>
                
                <div class="lfs-intro-card">
                    <span class="lfs-intro-icon">⚖️</span>
                    <h3>Balans i vardagen</h3>
                    <p>Spåra och belöna aktiviteter inom arbete, träning, relationer och personlig utveckling.</p>
                </div>
                
                <div class="lfs-intro-card">
                    <span class="lfs-intro-icon">💰</span>
                    <h3>Ekonomisk stabilitet</h3>
                    <p>Bygg ekonomisk trygghet genom att följa din budget och undvika "läckor" från sparkonton.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Start -->
    <div id="quick-start" class="lfs-guide-section">
        <h2>🚀 Snabbstart (5 minuter)</h2>
        
        <div class="lfs-steps">
            <div class="lfs-step">
                <div class="lfs-step-number">1</div>
                <div class="lfs-step-content">
                    <h3>Konfigurera inställningar</h3>
                    <p>Gå till <strong>Freedom System → Inställningar</strong></p>
                    <ul>
                        <li>Välj din livsfas (Survival/Stabilisering/Autonomi)</li>
                        <li>Sätt dina veckomål (FP, BP, SP)</li>
                        <li>Ange din månadsinkomst</li>
                    </ul>
                    <a href="<?php echo admin_url('admin.php?page=lfs-settings'); ?>" class="button button-primary">
                        Gå till Inställningar →
                    </a>
                </div>
            </div>
            
            <div class="lfs-step">
                <div class="lfs-step-number">2</div>
                <div class="lfs-step-content">
                    <h3>Skapa dina belöningar</h3>
                    <p>Definiera vad du vill kunna belöna dig själv med:</p>
                    <ul>
                        <li><strong>Nivå 0:</strong> Gratis belöningar (gaming, Netflix, siesta)</li>
                        <li><strong>Nivå 1:</strong> Små belöningar 0-100 kr (fika, godis)</li>
                        <li><strong>Nivå 2-4:</strong> Större belöningar (middag ute, massage, resor)</li>
                    </ul>
                    <a href="<?php echo admin_url('post-new.php?post_type=lfs_reward'); ?>" class="button button-primary">
                        Skapa belöning →
                    </a>
                </div>
            </div>
            
            <div class="lfs-step">
                <div class="lfs-step-number">3</div>
                <div class="lfs-step-content">
                    <h3>Logga din första aktivitet</h3>
                    <p>Börja tjäna poäng direkt! Använd snabbloggning på Dashboard eller skapa detaljerad aktivitet.</p>
                    <a href="<?php echo admin_url('admin.php?page=life-freedom-system'); ?>" class="button button-primary">
                        Gå till Dashboard →
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Points System -->
    <div id="points" class="lfs-guide-section">
        <h2>📊 Poängsystemet förklarat</h2>
        
        <div class="lfs-points-explanation">
            <div class="lfs-point-type">
                <div class="lfs-point-header" style="background: #3498db;">
                    <h3>🚀 Freedom Points (FP)</h3>
                    <span class="lfs-point-badge">Din väg till frihet</span>
                </div>
                <div class="lfs-point-body">
                    <p><strong>Vad de betyder:</strong> Aktiviteter som driver ditt företagande och autonomi framåt.</p>
                    
                    <h4>Exempel på FP-aktiviteter:</h4>
                    <table class="lfs-examples-table">
                        <tr>
                            <td><strong>Deep Work på eget projekt (2h+)</strong></td>
                            <td class="lfs-points-cell">70 FP</td>
                        </tr>
                        <tr>
                            <td><strong>Skapat innehåll (artikel, kursmaterial)</strong></td>
                            <td class="lfs-points-cell">80 FP</td>
                        </tr>
                        <tr>
                            <td><strong>Kontaktat potentiell kund</strong></td>
                            <td class="lfs-points-cell">60 FP</td>
                        </tr>
                        <tr>
                            <td><strong>Levererat kundarbete/projekt</strong></td>
                            <td class="lfs-points-cell">100 FP</td>
                        </tr>
                    </table>
                    
                    <p class="lfs-tip">💡 <strong>Tips:</strong> Fokusera på FP för att accelerera din övergång till egen företagare.</p>
                </div>
            </div>
            
            <div class="lfs-point-type">
                <div class="lfs-point-header" style="background: #2ecc71;">
                    <h3>⚖️ Balance Points (BP)</h3>
                    <span class="lfs-point-badge">Din hållbarhet</span>
                </div>
                <div class="lfs-point-body">
                    <p><strong>Vad de betyder:</strong> Aktiviteter som håller dig frisk, närvarande och i balans.</p>
                    
                    <h4>Exempel på BP-aktiviteter:</h4>
                    <table class="lfs-examples-table">
                        <tr>
                            <td><strong>Träning</strong></td>
                            <td class="lfs-points-cell">35 BP</td>
                        </tr>
                        <tr>
                            <td><strong>Kvalitetstid med partner/familj</strong></td>
                            <td class="lfs-points-cell">30 BP</td>
                        </tr>
                        <tr>
                            <td><strong>Pauser under arbetsdag</strong></td>
                            <td class="lfs-points-cell">20 BP</td>
                        </tr>
                        <tr>
                            <td><strong>Hemmauppgifter (städa, handla)</strong></td>
                            <td class="lfs-points-cell">25 BP</td>
                        </tr>
                    </table>
                    
                    <p class="lfs-tip">💡 <strong>Tips:</strong> Utan BP kommer du att bränna ut. Balans är nyckeln!</p>
                </div>
            </div>
            
            <div class="lfs-point-type">
                <div class="lfs-point-header" style="background: #f39c12;">
                    <h3>🛡️ Stability Points (SP)</h3>
                    <span class="lfs-point-badge">Din ekonomiska trygghet</span>
                </div>
                <div class="lfs-point-body">
                    <p><strong>Vad de betyder:</strong> Aktiviteter som bygger din ekonomiska grund.</p>
                    
                    <h4>Exempel på SP-aktiviteter:</h4>
                    <table class="lfs-examples-table">
                        <tr>
                            <td><strong>Följt budget hela veckan</strong></td>
                            <td class="lfs-points-cell">50 SP</td>
                        </tr>
                        <tr>
                            <td><strong>Inga "läckor" från sparkonton</strong></td>
                            <td class="lfs-points-cell">100 SP</td>
                        </tr>
                        <tr>
                            <td><strong>Arbetat hemifrån (istället för kontor)</strong></td>
                            <td class="lfs-points-cell">40 SP</td>
                        </tr>
                        <tr>
                            <td><strong>Deep Work på heltidsjobb</strong></td>
                            <td class="lfs-points-cell">30 SP</td>
                        </tr>
                        <tr>
                            <td><strong>Inkomst från eget projekt (per 1000 kr)</strong></td>
                            <td class="lfs-points-cell">50 SP</td>
                        </tr>
                    </table>
                    
                    <p class="lfs-tip">💡 <strong>Tips:</strong> SP bygger din buffert så du kan lämna heltidsjobbet trygg.</p>
                </div>
            </div>
        </div>
        
        <div class="lfs-phase-explanation">
            <h3>🎯 Livsfaser och poängvärde</h3>
            <p>Beroende på vilken fas du är i har dina poäng olika värde:</p>
            
            <div class="lfs-phases-grid">
                <div class="lfs-phase-card">
                    <h4>🔴 Survival</h4>
                    <p><strong>10 poäng = 5 kr</strong></p>
                    <p class="lfs-phase-desc">Du har fortfarande heltidsjobb och ekonomin är ansträngd. Fokusera på stabilitet.</p>
                </div>
                
                <div class="lfs-phase-card">
                    <h4>🟡 Stabilisering</h4>
                    <p><strong>10 poäng = 8 kr</strong></p>
                    <p class="lfs-phase-desc">Egna projekt börjar ge inkomst. Du bygger buffert och ökar din frihet.</p>
                </div>
                
                <div class="lfs-phase-card">
                    <h4>🟢 Autonomi</h4>
                    <p><strong>10 poäng = 10 kr</strong></p>
                    <p class="lfs-phase-desc">Full självständighet! Du styr din tid och ditt arbete helt.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activities -->
    <div id="activities" class="lfs-guide-section">
        <h2>✅ Arbeta med aktiviteter</h2>
        
        <div class="lfs-guide-content">
            <h3>Två sätt att logga aktiviteter:</h3>
            
            <div class="lfs-method-grid">
                <div class="lfs-method-card">
                    <h4>⚡ Snabbloggning (Dashboard)</h4>
                    <p>Perfekt för vanliga, återkommande aktiviteter.</p>
                    <ol>
                        <li>Gå till Dashboard</li>
                        <li>Klicka på en förinställd aktivitetsmall</li>
                        <li>Klart! Poäng läggs till automatiskt</li>
                    </ol>
                </div>
                
                <div class="lfs-method-card">
                    <h4>📝 Detaljerad loggning</h4>
                    <p>För unika aktiviteter eller när du vill lägga till detaljer.</p>
                    <ol>
                        <li>Gå till Aktiviteter → Add New</li>
                        <li>Fyll i titel och sätt poäng (FP/BP/SP)</li>
                        <li>Välj kategori, typ och kontext</li>
                        <li>Lägg till reflektion om du vill</li>
                        <li>Publicera</li>
                    </ol>
                </div>
            </div>
            
            <div class="lfs-best-practices">
                <h3>📌 Best Practices</h3>
                <ul class="lfs-checklist">
                    <li><strong>Logga direkt:</strong> Lägg in aktiviteter så fort du gör dem för att inte glömma</li>
                    <li><strong>Var ärlig:</strong> Ge rätt poäng - systemet fungerar bara om du är ärlig</li>
                    <li><strong>Var konsekvent:</strong> Logga varje dag, även små saker</li>
                    <li><strong>Reflektera:</strong> Använd anteckningsfältet för att lära av dina aktiviteter</li>
                    <li><strong>Koppla till projekt:</strong> Länka aktiviteter till dina projekt för att se progress</li>
                </ul>
            </div>
            
            <div class="lfs-example-day">
                <h3>📅 Exempel på en dag</h3>
                <table class="lfs-day-table">
                    <thead>
                        <tr>
                            <th>Tid</th>
                            <th>Aktivitet</th>
                            <th>Poäng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>07:00</td>
                            <td>Träning</td>
                            <td><span class="lfs-badge lfs-badge-bp">35 BP</span></td>
                        </tr>
                        <tr>
                            <td>09:00-12:00</td>
                            <td>Deep Work på eget projekt</td>
                            <td><span class="lfs-badge lfs-badge-fp">70 FP</span></td>
                        </tr>
                        <tr>
                            <td>12:00-13:00</td>
                            <td>Lunch & paus</td>
                            <td><span class="lfs-badge lfs-badge-bp">20 BP</span></td>
                        </tr>
                        <tr>
                            <td>13:00-17:00</td>
                            <td>Heltidsjobb (hemifrån)</td>
                            <td><span class="lfs-badge lfs-badge-sp">40 SP</span></td>
                        </tr>
                        <tr>
                            <td>18:00-20:00</td>
                            <td>Kvalitetstid med Camilla</td>
                            <td><span class="lfs-badge lfs-badge-bp">30 BP</span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Total dag:</strong></td>
                            <td><strong>70 FP + 85 BP + 40 SP = 195 poäng</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Rewards -->
    <div id="rewards" class="lfs-guide-section">
        <h2>🎁 Belöningssystemet</h2>
        
        <div class="lfs-guide-content">
            <p class="lfs-intro-text">
                Belöningar är kärnan i systemet - de ger dig dopaminkickar och håller dig motiverad!
            </p>
            
            <h3>Nivåer av belöningar:</h3>
            
            <div class="lfs-reward-levels">
                <div class="lfs-reward-level">
                    <h4>Nivå 0 - Gratis belöningar (30-50 poäng)</h4>
                    <p>Detta är <strong>psykologiska tillstånd</strong> - du ger dig själv <em>lov</em> att göra dessa saker utan skuld.</p>
                    <ul>
                        <li>Gaming session 2h (30 BP)</li>
                        <li>Netflix-maraton (40 BP)</li>
                        <li>Läsa bok hela eftermiddagen (35 BP)</li>
                        <li>Siesta/vila (25 BP)</li>
                    </ul>
                </div>
                
                <div class="lfs-reward-level">
                    <h4>Nivå 1 - Dagliga belöningar (50-100 kr, 50-100 poäng)</h4>
                    <ul>
                        <li>Fika på café (60 poäng, 50 kr)</li>
                        <li>Godis/snacks (40 poäng, 30 kr)</li>
                        <li>Takeaway-middag (80 poäng, 150 kr)</li>
                        <li><strong>Sätta över 50-100 kr till belöningskonto (50 poäng)</strong></li>
                    </ul>
                    <p class="lfs-tip">💡 <strong>Protip:</strong> "Överföring som belöning" är genialiskt - handlingen i sig själv ger dopamin!</p>
                </div>
                
                <div class="lfs-reward-level">
                    <h4>Nivå 2 - Veckobelöningar (100-300 kr, 150-300 poäng)</h4>
                    <ul>
                        <li>Middag ute med Camilla (200 poäng, 250 kr)</li>
                        <li>Ny bok eller spel (150 poäng, 150 kr)</li>
                        <li>Klädinköp (250 poäng, 300 kr)</li>
                    </ul>
                </div>
                
                <div class="lfs-reward-level">
                    <h4>Nivå 3 - Månadsbelöningar (500-2000 kr, 400-800 poäng)</h4>
                    <ul>
                        <li>Dagspa/massage (500 poäng, 600 kr)</li>
                        <li>Konsert/event (600 poäng, 800 kr)</li>
                        <li>Elektronik/gadget (700 poäng, 1500 kr)</li>
                    </ul>
                </div>
                
                <div class="lfs-reward-level">
                    <h4>Nivå 4 - Milstolpsbelöningar (2000+ kr, baserat på specifika mål)</h4>
                    <p>Dessa knyts till stora milstolpar:</p>
                    <ul>
                        <li>Första kunden på eget projekt → 1500 kr fritt</li>
                        <li>Första månad med 10k+ sidoinkomst → 3000 kr fritt</li>
                        <li>Sagt upp dig från heltidsjobbet → 5000 kr fritt</li>
                    </ul>
                </div>
            </div>
            
            <h3>🔓 Hur du löser in belöningar:</h3>
            <ol class="lfs-ordered-list">
                <li>Gå till <strong>Freedom System → Belöningar</strong></li>
                <li>Se vilka belöningar som är <span style="color: #2ecc71; font-weight: bold;">gröna</span> (du har råd)</li>
                <li>Kontrollera att du har tillräckligt på belöningskontot (om det kostar pengar)</li>
                <li>Klicka "Lös in"</li>
                <li>Njut av din belöning! 🎉</li>
            </ol>
        </div>
    </div>
    
    <!-- Economy -->
    <div id="economy" class="lfs-guide-section">
        <h2>💰 Ekonomisk spårning</h2>
        
        <div class="lfs-guide-content">
            <h3>Kontosystemet</h3>
            <p>Systemet hjälper dig att fördela din inkomst automatiskt över dessa konton:</p>
            
            <table class="lfs-accounts-table">
                <thead>
                    <tr>
                        <th>Konto</th>
                        <th>% av inkomst</th>
                        <th>Syfte</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Hyra & Fasta utgifter</strong></td>
                        <td>40-45%</td>
                        <td>Hyra, el, försäkringar, abonnemang</td>
                    </tr>
                    <tr>
                        <td><strong>Mat & Hem</strong></td>
                        <td>15-20%</td>
                        <td>Matinköp och hushållsprodukter</td>
                    </tr>
                    <tr>
                        <td><strong>Elias Vardagspott</strong></td>
                        <td>5-10%</td>
                        <td>För din son Elias</td>
                    </tr>
                    <tr>
                        <td><strong>Oförutsett</strong></td>
                        <td>5%</td>
                        <td>Buffert för oväntade utgifter</td>
                    </tr>
                    <tr>
                        <td><strong>Sparande & Investering</strong></td>
                        <td>10-15%</td>
                        <td>Långsiktigt sparande för dig och Elias</td>
                    </tr>
                    <tr>
                        <td><strong>Resor & Semester</strong></td>
                        <td>5%</td>
                        <td>Årliga resor och semester</td>
                    </tr>
                    <tr>
                        <td><strong>Belöningskonto</strong></td>
                        <td>2-10%</td>
                        <td>För dina belöningar (ökar med din fas)</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="lfs-warning-box">
                <span class="dashicons dashicons-warning"></span>
                <div>
                    <h4>⚠️ Undvik "läckor"!</h4>
                    <p>En "läcka" är när du flyttar pengar från sparkonton för att betala löpande utgifter. 
                    Detta är ett tecken på att din budget inte håller. Systemet varnar dig när detta händer.</p>
                    <p><strong>Varje månad utan läckor ger dig 50 SP bonus!</strong></p>
                </div>
            </div>
            
            <h3>Logga transaktioner</h3>
            <p>På <strong>Ekonomi</strong>-sidan kan du snabbt logga transaktioner:</p>
            <ol class="lfs-ordered-list">
                <li>Välj kategori (Lön, Projektinkomst, Utgift, Överföring, Sparande)</li>
                <li>Ange belopp</li>
                <li>Välj från/till konto</li>
                <li>Markera om budget följdes</li>
                <li>Klicka "Lägg till transaktion"</li>
            </ol>
            
            <p>SP beräknas automatiskt baserat på:</p>
            <ul class="lfs-checklist">
                <li>Inkomst från egna projekt: <strong>50 SP per 1000 kr</strong></li>
                <li>Sparande: <strong>1 SP per 100 kr</strong></li>
                <li>Överföring till belöningskonto: <strong>1 SP per 100 kr</strong></li>
                <li>Budget följd: <strong>+20 SP bonus</strong></li>
            </ul>
        </div>
    </div>
    
    <!-- Tips & Tricks -->
    <div id="tips" class="lfs-guide-section">
        <h2>💡 Tips & Tricks för framgång</h2>
        
        <div class="lfs-tips-grid">
            <div class="lfs-tip-card">
                <h3>📅 Daglig rutin</h3>
                <ul>
                    <li><strong>Morgon:</strong> Kolla Dashboard, se veckomål</li>
                    <li><strong>Under dagen:</strong> Logga aktiviteter direkt</li>
                    <li><strong>Kväll:</strong> Daglig avstämning (5 min)</li>
                </ul>
            </div>
            
            <div class="lfs-tip-card">
                <h3>📊 Veckovis</h3>
                <ul>
                    <li><strong>Söndag kväll:</strong> Granska veckan</li>
                    <li>Kontrollera om du nådde veckomål</li>
                    <li>Lös in belöningar du förtjänat</li>
                    <li>Planera nästa vecka</li>
                </ul>
            </div>
            
            <div class="lfs-tip-card">
                <h3>📈 Månadsvis</h3>
                <ul>
                    <li>Granska ekonomi-sidan</li>
                    <li>Kontrollera om budget följts</li>
                    <li>Fira om inga läckor!</li>
                    <li>Justera inställningar om behov</li>
                </ul>
            </div>
        </div>
        
        <div class="lfs-golden-rules">
            <h3>🏆 Gyllene regler</h3>
            <div class="lfs-rules-grid">
                <div class="lfs-rule-card">
                    <span class="lfs-rule-number">1</span>
                    <h4>Konsistens över intensitet</h4>
                    <p>Det är bättre att logga 50 poäng varje dag än 500 poäng en gång i veckan. Bygg vanan!</p>
                </div>
                
                <div class="lfs-rule-card">
                    <span class="lfs-rule-number">2</span>
                    <h4>Ärlighet är nyckeln</h4>
                    <p>Ge dig själv rätt poäng. Om du fuskar är det bara du själv du lurar.</p>
                </div>
                
                <div class="lfs-rule-card">
                    <span class="lfs-rule-number">3</span>
                    <h4>Balansera alla tre</h4>
                    <p>Se till att tjäna FP, BP OCH SP varje vecka. Bara FP leder till utbrändhet. Bara SP leder till stagnation.</p>
                </div>
                
                <div class="lfs-rule-card">
                    <span class="lfs-rule-number">4</span>
                    <h4>Fira framsteg</h4>
                    <p>Lös in belöningar! Det är inte fusk - det är hela poängen. Dopaminkickarna driver dig framåt.</p>
                </div>
                
                <div class="lfs-rule-card">
                    <span class="lfs-rule-number">5</span>
                    <h4>Anpassa systemet</h4>
                    <p>Detta är DITT system. Justera poängvärden, lägg till aktiviteter, ändra belöningar - gör det till ditt eget.</p>
                </div>
                
                <div class="lfs-rule-card">
                    <span class="lfs-rule-number">6</span>
                    <h4>Respektera Camilla-faktorn</h4>
                    <p>Kommunicera dina mål. Visa henne systemet. Skapa gemensamma belöningar. Sätt gränser kärleksfullt.</p>
                </div>
            </div>
        </div>
        
        <div class="lfs-common-problems">
            <h3>🔧 Vanliga utmaningar & lösningar</h3>
            
            <div class="lfs-problem">
                <h4>Problem: "Jag glömmer logga aktiviteter"</h4>
                <p><strong>Lösning:</strong></p>
                <ul>
                    <li>Sätt en påminnelse i telefonen (3 gånger/dag)</li>
                    <li>Ha Dashboard öppet i en tab hela dagen</li>
                    <li>Logga direkt efter varje aktivitet - vänta inte till kvällen</li>
                </ul>
            </div>
            
            <div class="lfs-problem">
                <h4>Problem: "Jag når aldrig mina veckomål"</h4>
                <p><strong>Lösning:</strong></p>
                <ul>
                    <li>Kanske är målen för höga? Sänk dem lite!</li>
                    <li>Fokusera på en poängtyp i taget</li>
                    <li>Kom ihåg att räkna små aktiviteter också</li>
                </ul>
            </div>
            
            <div class="lfs-problem">
                <h4>Problem: "Ekonomin läcker fortfarande"</h4>
                <p><strong>Lösning:</strong></p>
                <ul>
                    <li>Sänk de automatiska överföringarna till realistiska nivåer</li>
                    <li>Fokusera på att INTE flytta pengar tillbaka (framgång = hålla strukturen)</li>
                    <li>Diskutera spontana utgifter med Camilla i förväg</li>
                    <li>Öka FP-aktiviteter för att få mer projektinkomst</li>
                </ul>
            </div>
            
            <div class="lfs-problem">
                <h4>Problem: "Jag känner mig skyldig när jag löser in belöningar"</h4>
                <p><strong>Lösning:</strong></p>
                <ul>
                    <li>Du har FÖRTJÄNAT dem! Det är inte fusk - det är systemet.</li>
                    <li>Belöningar är bränsle, inte slöseri</li>
                    <li>Börja med gratis belöningar (Nivå 0) för att vänja dig</li>
                </ul>
            </div>
        </div>
        
        <div class="lfs-keyboard-shortcuts">
            <h3>⌨️ Tangentbordsgenvägar</h3>
            <table class="lfs-shortcuts-table">
                <tr>
                    <td><kbd>Ctrl/Cmd</kbd> + <kbd>K</kbd></td>
                    <td>Fokusera snabbloggning på Dashboard</td>
                </tr>
            </table>
        </div>
    </div>
    
    <!-- Success Stories Section -->
    <div class="lfs-guide-section lfs-success-section">
        <h2>🎯 Vägen framåt</h2>
        
        <div class="lfs-roadmap">
            <h3>Din resa i tre faser:</h3>
            
            <div class="lfs-journey">
                <div class="lfs-journey-phase">
                    <span class="lfs-phase-icon">🔴</span>
                    <h4>Fas 1: Survival (Månad 1-3)</h4>
                    <p><strong>Mål:</strong> Stabilisera ekonomin, bygga vanan</p>
                    <ul>
                        <li>Logga aktiviteter varje dag</li>
                        <li>Följ budget en hel månad</li>
                        <li>Bygg 30-dagars streak</li>
                        <li>Börja få inkomst från sidoprojekt</li>
                    </ul>
                    <p class="lfs-phase-result"><strong>Resultat:</strong> Ingen panik över ekonomi, systemet är en vana</p>
                </div>
                
                <div class="lfs-journey-phase">
                    <span class="lfs-phase-icon">🟡</span>
                    <h4>Fas 2: Stabilisering (Månad 4-12)</h4>
                    <p><strong>Mål:</strong> Bygga buffert, öka sidoinkomst</p>
                    <ul>
                        <li>3 månaders utgifter i buffert</li>
                        <li>Sidoinkomst 5-10k/månad</li>
                        <li>Inga läckor 3 månader i rad</li>
                        <li>Jobba hemifrån 3-4 dagar/vecka</li>
                    </ul>
                    <p class="lfs-phase-result"><strong>Resultat:</strong> Du ser slutmålet, ekonomisk trygghet växer</p>
                </div>
                
                <div class="lfs-journey-phase">
                    <span class="lfs-phase-icon">🟢</span>
                    <h4>Fas 3: Autonomi (Månad 12+)</h4>
                    <p><strong>Mål:</strong> Full självständighet</p>
                    <ul>
                        <li>Säg upp dig från heltidsjobb</li>
                        <li>Egen inkomst täcker alla utgifter + 20%</li>
                        <li>6 månaders buffert</li>
                        <li>Du bestämmer din tid helt</li>
                    </ul>
                    <p class="lfs-phase-result"><strong>Resultat:</strong> FRIHET! Du har nått målet 🎉</p>
                </div>
            </div>
        </div>
        
        <div class="lfs-final-message">
            <h3>🚀 Du klarar det!</h3>
            <p class="lfs-lead">
                Life Freedom System är ditt verktyg för att bygga livet du vill ha. 
                Det kommer ta tid, det kommer kräva disciplin, men varje dag tar dig närmare målet.
            </p>
            <p class="lfs-lead">
                <strong>Kom ihåg:</strong> Det är inte perfektionen som räknas - det är riktningen. 
                Så länge du rör dig framåt, även långsamt, så kommer du dit.
            </p>
            <p style="text-align: center; font-size: 24px; margin-top: 30px;">
                Lycka till på din resa! 🎯
            </p>
        </div>
    </div>
    
    <!-- Quick Reference -->
    <div class="lfs-guide-section lfs-quick-ref">
        <h2>📋 Snabbreferens</h2>
        
        <div class="lfs-ref-grid">
            <div class="lfs-ref-card">
                <h4>Poängvärden (exempel)</h4>
                <ul class="lfs-compact-list">
                    <li>Deep Work 2h: 70 FP</li>
                    <li>Innehållsskapande: 80 FP</li>
                    <li>Kundkontakt: 60 FP</li>
                    <li>Träning: 35 BP</li>
                    <li>Kvalitetstid: 30 BP</li>
                    <li>Hemarbete: 40 SP</li>
                    <li>Budget följd/vecka: 50 SP</li>
                </ul>
            </div>
            
            <div class="lfs-ref-card">
                <h4>Veckomål (rekommenderat)</h4>
                <ul class="lfs-compact-list">
                    <li>FP: 400-600</li>
                    <li>BP: 250-400</li>
                    <li>SP: 300-500</li>
                    <li>Total: 1000-1500 poäng/vecka</li>
                </ul>
            </div>
            
            <div class="lfs-ref-card">
                <h4>Belöningsnivåer</h4>
                <ul class="lfs-compact-list">
                    <li>Nivå 0: Gratis (30-50p)</li>
                    <li>Nivå 1: 0-100kr (50-100p)</li>
                    <li>Nivå 2: 100-300kr (150-300p)</li>
                    <li>Nivå 3: 500-2000kr (400-800p)</li>
                    <li>Nivå 4: 2000+kr (milstolpar)</li>
                </ul>
            </div>
            
            <div class="lfs-ref-card">
                <h4>Kontaktinfo</h4>
                <ul class="lfs-compact-list">
                    <li><a href="<?php echo admin_url('admin.php?page=life-freedom-system'); ?>">Dashboard</a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=lfs-rewards'); ?>">Belöningar</a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=lfs-financial'); ?>">Ekonomi</a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=lfs-settings'); ?>">Inställningar</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.lfs-user-guide {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.lfs-guide-nav {
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

.lfs-nav-link {
    padding: 8px 16px;
    background: #f8f9fa;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
}

.lfs-nav-link:hover {
    background: #3498db;
    color: #fff;
}

.lfs-guide-section {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.lfs-guide-section h2 {
    margin: 0 0 25px;
    font-size: 28px;
    color: #333;
    border-bottom: 3px solid #3498db;
    padding-bottom: 10px;
}

.lfs-lead {
    font-size: 18px;
    line-height: 1.6;
    color: #555;
    margin: 20px 0;
}

.lfs-intro-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.lfs-intro-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    border-radius: 8px;
    color: #fff;
    text-align: center;
}

.lfs-intro-icon {
    font-size: 48px;
    display: block;
    margin-bottom: 15px;
}

.lfs-intro-card h3 {
    color: #fff;
    margin: 0 0 10px;
}

.lfs-intro-card p {
    color: rgba(255,255,255,0.9);
    margin: 0;
}

.lfs-steps {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-top: 30px;
}

.lfs-step {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.lfs-step-number {
    background: #3498db;
    color: #fff;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    flex-shrink: 0;
}

.lfs-step-content {
    flex: 1;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.lfs-step-content h3 {
    margin: 0 0 10px;
    color: #333;
}

.lfs-step-content ul {
    margin: 15px 0;
}

.lfs-points-explanation {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.lfs-point-type {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.lfs-point-header {
    padding: 20px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.lfs-point-header h3 {
    margin: 0;
    color: #fff;
}

.lfs-point-badge {
    background: rgba(255,255,255,0.2);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
}

.lfs-point-body {
    padding: 25px;
    background: #fff;
}

.lfs-examples-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.lfs-examples-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.lfs-points-cell {
    text-align: right;
    font-weight: 700;
    color: #3498db;
}

.lfs-tip {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
}

.lfs-phases-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.lfs-phase-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
}

.lfs-phase-card h4 {
    margin: 0 0 10px;
    font-size: 20px;
}

.lfs-phase-desc {
    color: #666;
    font-size: 14px;
    margin: 10px 0 0;
}

.lfs-method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.lfs-method-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.lfs-best-practices {
    background: #e8f5e9;
    padding: 25px;
    border-radius: 8px;
    margin: 25px 0;
}

.lfs-checklist {
    list-style: none;
    padding: 0;
}

.lfs-checklist li {
    padding: 8px 0 8px 30px;
    position: relative;
}

.lfs-checklist li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #2ecc71;
    font-weight: 700;
    font-size: 18px;
}

.lfs-day-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.lfs-day-table th,
.lfs-day-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.lfs-day-table thead {
    background: #3498db;
    color: #fff;
}

.lfs-reward-levels {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.lfs-reward-level {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #9b59b6;
}

.lfs-reward-level h4 {
    margin: 0 0 15px;
    color: #9b59b6;
}

.lfs-ordered-list {
    counter-reset: item;
    list-style: none;
    padding: 0;
}

.lfs-ordered-list li {
    counter-increment: item;
    padding: 10px 0 10px 40px;
    position: relative;
}

.lfs-ordered-list li:before {
    content: counter(item);
    position: absolute;
    left: 0;
    top: 8px;
    background: #3498db;
    color: #fff;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.lfs-accounts-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.lfs-accounts-table th,
.lfs-accounts-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.lfs-accounts-table thead {
    background: #2ecc71;
    color: #fff;
}

.lfs-warning-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 20px;
    margin: 25px 0;
    display: flex;
    gap: 15px;
}

.lfs-warning-box .dashicons {
    color: #ffc107;
    font-size: 32px;
    width: 32px;
    height: 32px;
    flex-shrink: 0;
}

.lfs-warning-box h4 {
    margin: 0 0 10px;
    color: #856404;
}

.lfs-tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.lfs-tip-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-top: 4px solid #3498db;
}

.lfs-tip-card h3 {
    margin: 0 0 15px;
}

.lfs-rules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.lfs-rule-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    border-radius: 8px;
    color: #fff;
    position: relative;
}

.lfs-rule-number {
    position: absolute;
    top: -15px;
    right: 20px;
    background: #fff;
    color: #667eea;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.lfs-rule-card h4 {
    margin: 0 0 10px;
    color: #fff;
}

.lfs-rule-card p {
    margin: 0;
    color: rgba(255,255,255,0.9);
}

.lfs-problem {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid #e74c3c;
}

.lfs-problem h4 {
    margin: 0 0 10px;
    color: #e74c3c;
}

.lfs-shortcuts-table {
    width: 100%;
    border-collapse: collapse;
}

.lfs-shortcuts-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.lfs-shortcuts-table kbd {
    background: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    margin: 0 2px;
}

.lfs-success-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.lfs-success-section h2,
.lfs-success-section h3,
.lfs-success-section h4 {
    color: #fff;
}

.lfs-journey {
    display: flex;
    flex-direction: column;
    gap: 25px;
    margin: 25px 0;
}

.lfs-journey-phase {
    background: rgba(255,255,255,0.1);
    padding: 25px;
    border-radius: 8px;
    border-left: 4px solid #fff;
}

.lfs-phase-icon {
    font-size: 32px;
    display: inline-block;
    margin-right: 10px;
}

.lfs-phase-result {
    background: rgba(255,255,255,0.2);
    padding: 15px;
    border-radius: 6px;
    margin-top: 15px;
}

.lfs-final-message {
    background: rgba(255,255,255,0.1);
    padding: 30px;
    border-radius: 8px;
    margin-top: 30px;
    text-align: center;
}

.lfs-quick-ref {
    background: #f8f9fa;
}

.lfs-ref-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.lfs-ref-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lfs-ref-card h4 {
    margin: 0 0 15px;
    color: #3498db;
}

.lfs-compact-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.lfs-compact-list li {
    padding: 5px 0;
    font-size: 14px;
}

@media (max-width: 768px) {
    .lfs-intro-grid,
    .lfs-phases-grid,
    .lfs-method-grid,
    .lfs-tips-grid,
    .lfs-rules-grid,
    .lfs-ref-grid {
        grid-template-columns: 1fr;
    }
    
    .lfs-guide-nav {
        position: static;
    }
}

/* Print styles */
@media print {
    .lfs-guide-nav {
        display: none;
    }
    
    .lfs-guide-section {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Smooth scroll to sections
    $('.lfs-nav-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 100
        }, 500);
    });
});
</script>