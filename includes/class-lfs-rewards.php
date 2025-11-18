<?php
/**
 * Rewards Class - UPPDATERAD VERSION MED DUAL-LOCK SYSTEM
 * 
 * File location: includes/class-lfs-rewards.php
 * 
 * ÄNDRINGAR:
 * - Lagt till dual-lock system: kontrollerar både poäng OCH faktiska pengar
 * - Nya metoder för att hämta faktiskt saldo från belöningskontot
 * - Förbättrad can_afford_reward() som kollar båda kriterierna
 * - Nya get_affordable_rewards() som returnerar detaljerad status
 * - Säkerhetsspärr vid inlösen - dubbelkollar faktiskt saldo
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Rewards {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // AJAX hooks
        add_action('wp_ajax_lfs_redeem_reward', array($this, 'ajax_redeem_reward'));
        add_action('wp_ajax_lfs_get_rewards_by_level', array($this, 'ajax_get_rewards_by_level'));
        add_action('wp_ajax_lfs_get_affordable_rewards', array($this, 'ajax_get_affordable_rewards'));
        add_action('wp_ajax_lfs_get_reward_budget_status', array($this, 'ajax_get_reward_budget_status'));
        
        // Cron för att återställa recurring rewards
        add_action('lfs_reset_daily_rewards', array($this, 'reset_daily_rewards'));
        
        // Registrera cron om det inte redan är schemalagt
        if (!wp_next_scheduled('lfs_reset_daily_rewards')) {
            wp_schedule_event(strtotime('tomorrow 00:00:00'), 'daily', 'lfs_reset_daily_rewards');
        }

        // General recurring reset (daily check)
        add_action('lfs_reset_recurring_rewards', array($this, 'reset_recurring_rewards'));
        if (!wp_next_scheduled('lfs_reset_recurring_rewards')) {
            wp_schedule_event(strtotime('tomorrow 00:10:00'), 'daily', 'lfs_reset_recurring_rewards');
        }
    }

    /**
     * ============================================================================
     * NYA METODER FÖR DUAL-LOCK SYSTEMET
     * ============================================================================
     */
    
    /**
     * Hämta faktiskt saldo från belöningskontot (baserat på transaktioner)
     * Detta är VERKLIGA PENGAR, inte teoretiskt värde från poäng
     */
    public function get_actual_reward_account_balance() {
        $financial = LFS_Financial::get_instance();
        $accounts = $financial->get_account_balances();
        
        // Hitta belöningskontot
        foreach ($accounts as $account) {
            if (stripos($account['name'], 'belöning') !== false) {
                return floatval($account['balance']);
            }
        }
        
        return 0;
    }
    
    /**
     * Beräkna rekommenderad månatlig överföring baserat på livsfas
     */
    public function calculate_recommended_transfer() {
        $monthly_income = floatval(get_option('lfs_monthly_income', 0));
        $current_phase = get_option('lfs_current_phase', 'survival');
        
        // Fasbaserade procentsatser
        $percentages = array(
            'survival' => 0.02,      // 2% - spara varje krona
            'stabilizing' => 0.05,   // 5% - börja unna dig mer
            'autonomy' => 0.10       // 10% - du har råd att belöna dig
        );
        
        $percentage = isset($percentages[$current_phase]) ? $percentages[$current_phase] : 0.02;
        $recommended = $monthly_income * $percentage;
        $current_balance = $this->get_actual_reward_account_balance();
        
        // Kolla om du är efter målet
        $target_balance = $recommended; // bör alltid ha minst en månads allokering
        $deficit = max(0, $target_balance - $current_balance);
        
        // Hitta senaste överföring till belöningskonto
        $last_transfer_date = $this->get_last_transfer_to_reward_account();
        $days_since_transfer = 999;
        
        if ($last_transfer_date) {
            $days_since_transfer = floor((time() - strtotime($last_transfer_date)) / (60 * 60 * 24));
        }
        
        return array(
            'recommended_monthly' => round($recommended),
            'current_balance' => $current_balance,
            'deficit' => round($deficit),
            'percentage' => $percentage * 100,
            'phase' => $current_phase,
            'status' => ($deficit > 0) ? 'below_target' : 'healthy',
            'message' => ($deficit > 0) 
                ? sprintf(__('Överför %s kr för att komma i fas', 'life-freedom-system'), number_format($deficit, 0, ',', ' '))
                : __('Din belöningsbudget är hälsosam! 🎉', 'life-freedom-system'),
            'days_since_transfer' => $days_since_transfer
        );
    }
    
    /**
     * Hitta senaste överföringen till belöningskontot
     */
    private function get_last_transfer_to_reward_account() {
        $financial = LFS_Financial::get_instance();
        $accounts = $financial->get_account_balances();
        
        // Hitta belöningskonto term ID
        $reward_account_id = null;
        $accounts_terms = get_terms(array(
            'taxonomy' => 'lfs_account',
            'hide_empty' => false,
        ));
        
        foreach ($accounts_terms as $account) {
            if (stripos($account->name, 'belöning') !== false) {
                $reward_account_id = $account->term_id;
                break;
            }
        }
        
        if (!$reward_account_id) {
            return null;
        }
        
        // Hitta senaste transaktionen till belöningskontot
        $args = array(
            'post_type' => 'lfs_transaction',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'orderby' => 'meta_value',
            'meta_key' => 'lfs_transaction_date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => 'lfs_transaction_to',
                    'value' => $reward_account_id,
                    'compare' => '=',
                ),
            ),
        );
        
        $transactions = get_posts($args);
        
        if (!empty($transactions)) {
            return get_post_meta($transactions[0]->ID, 'lfs_transaction_date', true);
        }
        
        return null;
    }
    
    /**
     * Hämta alla belöningar med detaljerad affordability-status
     * 
     * Detta är hjärtat i dual-lock systemet!
     * Returnerar lista med belöningar och varför de är låsta/tillgängliga
     */
    public function get_affordable_rewards() {
        $rewards = get_posts(array(
            'post_type' => 'lfs_reward',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'available',
                ),
            ),
        ));
        
        $calc = LFS_Calculations::get_instance();
        $points = $calc->get_current_points();
        
        $financial = LFS_Financial::get_instance();
        $reward_account_balance = $financial->get_account_balance('Belöningskonto');
        
        $milestones_helper = LFS_Milestones::get_instance();
        
        $results = array();
        
        foreach ($rewards as $reward) {
            $cost = (float) get_post_meta($reward->ID, 'lfs_reward_cost', true);
            $fp_req = (int) get_post_meta($reward->ID, 'lfs_reward_fp_required', true);
            $bp_req = (int) get_post_meta($reward->ID, 'lfs_reward_bp_required', true);
            $sp_req = (int) get_post_meta($reward->ID, 'lfs_reward_sp_required', true);
            $total_req = (int) get_post_meta($reward->ID, 'lfs_reward_total_required', true);
            
            // Check milestone requirement
            $requires_milestone = get_post_meta($reward->ID, 'lfs_reward_requires_milestone', true);
            $milestone_id = get_post_meta($reward->ID, 'lfs_reward_milestone', true);
            
            $milestone_locked = false;
            $milestone_name = '';
            
            if ($requires_milestone && $milestone_id) {
                $milestone_completed = $milestones_helper->is_milestone_completed($milestone_id);
                if (!$milestone_completed) {
                    $milestone_locked = true;
                    $milestone_post = get_post($milestone_id);
                    $milestone_name = $milestone_post ? $milestone_post->post_title : '';
                }
            }
            
            // Check points (existing logic)
            $has_points = false;
            if ($total_req > 0) {
                $has_points = $points['total'] >= $total_req;
            } else {
                $has_points = (
                    $points['fp'] >= $fp_req &&
                    $points['bp'] >= $bp_req &&
                    $points['sp'] >= $sp_req
                );
            }
            
            // Check money
            $has_money = $reward_account_balance >= $cost;
            
            // Determine status
            $status = 'affordable';
            $lock_reason = '';
            
            if ($milestone_locked) {
                $status = 'locked_milestone';
                $lock_reason = sprintf(
                    __('Kräver milstolpe: %s', 'life-freedom-system'),
                    $milestone_name
                );
            } elseif (!$has_points && !$has_money) {
                $status = 'locked_both';
                $points_missing = $total_req > 0 
                    ? ($total_req - $points['total']) 
                    : max($fp_req - $points['fp'], $bp_req - $points['bp'], $sp_req - $points['sp']);
                $money_missing = $cost - $reward_account_balance;
                $lock_reason = sprintf(
                    __('Saknar %d poäng och %s kr', 'life-freedom-system'),
                    $points_missing,
                    number_format($money_missing, 0, ',', ' ')
                );
            } elseif (!$has_points) {
                $status = 'locked_points';
                $points_missing = $total_req > 0 
                    ? ($total_req - $points['total']) 
                    : max($fp_req - $points['fp'], $bp_req - $points['bp'], $sp_req - $points['sp']);
                $lock_reason = sprintf(
                    __('Saknar %d poäng', 'life-freedom-system'),
                    $points_missing
                );
            } elseif (!$has_money) {
                $status = 'locked_money';
                $money_missing = $cost - $reward_account_balance;
                $lock_reason = sprintf(
                    __('Saknar %s kr på belöningskontot', 'life-freedom-system'),
                    number_format($money_missing, 0, ',', ' ')
                );
            }
            
            $results[] = array(
                'reward' => $reward,
                'status' => $status,
                'lock_reason' => $lock_reason,
                'has_points' => $has_points,
                'has_money' => $has_money,
                'milestone_locked' => $milestone_locked,
                'milestone_name' => $milestone_name,
            );
        }
        
        return $results;
    }
    
    /**
     * Hitta dyraste belöning användaren har råd med
     */
    public function get_most_expensive_affordable_reward() {
        $affordable = $this->get_affordable_rewards();
        $affordable_only = array_filter($affordable, function($r) {
            return $r['status'] === 'affordable';
        });
        
        if (empty($affordable_only)) {
            return null;
        }
        
        usort($affordable_only, function($a, $b) {
            return $b['cost'] - $a['cost'];
        });
        
        return $affordable_only[0];
    }

    /**
     * ============================================================================
     * UPPDATERAD REDEEM-METOD MED SÄKERHETSKONTROLL
     * ============================================================================
     */
    
    /**
     * Lös in en belöning - med dual-lock säkerhetskontroll
     */
    public function redeem_reward($reward_id) {
        $calculations = LFS_Calculations::get_instance();
        
        // Kontrollera att belöningen existerar
        $reward = get_post($reward_id);
        if (!$reward || $reward->post_type !== 'lfs_reward') {
            return new WP_Error('invalid_reward', __('Ogiltig belöning', 'life-freedom-system'));
        }
        
        // Kontrollera status
        $status = get_post_meta($reward_id, 'lfs_reward_status', true);
        $is_recurring = get_post_meta($reward_id, 'lfs_reward_recurring', true) === 'yes';
        
        if ($status === 'redeemed' && !$is_recurring) {
            return new WP_Error('already_redeemed', __('Denna belöning är redan inlöst', 'life-freedom-system'));
        }
        
        // KRITISK SÄKERHETSKONTROLL: Kolla båda kriterierna
        $cost = floatval(get_post_meta($reward_id, 'lfs_reward_cost', true));
        $current_points = $calculations->get_current_points();
        $reward_balance = $this->get_actual_reward_account_balance();
        
        $fp_req = intval(get_post_meta($reward_id, 'lfs_reward_fp_required', true));
        $bp_req = intval(get_post_meta($reward_id, 'lfs_reward_bp_required', true));
        $sp_req = intval(get_post_meta($reward_id, 'lfs_reward_sp_required', true));
        $total_req = intval(get_post_meta($reward_id, 'lfs_reward_total_required', true));
        
        // Kontrollera poäng
        $has_points = false;
        if ($total_req > 0) {
            $has_points = ($current_points['total'] >= $total_req);
        } else {
            $has_points = ($current_points['fp'] >= $fp_req &&
                          $current_points['bp'] >= $bp_req &&
                          $current_points['sp'] >= $sp_req);
        }
        
        if (!$has_points) {
            return new WP_Error('insufficient_points', __('Du har inte tillräckligt med poäng', 'life-freedom-system'));
        }
        
        // NYTT: Kontrollera faktiska pengar
        if ($cost > 0 && $reward_balance < $cost) {
            return new WP_Error(
                'insufficient_funds',
                sprintf(
                    __('Du har bara %s kr på belöningskontot, men denna belöning kostar %s kr. Överför %s kr först.', 'life-freedom-system'),
                    number_format($reward_balance, 0, ',', ' '),
                    number_format($cost, 0, ',', ' '),
                    number_format($cost - $reward_balance, 0, ',', ' ')
                )
            );
        }

        // NEW: Check milestone requirement
        $requires_milestone = get_post_meta($reward_id, 'lfs_reward_requires_milestone', true);
        $milestone_id = get_post_meta($reward_id, 'lfs_reward_milestone', true);
        
        if ($requires_milestone && $milestone_id) {
            $milestones = LFS_Milestones::get_instance();
            if (!$milestones->is_milestone_completed($milestone_id)) {
                $milestone = get_post($milestone_id);
                return array(
                    'success' => false,
                    'message' => sprintf(
                        __('Denna belöning kräver att du först når milstolpen: %s', 'life-freedom-system'),
                        $milestone->post_title
                    ),
                );
            }
        }
        
        // Dra av poäng
        if ($total_req > 0) {
            // Om totalt poäng krävs, dra av från alla tre proportionellt
            $total_current = $current_points['fp'] + $current_points['bp'] + $current_points['sp'];
            
            if ($total_current >= $total_req) {
                $fp_deduct = round(($current_points['fp'] / $total_current) * $total_req);
                $bp_deduct = round(($current_points['bp'] / $total_current) * $total_req);
                $sp_deduct = $total_req - $fp_deduct - $bp_deduct;
                
                update_option('lfs_current_fp', max(0, $current_points['fp'] - $fp_deduct));
                update_option('lfs_current_bp', max(0, $current_points['bp'] - $bp_deduct));
                update_option('lfs_current_sp', max(0, $current_points['sp'] - $sp_deduct));
            }
        } else {
            // Dra av specifika poäng
            if ($fp_req > 0) {
                update_option('lfs_current_fp', max(0, $current_points['fp'] - $fp_req));
            }
            if ($bp_req > 0) {
                update_option('lfs_current_bp', max(0, $current_points['bp'] - $bp_req));
            }
            if ($sp_req > 0) {
                update_option('lfs_current_sp', max(0, $current_points['sp'] - $sp_req));
            }
        }
        
        // NYTT: Dra av från faktiska belöningskontot via transaktion
        if ($cost > 0) {
            $financial = LFS_Financial::get_instance();
            
            // Hitta belöningskonto term ID
            $reward_account_id = null;
            $accounts = get_terms(array(
                'taxonomy' => 'lfs_account',
                'hide_empty' => false,
            ));
            
            foreach ($accounts as $account) {
                if (stripos($account->name, 'belöning') !== false) {
                    $reward_account_id = $account->term_id;
                    break;
                }
            }
            
            if ($reward_account_id) {
                // Skapa en utgiftstransaktion från belöningskontot
                $transaction_data = array(
                    'title' => sprintf(__('Belöning: %s', 'life-freedom-system'), $reward->post_title),
                    'amount' => $cost,
                    'date' => date('Y-m-d'),
                    'category' => 'expense',
                    'from_account' => $reward_account_id,
                    'budget_followed' => true, // Detta är en planerad utgift
                );
                
                $financial->create_transaction($transaction_data);
            }
        }
        
        // Uppdatera belöningens status
        update_post_meta($reward_id, 'lfs_reward_status', 'redeemed');
        update_post_meta($reward_id, 'lfs_reward_redeemed_date', current_time('timestamp'));
        
        // Om det är en recurring reward, logga inlösningen separat
        if ($is_recurring) {
            $this->log_recurring_redemption($reward_id);
        }
        
        return true;
    }

    /**
     * ============================================================================
     * BEFINTLIGA METODER (oförändrade)
     * ============================================================================
     */

    /**
     * Normalize stored reward status to canonical values.
     * Allowed canonical: 'available', 'redeemed'
     */
    private function normalize_status($raw) {
        $raw = is_string($raw) ? strtolower(trim($raw)) : '';
        if (in_array($raw, array('redeemed', 'inlöst', 'inlost'))) return 'redeemed';
        if (in_array($raw, array('available', 'tillgänglig', 'tillganglig'))) return 'available';
        return $raw ? $raw : 'available';
    }

    /**
     * Set reward status to available and clear redeemed date (+optional taxonomy mirror).
     */
    private function set_status_available($reward_id) {
        update_post_meta($reward_id, 'lfs_reward_status', 'available');
        delete_post_meta($reward_id, 'lfs_reward_redeemed_date');
        if (taxonomy_exists('lfs_reward_state')) {
            $term = get_term_by('slug', 'available', 'lfs_reward_state');
            if ($term && !is_wp_error($term)) {
                wp_set_object_terms($reward_id, (int) $term->term_id, 'lfs_reward_state', false);
            }
        }
    }

    /**
     * Sum of points earned today across all activities.
     */
    private function get_points_earned_today() {
        $now  = current_time('timestamp');
        $from = strtotime(date('Y-m-d 00:00:00', $now), $now);
        $to   = strtotime(date('Y-m-d 23:59:59', $now), $now);

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'lfs_activity_datetime',
                'value'   => array($from, $to),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ),
        );

        $args = array(
            'post_type'      => 'life_activity',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => $meta_query,
        );
        
        $args = apply_filters('lfs/points_query_args', $args);

        $ids = get_posts($args);
        $fp = $bp = $sp = 0;
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $fp += intval(get_post_meta($id, 'lfs_fp', true));
                $bp += intval(get_post_meta($id, 'lfs_bp', true));
                $sp += intval(get_post_meta($id, 'lfs_sp', true));
            }
        }
        return array('fp'=>$fp, 'bp'=>$bp, 'sp'=>$sp, 'total'=>($fp+$bp+$sp));
    }
    
    /**
     * Logga recurring reward-inlösning
     */
    private function log_recurring_redemption($reward_id) {
        $history = get_option('lfs_recurring_redemption_history', array());
        
        $history[] = array(
            'reward_id' => $reward_id,
            'reward_title' => get_the_title($reward_id),
            'date' => time(),
            'cost' => floatval(get_post_meta($reward_id, 'lfs_reward_cost', true)),
        );
        
        update_option('lfs_recurring_redemption_history', $history);
    }
    
    /**
     * Återställ dagliga recurring rewards (körs via cron)
     */
    public function reset_daily_rewards() {
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'lfs_reward_recurring',
                    'value' => 'yes',
                    'compare' => '=',
                ),
                array(
                    'key' => 'lfs_reward_recurring_frequency',
                    'value' => 'daily',
                    'compare' => '=',
                ),
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'redeemed',
                    'compare' => '=',
                ),
            ),
        );
        
        $rewards = get_posts($args);
        
        foreach ($rewards as $reward) {
            $this->set_status_available($reward->ID);
        }
    }

    /**
     * Återställ alla recurring rewards baserat på frekvens
     */
    public function reset_recurring_rewards() {
        $rewards = get_posts(array(
            'post_type'      => 'lfs_reward',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'AND',
                array('key' => 'lfs_reward_recurring', 'value' => 'yes', 'compare' => '='),
                array('key' => 'lfs_reward_status',    'value' => 'redeemed', 'compare' => '='),
            ),
        ));
        
        if (empty($rewards)) { 
            return; 
        }

        $now = current_time('timestamp');
        foreach ($rewards as $reward) {
            $freq    = get_post_meta($reward->ID, 'lfs_reward_recurring_frequency', true);
            $last_ts = (int) get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true);
            if (!$last_ts) { 
                $last_ts = $now; 
            }

            $should_reset = false;
            switch ($freq) {
                case 'daily':
                    $should_reset = (date('Y-m-d', $now) !== date('Y-m-d', $last_ts));
                    break;
                case 'weekly':
                    $should_reset = ($now - $last_ts) >= DAY_IN_SECONDS * 7;
                    break;
                case 'monthly':
                    $should_reset = ($now - $last_ts) >= DAY_IN_SECONDS * 30;
                    break;
                case 'custom':
                    $days = max(1, (int) get_post_meta($reward->ID, 'lfs_reward_recurring_interval_days', true));
                    $should_reset = ($now - $last_ts) >= DAY_IN_SECONDS * $days;
                    break;
            }

            if ($should_reset) {
                $earned = $this->get_points_earned_today();
                $needed = $this->get_required_points_total($reward->ID);
                if ($needed <= 0 || $earned['total'] >= $needed) {
                    $this->set_status_available($reward->ID);
                }
            }
        }
    }

    /**
     * Get rewards by level
     */
    public function get_rewards_by_level($level = null) {
        $args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
        );
        
        if ($level) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'lfs_reward_level',
                    'field' => 'slug',
                    'terms' => $level,
                ),
            );
        }
        
        $rewards = get_posts($args);
        $calculations = LFS_Calculations::get_instance();
        $current_points = $calculations->get_current_points();
        
        $result = array();
        foreach ($rewards as $reward) {
            $can_afford = $calculations->can_afford_reward($reward->ID);
            $status = get_post_meta($reward->ID, 'lfs_reward_status', true);
            
            $result[] = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'cost' => floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true)),
                'points_required' => intval(get_post_meta($reward->ID, 'lfs_reward_total_required', true)),
                'can_afford' => $can_afford,
                'status' => $status ? $status : 'available',
                'is_recurring' => get_post_meta($reward->ID, 'lfs_reward_recurring', true) === 'yes',
            );
        }
        
        return $result;
    }

    /**
     * Hämta inlösta belöningar (historik)
     */
    public function get_redeemed_rewards($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        // Hämta permanenta (non-recurring) inlösta belöningar
        $permanent_args = array(
            'post_type' => 'lfs_reward',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'lfs_reward_status',
                    'value' => 'redeemed',
                    'compare' => '=',
                ),
                array(
                    'key' => 'lfs_reward_recurring',
                    'value' => 'yes',
                    'compare' => '!=',
                ),
            ),
            'orderby' => 'meta_value_num',
            'meta_key' => 'lfs_reward_redeemed_date',
            'order' => 'DESC',
        );
        
        $permanent_rewards = get_posts($permanent_args);
        $result = array();
        $total_spent = 0;
        
        // Lägg till permanenta belöningar
        foreach ($permanent_rewards as $reward) {
            $cost = floatval(get_post_meta($reward->ID, 'lfs_reward_cost', true));
            $total_spent += $cost;
            
            $result[] = array(
                'id' => $reward->ID,
                'title' => $reward->post_title,
                'cost' => $cost,
                'is_recurring' => false,
                'redeemed_date' => intval(get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true)),
                'redeemed_date_formatted' => date('Y-m-d H:i', intval(get_post_meta($reward->ID, 'lfs_reward_redeemed_date', true))),
            );
        }
        
        // Lägg till recurring history
        $recurring_history = get_option('lfs_recurring_redemption_history', array());
        foreach ($recurring_history as $entry) {
            $total_spent += $entry['cost'];
            $result[] = array(
                'id' => $entry['reward_id'],
                'title' => $entry['reward_title'],
                'cost' => $entry['cost'],
                'is_recurring' => true,
                'redeemed_date' => $entry['date'],
                'redeemed_date_formatted' => date('Y-m-d H:i', $entry['date']),
            );
        }
        
        // Sortera efter datum
        usort($result, function($a, $b) {
            return $b['redeemed_date'] - $a['redeemed_date'];
        });
        
        return array(
            'rewards' => $result,
            'total_spent' => $total_spent,
        );
    }
    
    /**
     * Get required points for a reward
     */
    private function get_required_points_total($reward_id) {
        $total_req = intval(get_post_meta($reward_id, 'lfs_reward_total_required', true));
        if ($total_req > 0) return $total_req;
        $fp_req = intval(get_post_meta($reward_id, 'lfs_reward_fp_required', true));
        $bp_req = intval(get_post_meta($reward_id, 'lfs_reward_bp_required', true));
        $sp_req = intval(get_post_meta($reward_id, 'lfs_reward_sp_required', true));
        return max(0, $fp_req + $bp_req + $sp_req);
    }

    /**
     * ============================================================================
     * AJAX HANDLERS
     * ============================================================================
     */
    
    /**
     * AJAX: Lös in belöning
     */
    public function ajax_redeem_reward() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $reward_id = isset($_POST['reward_id']) ? intval($_POST['reward_id']) : 0;
        
        if (!$reward_id) {
            wp_send_json_error(__('Ogiltigt belönings-ID', 'life-freedom-system'));
        }
        
        $result = $this->redeem_reward($reward_id);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        // Hämta uppdaterade data
        $calculations = LFS_Calculations::get_instance();
        
        wp_send_json_success(array(
            'message' => __('Belöning inlöst!', 'life-freedom-system'),
            'current_points' => $calculations->get_current_points(),
            'reward_balance' => $this->get_actual_reward_account_balance(),
        ));
    }
    
    /**
     * AJAX: Hämta belöningar per nivå
     */
    public function ajax_get_rewards_by_level() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $level = isset($_POST['level']) ? sanitize_text_field($_POST['level']) : null;
        
        $rewards = $this->get_rewards_by_level($level);
        wp_send_json_success($rewards);
    }

    /**
     * NYA AJAX: Hämta affordable rewards med detaljerad status
     */
    public function ajax_get_affordable_rewards() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $level = isset($_POST['level']) ? sanitize_text_field($_POST['level']) : null;
        
        $rewards = $this->get_affordable_rewards($level);
        wp_send_json_success($rewards);
    }

    /**
     * NYA AJAX: Hämta belöningsbudget-status
     */
    public function ajax_get_reward_budget_status() {
        check_ajax_referer('lfs_nonce', 'nonce');
        
        $budget_status = $this->calculate_recommended_transfer();
        $most_expensive = $this->get_most_expensive_affordable_reward();
        
        wp_send_json_success(array(
            'budget' => $budget_status,
            'most_expensive_affordable' => $most_expensive,
        ));
    }
}
