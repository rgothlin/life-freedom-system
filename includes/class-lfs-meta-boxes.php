<?php
/**
 * MetaBox Configuration Class
 * 
 * Handles all MetaBox.io field registrations
 */

if (!defined('ABSPATH')) {
    exit;
}

class LFS_Meta_Boxes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'));
    }
    
    /**
     * Register all meta boxes
     */
    public function register_meta_boxes($meta_boxes) {
        
        // Activity Meta Boxes
        $meta_boxes[] = $this->activity_points_meta_box();
        $meta_boxes[] = $this->activity_details_meta_box();
        $meta_boxes[] = $this->activity_relations_meta_box();
        $meta_boxes[] = $this->activity_reflection_meta_box();
        
        // Project Meta Boxes
        $meta_boxes[] = $this->project_meta_box();
        
        // Reward Meta Boxes
        $meta_boxes[] = $this->reward_meta_box();
        
        // Transaction Meta Boxes
        $meta_boxes[] = $this->transaction_meta_box();
        
        // Milestone Meta Boxes
        $meta_boxes[] = $this->milestone_meta_box();
        
        // Activity Template Meta Boxes
        $meta_boxes[] = $this->activity_template_meta_box();
        
        // Settings Page Meta Boxes
        $meta_boxes[] = $this->settings_current_status_meta_box();
        $meta_boxes[] = $this->settings_goals_meta_box();
        $meta_boxes[] = $this->settings_economic_meta_box();

        // Inside register_meta_boxes() method, add:
        $meta_boxes[] = $this->recurring_transaction_meta_box();
        
        return $meta_boxes;
    }
    
    /**
     * Activity Points Meta Box
     */
    private function activity_points_meta_box() {
        return array(
            'title' => __('Poäng', 'life-freedom-system'),
            'id' => 'activity_points',
            'post_types' => array('life_activity'),
            'context' => 'normal',
            'priority' => 'high',
            'tabs' => array(
                'points' => __('Poäng', 'life-freedom-system'),
            ),
            'tab_style' => 'left',
            'fields' => array(
                array(
                    'name' => __('Freedom Points (FP)', 'life-freedom-system'),
                    'id' => 'lfs_fp',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => ' FP',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'desc' => __('Poäng för aktiviteter som driver ditt företagande och autonomi', 'life-freedom-system'),
                    'tab' => 'points',
                ),
                array(
                    'name' => __('Balance Points (BP)', 'life-freedom-system'),
                    'id' => 'lfs_bp',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 50,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => ' BP',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'desc' => __('Poäng för aktiviteter som håller dig frisk och närvarande', 'life-freedom-system'),
                    'tab' => 'points',
                ),
                array(
                    'name' => __('Stability Points (SP)', 'life-freedom-system'),
                    'id' => 'lfs_sp',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => ' SP',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'desc' => __('Poäng för aktiviteter som bygger din ekonomiska trygghet', 'life-freedom-system'),
                    'tab' => 'points',
                ),
                array(
                    'name' => __('Totalt poäng', 'life-freedom-system'),
                    'id' => 'lfs_total_points',
                    'type' => 'custom_html',
                    'callback' => array($this, 'display_total_points'),
                    'tab' => 'points',
                ),
            ),
        );
    }
    
    /**
     * Activity Details Meta Box
     */
    private function activity_details_meta_box() {
        return array(
            'title' => __('Detaljer', 'life-freedom-system'),
            'id' => 'activity_details',
            'post_types' => array('life_activity'),
            'context' => 'normal',
            'fields' => array(
                array(
                    'name' => __('Datum & Tid', 'life-freedom-system'),
                    'id' => 'lfs_activity_datetime',
                    'type' => 'datetime',
                    'timestamp' => true,
                    'js_options' => array(
                        'timeFormat' => 'HH:mm',
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'inline' => false,
                ),
                array(
                    'name' => __('Tidsåtgång', 'life-freedom-system'),
                    'id' => 'lfs_time_spent',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 5,
                    'suffix' => __('minuter', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Energinivå efter', 'life-freedom-system'),
                    'id' => 'lfs_energy_level',
                    'type' => 'slider',
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'std' => 5,
                    'suffix' => '/10',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                ),
                array(
                    'name' => __('Svårighetsgrad', 'life-freedom-system'),
                    'id' => 'lfs_difficulty',
                    'type' => 'slider',
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'std' => 5,
                    'suffix' => '/10',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                ),
            ),
        );
    }
    
    /**
     * Activity Relations Meta Box
     */
    private function activity_relations_meta_box() {
        return array(
            'title' => __('Kopplingar', 'life-freedom-system'),
            'id' => 'activity_relations',
            'post_types' => array('life_activity'),
            'context' => 'side',
            'fields' => array(
                array(
                    'name' => __('Relaterat projekt', 'life-freedom-system'),
                    'id' => 'lfs_related_project',
                    'type' => 'post',
                    'post_type' => 'lfs_project',
                    'field_type' => 'select_advanced',
                    'placeholder' => __('Välj projekt', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Relaterad milstolpe', 'life-freedom-system'),
                    'id' => 'lfs_related_milestone',
                    'type' => 'post',
                    'post_type' => 'lfs_milestone',
                    'field_type' => 'select_advanced',
                    'placeholder' => __('Välj milstolpe', 'life-freedom-system'),
                ),
            ),
        );
    }
    
    /**
     * Activity Reflection Meta Box
     */
    private function activity_reflection_meta_box() {
        return array(
            'title' => __('Reflektion', 'life-freedom-system'),
            'id' => 'activity_reflection',
            'post_types' => array('life_activity'),
            'context' => 'normal',
            'fields' => array(
                array(
                    'name' => __('Anteckningar', 'life-freedom-system'),
                    'id' => 'lfs_notes',
                    'type' => 'textarea',
                    'rows' => 4,
                ),
            ),
        );
    }
    
    /**
     * Project Meta Box
     */
    private function project_meta_box() {
        return array(
            'title' => __('Projektinformation', 'life-freedom-system'),
            'id' => 'project_info',
            'post_types' => array('lfs_project'),
            'context' => 'normal',
            'tabs' => array(
                'general' => __('Allmänt', 'life-freedom-system'),
                'progress' => __('Progress', 'life-freedom-system'),
                'financial' => __('Ekonomiskt', 'life-freedom-system'),
            ),
            'tab_style' => 'left',
            'fields' => array(
                // General tab
                array(
                    'name' => __('Status', 'life-freedom-system'),
                    'id' => 'lfs_project_status',
                    'type' => 'select',
                    'options' => array(
                        'idea' => __('Idé', 'life-freedom-system'),
                        'active' => __('Aktiv', 'life-freedom-system'),
                        'paused' => __('Pausad', 'life-freedom-system'),
                        'completed' => __('Klar', 'life-freedom-system'),
                    ),
                    'std' => 'idea',
                    'tab' => 'general',
                ),
                array(
                    'name' => __('Startdatum', 'life-freedom-system'),
                    'id' => 'lfs_project_start_date',
                    'type' => 'date',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'tab' => 'general',
                ),
                array(
                    'name' => __('Måldatum', 'life-freedom-system'),
                    'id' => 'lfs_project_target_date',
                    'type' => 'date',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'tab' => 'general',
                ),
                // Progress tab
                array(
                    'name' => __('Total FP genererad', 'life-freedom-system'),
                    'id' => 'lfs_project_total_fp',
                    'type' => 'number',
                    'readonly' => true,
                    'desc' => __('Beräknas automatiskt från aktiviteter', 'life-freedom-system'),
                    'tab' => 'progress',
                ),
                array(
                    'name' => __('FP-mål för nästa milstolpe', 'life-freedom-system'),
                    'id' => 'lfs_project_fp_goal',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                    'tab' => 'progress',
                ),
                array(
                    'name' => __('Progress (%)', 'life-freedom-system'),
                    'id' => 'lfs_project_progress',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'suffix' => '%',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'tab' => 'progress',
                ),
                // Financial tab
                array(
                    'name' => __('Inkomst hittills (kr)', 'life-freedom-system'),
                    'id' => 'lfs_project_income',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 100,
                    'prepend' => 'kr',
                    'tab' => 'financial',
                ),
                array(
                    'name' => __('Målinkomst (kr)', 'life-freedom-system'),
                    'id' => 'lfs_project_target_income',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 1000,
                    'prepend' => 'kr',
                    'tab' => 'financial',
                ),
                array(
                    'name' => __('Utgifter (kr)', 'life-freedom-system'),
                    'id' => 'lfs_project_expenses',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 100,
                    'prepend' => 'kr',
                    'tab' => 'financial',
                ),
            ),
        );
    }
    
    /**
     * Reward Meta Box
     */
    private function reward_meta_box() {
        return array(
            'title' => __('Belöningsinformation', 'life-freedom-system'),
            'id' => 'reward_info',
            'post_types' => array('lfs_reward'),
            'context' => 'normal',
            'fields' => array(
                // Kostnad och typ
                array(
                    'id'   => 'lfs_reward_cost',
                    'name' => __('Kostnad (kr)', 'life-freedom-system'),
                    'desc' => __('Hur mycket kostar denna belöning? Sätt till 0 för gratis belöningar', 'life-freedom-system'),
                    'type' => 'number',
                    'min'  => 0,
                    'step' => 1,
                    'std'  => 0,
                ),
                
                array(
                    'id'      => 'lfs_reward_type',
                    'name'    => __('Typ av belöning', 'life-freedom-system'),
                    'type'    => 'select',
                    'options' => array(
                        'psychological' => __('Psykologisk (gratis upplevelse)', 'life-freedom-system'),
                        'material'      => __('Materiell (köpa något)', 'life-freedom-system'),
                        'experience'    => __('Upplevelse (aktivitet)', 'life-freedom-system'),
                    ),
                    'std'     => 'psychological',
                ),
                
                array(
                    'type' => 'divider',
                ),
                
                // Poängkrav
                array(
                    'id'   => 'lfs_reward_fp_required',
                    'name' => __('FP krävs', 'life-freedom-system'),
                    'desc' => __('Antal Freedom Points som krävs', 'life-freedom-system'),
                    'type' => 'number',
                    'min'  => 0,
                    'std'  => 0,
                ),
                
                array(
                    'id'   => 'lfs_reward_bp_required',
                    'name' => __('BP krävs', 'life-freedom-system'),
                    'desc' => __('Antal Balance Points som krävs', 'life-freedom-system'),
                    'type' => 'number',
                    'min'  => 0,
                    'std'  => 0,
                ),
                
                array(
                    'id'   => 'lfs_reward_sp_required',
                    'name' => __('SP krävs', 'life-freedom-system'),
                    'desc' => __('Antal Stability Points som krävs', 'life-freedom-system'),
                    'type' => 'number',
                    'min'  => 0,
                    'std'  => 0,
                ),
                
                array(
                    'id'   => 'lfs_reward_total_required',
                    'name' => __('ELLER totalt poäng', 'life-freedom-system'),
                    'desc' => __('Om du fyller i detta så räcker det med totalt poäng oavsett typ', 'life-freedom-system'),
                    'type' => 'number',
                    'min'  => 0,
                    'std'  => 0,
                ),
                
                array(
                    'type' => 'divider',
                ),
                
                // NYTT: Recurring rewards
                array(
                    'id'   => 'lfs_reward_recurring',
                    'name' => __('Återkommande belöning?', 'life-freedom-system'),
                    'desc' => __('Markera om denna belöning ska återställas automatiskt efter inlösning. Perfekt för dagliga belöningar och vanor.', 'life-freedom-system'),
                    'type' => 'checkbox',
                    'std'  => 0,
                ),
                
                array(
                    'id'      => 'lfs_reward_recurring_frequency',
                    'name'    => __('Återställningsfrekvens', 'life-freedom-system'),
                    'desc'    => __('Hur ofta ska belöningen återställas?', 'life-freedom-system'),
                    'type'    => 'select',
                    'options' => array(
                        'daily'   => __('Dagligen (vid midnatt)', 'life-freedom-system'),
                        'weekly'  => __('Veckovis (på måndag)', 'life-freedom-system'),
                        'monthly' => __('Månadsvis (första dagen)', 'life-freedom-system'),
                    ),
                    'std'     => 'daily',
                    'visible' => array(
                        'when'     => array(array('lfs_reward_recurring', '=', '1')),
                        'relation' => 'and',
                    ),
                ),
                
                array(
                    'type' => 'divider',
                ),

                array(
                    'type' => 'divider',
                ),
                array(
                    'type' => 'heading',
                    'name' => __('Milstolpe-krav', 'life-freedom-system'),
                ),
                array(
                    'id'   => 'lfs_reward_requires_milestone',
                    'name' => __('Kräver milstolpe?', 'life-freedom-system'),
                    'desc' => __('Om aktiverad låses belöningen tills vald milstolpe är klar', 'life-freedom-system'),
                    'type' => 'checkbox',
                    'std'  => 0,
                ),
                array(
                    'id' => 'lfs_reward_milestone',
                    'name' => __('Vilken milstolpe?', 'life-freedom-system'),
                    'type' => 'post',
                    'post_type' => 'lfs_milestone',
                    'field_type' => 'select_advanced',
                    'placeholder' => __('Välj milstolpe', 'life-freedom-system'),
                    'desc' => __('Belöningen låses upp först när denna milstolpe är markerad som "Klar"', 'life-freedom-system'),
                    'visible' => array(
                        'when'     => array(array('lfs_reward_requires_milestone', '=', '1')),
                        'relation' => 'and',
                    ),
                ),
                
                // Status
                array(
                    'id'      => 'lfs_reward_status',
                    'name'    => __('Status', 'life-freedom-system'),
                    'type'    => 'select',
                    'options' => array(
                        'available' => __('Tillgänglig', 'life-freedom-system'),
                        'redeemed'  => __('Inlöst', 'life-freedom-system'),
                    ),
                    'std'     => 'available',
                ),
                
                array(
                    'id'       => 'lfs_reward_redeemed_date',
                    'name'     => __('Inlöst datum', 'life-freedom-system'),
                    'type'     => 'datetime',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                        'timeFormat' => 'HH:mm:ss',
                    ),
                    'visible' => array(
                        'when'     => array(array('lfs_reward_status', '=', 'redeemed')),
                        'relation' => 'and',
                    ),
                ),
            ),
        );
    }
    
    /**
     * Transaction Meta Box
     */
    private function transaction_meta_box() {
        return array(
            'title' => __('Transaktionsdetaljer', 'life-freedom-system'),
            'id' => 'transaction_details',
            'post_types' => array('lfs_transaction'),
            'context' => 'normal',
            'fields' => array(
                array(
                    'name' => __('Belopp (kr)', 'life-freedom-system'),
                    'id' => 'lfs_transaction_amount',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 1,
                    'prepend' => 'kr',
                    'required' => true,
                ),
                array(
                    'name' => __('Datum', 'life-freedom-system'),
                    'id' => 'lfs_transaction_date',
                    'type' => 'date',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'std' => date('Y-m-d'),
                ),
                array(
                    'name' => __('Från konto', 'life-freedom-system'),
                    'id' => 'lfs_transaction_from',
                    'type' => 'taxonomy_advanced',
                    'taxonomy' => 'lfs_account',
                    'field_type' => 'select_advanced',
                ),
                array(
                    'name' => __('Till konto', 'life-freedom-system'),
                    'id' => 'lfs_transaction_to',
                    'type' => 'taxonomy_advanced',
                    'taxonomy' => 'lfs_account',
                    'field_type' => 'select_advanced',
                ),
                array(
                    'name' => __('Budget följd?', 'life-freedom-system'),
                    'id' => 'lfs_transaction_budget_followed',
                    'type' => 'switch',
                    'style' => 'rounded',
                    'on_label' => __('Ja', 'life-freedom-system'),
                    'off_label' => __('Nej', 'life-freedom-system'),
                ),
                array(
                    'name' => __('SP genererade', 'life-freedom-system'),
                    'id' => 'lfs_transaction_sp',
                    'type' => 'number',
                    'readonly' => true,
                    'desc' => __('Beräknas automatiskt', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Kategori', 'life-freedom-system'),
                    'id' => 'lfs_transaction_category',
                    'type' => 'select',
                    'options' => array(
                        'salary' => __('Lön (heltidsjobb)', 'life-freedom-system'),
                        'project_income' => __('Inkomst eget projekt', 'life-freedom-system'),
                        'reward' => __('Belöning', 'life-freedom-system'),
                        'expense' => __('Utgift', 'life-freedom-system'),
                        'transfer' => __('Överföring', 'life-freedom-system'),
                        'savings' => __('Sparande', 'life-freedom-system'),
                    ),
                ),
            ),
        );
    }

    /**
     * Recurring Transaction Meta Box
     */
    private function recurring_transaction_meta_box() {
        return array(
            'title' => __('Återkommande transaktion - Detaljer', 'life-freedom-system'),
            'id' => 'recurring_transaction_details',
            'post_types' => array('lfs_recurring_trans'),
            'context' => 'normal',
            'tabs' => array(
                'basic' => __('Grundläggande', 'life-freedom-system'),
                'schedule' => __('Schema', 'life-freedom-system'),
                'status' => __('Status', 'life-freedom-system'),
            ),
            'tab_style' => 'left',
            'fields' => array(
                
                // BASIC TAB
                array(
                    'tab' => 'basic',
                    'name' => __('Belopp (kr)', 'life-freedom-system'),
                    'id' => 'lfs_recurring_amount',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 1,
                    'prepend' => 'kr',
                    'required' => true,
                    'desc' => __('Hur mycket ska betalas varje gång?', 'life-freedom-system'),
                ),
                
                array(
                    'tab' => 'basic',
                    'name' => __('Kategori', 'life-freedom-system'),
                    'id' => 'lfs_recurring_category',
                    'type' => 'select',
                    'options' => array(
                        'expense' => __('Utgift', 'life-freedom-system'),
                        'transfer' => __('Överföring', 'life-freedom-system'),
                        'savings' => __('Sparande', 'life-freedom-system'),
                        'income' => __('Inkomst', 'life-freedom-system'),
                    ),
                    'std' => 'expense',
                    'required' => true,
                ),
                
                array(
                    'tab' => 'basic',
                    'name' => __('Från konto', 'life-freedom-system'),
                    'id' => 'lfs_recurring_from_account',
                    'type' => 'taxonomy_advanced',
                    'taxonomy' => 'lfs_account',
                    'field_type' => 'select_advanced',
                    'required' => true,
                    'desc' => __('Vilket konto ska pengarna tas från?', 'life-freedom-system'),
                ),
                
                array(
                    'tab' => 'basic',
                    'name' => __('Till konto', 'life-freedom-system'),
                    'id' => 'lfs_recurring_to_account',
                    'type' => 'taxonomy_advanced',
                    'taxonomy' => 'lfs_account',
                    'field_type' => 'select_advanced',
                    'desc' => __('Vilket konto ska pengarna till? (Valfritt, lämna tomt för utgifter)', 'life-freedom-system'),
                ),
                
                // SCHEDULE TAB
                array(
                    'tab' => 'schedule',
                    'name' => __('Frekvens', 'life-freedom-system'),
                    'id' => 'lfs_recurring_frequency',
                    'type' => 'select',
                    'options' => array(
                        'weekly' => __('Veckovis', 'life-freedom-system'),
                        'monthly' => __('Månadsvis', 'life-freedom-system'),
                        'quarterly' => __('Kvartalsvis', 'life-freedom-system'),
                        'yearly' => __('Årsvis', 'life-freedom-system'),
                        'custom' => __('Anpassad', 'life-freedom-system'),
                    ),
                    'std' => 'monthly',
                    'required' => true,
                    'desc' => __('Hur ofta ska transaktionen upprepas?', 'life-freedom-system'),
                ),
                
                array(
                    'tab' => 'schedule',
                    'name' => __('Anpassat intervall (dagar)', 'life-freedom-system'),
                    'id' => 'lfs_recurring_custom_interval',
                    'type' => 'number',
                    'min' => 1,
                    'step' => 1,
                    'desc' => __('Antal dagar mellan varje transaktion (endast för anpassad frekvens)', 'life-freedom-system'),
                    'visible' => array(
                        'when' => array(array('lfs_recurring_frequency', '=', 'custom')),
                        'relation' => 'and',
                    ),
                ),
                
                array(
                    'tab' => 'schedule',
                    'name' => __('Startdatum', 'life-freedom-system'),
                    'id' => 'lfs_recurring_start_date',
                    'type' => 'date',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'std' => date('Y-m-d'),
                    'required' => true,
                    'desc' => __('När ska första transaktionen skapas?', 'life-freedom-system'),
                ),
                
                array(
                    'tab' => 'schedule',
                    'name' => __('Nästa förfallodag', 'life-freedom-system'),
                    'id' => 'lfs_recurring_next_due',
                    'type' => 'date',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'readonly' => true,
                    'desc' => __('Beräknas automatiskt', 'life-freedom-system'),
                ),
                
                // STATUS TAB
                array(
                    'tab' => 'status',
                    'name' => __('Aktiv', 'life-freedom-system'),
                    'id' => 'lfs_recurring_active',
                    'type' => 'switch',
                    'style' => 'rounded',
                    'on_label' => __('Aktiv', 'life-freedom-system'),
                    'off_label' => __('Pausad', 'life-freedom-system'),
                    'std' => '1',
                    'desc' => __('Pausa transaktionen temporärt utan att ta bort den', 'life-freedom-system'),
                ),
                
                array(
                    'tab' => 'status',
                    'name' => __('Antal genererade transaktioner', 'life-freedom-system'),
                    'id' => 'lfs_recurring_generated_count',
                    'type' => 'number',
                    'readonly' => true,
                    'std' => 0,
                    'desc' => __('Hur många gånger har denna transaktion körts?', 'life-freedom-system'),
                ),
                
                array(
                    'tab' => 'status',
                    'name' => __('Senast genererad', 'life-freedom-system'),
                    'id' => 'lfs_recurring_last_generated',
                    'type' => 'datetime',
                    'readonly' => true,
                    'desc' => __('När skapades den senaste transaktionen?', 'life-freedom-system'),
                ),
            ),
        );
    }
    
    /**
     * Activity Template Meta Box
     */
    private function activity_template_meta_box() {
        return array(
            'title' => __('Mall-inställningar', 'life-freedom-system'),
            'id' => 'activity_template_settings',
            'post_types' => array('lfs_activity_tpl'),
            'context' => 'normal',
            'priority' => 'high',
            'tabs' => array(
                'points' => __('Poäng', 'life-freedom-system'),
                'details' => __('Detaljer', 'life-freedom-system'),
                'appearance' => __('Utseende', 'life-freedom-system'),
            ),
            'tab_style' => 'left',
            'fields' => array(
                // Points tab
                array(
                    'name' => __('Freedom Points (FP)', 'life-freedom-system'),
                    'id' => 'lfs_tpl_fp',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => ' FP',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'tab' => 'points',
                ),
                array(
                    'name' => __('Balance Points (BP)', 'life-freedom-system'),
                    'id' => 'lfs_tpl_bp',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 50,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => ' BP',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'tab' => 'points',
                ),
                array(
                    'name' => __('Stability Points (SP)', 'life-freedom-system'),
                    'id' => 'lfs_tpl_sp',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => ' SP',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'tab' => 'points',
                ),
                
                // Details tab
                array(
                    'name' => __('Kategori', 'life-freedom-system'),
                    'id' => 'lfs_tpl_category',
                    'type' => 'text',
                    'placeholder' => __('t.ex. Arbete, Hemma, Träning', 'life-freedom-system'),
                    'tab' => 'details',
                ),
                array(
                    'name' => __('Aktivitetstyp', 'life-freedom-system'),
                    'id' => 'lfs_tpl_type',
                    'type' => 'text',
                    'placeholder' => __('t.ex. Deep Work, Paus, Innehåll', 'life-freedom-system'),
                    'tab' => 'details',
                ),
                array(
                    'name' => __('Arbetskontext', 'life-freedom-system'),
                    'id' => 'lfs_tpl_context',
                    'type' => 'text',
                    'placeholder' => __('t.ex. Eget projekt, Heltidsjobb, Hemma', 'life-freedom-system'),
                    'tab' => 'details',
                ),
                array(
                    'name' => __('Typisk tidsåtgång (minuter)', 'life-freedom-system'),
                    'id' => 'lfs_tpl_time_spent',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 5,
                    'std' => 0,
                    'suffix' => __('min', 'life-freedom-system'),
                    'tab' => 'details',
                ),
                
                // Appearance tab
                array(
                    'name' => __('Färg', 'life-freedom-system'),
                    'id' => 'lfs_tpl_color',
                    'type' => 'color',
                    'std' => '#3498db',
                    'desc' => __('Färg för knappen på Dashboard', 'life-freedom-system'),
                    'tab' => 'appearance',
                ),
                array(
                    'name' => __('Ikon/Emoji', 'life-freedom-system'),
                    'id' => 'lfs_tpl_icon',
                    'type' => 'text',
                    'placeholder' => __('🚀', 'life-freedom-system'),
                    'desc' => __('En emoji som representerar aktiviteten', 'life-freedom-system'),
                    'tab' => 'appearance',
                ),
            ),
        );
    }
    
    /**
     * Milestone Meta Box
     */
    private function milestone_meta_box() {
        return array(
            'title' => __('Milstolpsdetaljer', 'life-freedom-system'),
            'id' => 'milestone_details',
            'post_types' => array('lfs_milestone'),
            'context' => 'normal',
            'fields' => array(
                array(
                    'name' => __('Kopplat projekt', 'life-freedom-system'),
                    'id' => 'lfs_milestone_project',
                    'type' => 'post',
                    'post_type' => 'lfs_project',
                    'field_type' => 'select_advanced',
                    'placeholder' => __('Välj projekt', 'life-freedom-system'),
                    'required' => true,
                    'desc' => __('Vilket projekt tillhör denna milstolpe?', 'life-freedom-system'),
                ),
                array(
                    'type' => 'divider',
                ),
                array(
                    'name' => __('Milstolpstyp', 'life-freedom-system'),
                    'id' => 'lfs_milestone_type',
                    'type' => 'select',
                    'options' => array(
                        'economic' => __('Ekonomisk', 'life-freedom-system'),
                        'project' => __('Projekt', 'life-freedom-system'),
                        'lifestyle' => __('Livsstil', 'life-freedom-system'),
                    ),
                ),
                array(
                    'type' => 'divider',
                ),
                array(
                    'name' => __('Poängkrav', 'life-freedom-system'),
                    'type' => 'heading',
                ),
                array(
                    'name' => __('FP-krav', 'life-freedom-system'),
                    'id' => 'lfs_milestone_fp_required',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                ),
                array(
                    'name' => __('BP-krav', 'life-freedom-system'),
                    'id' => 'lfs_milestone_bp_required',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                ),
                array(
                    'name' => __('SP-krav', 'life-freedom-system'),
                    'id' => 'lfs_milestone_sp_required',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                ),
                array(
                    'type' => 'divider',
                ),
                array(
                    'name' => __('Belöning vid uppnående (kr)', 'life-freedom-system'),
                    'id' => 'lfs_milestone_reward',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 100,
                    'prepend' => 'kr',
                ),
                array(
                    'name' => __('Status', 'life-freedom-system'),
                    'id' => 'lfs_milestone_status',
                    'type' => 'select',
                    'options' => array(
                        'upcoming' => __('Kommande', 'life-freedom-system'),
                        'active' => __('Pågående', 'life-freedom-system'),
                        'completed' => __('Klar', 'life-freedom-system'),
                        'blocked' => __('Blockerad', 'life-freedom-system'),
                    ),
                    'std' => 'upcoming',
                ),
                array(
                    'name' => __('Deadline', 'life-freedom-system'),
                    'id' => 'lfs_milestone_deadline',
                    'type' => 'date',
                    'js_options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'desc' => __('När ska milstolpen vara klar?', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Datum uppnådd', 'life-freedom-system'),
                    'id' => 'lfs_milestone_achieved_date',
                    'type' => 'date',
                    'visible' => array('lfs_milestone_status', '=', 'achieved'),
                ),
            ),
        );
    }
    
    /**
     * Settings - Current Status Meta Box
     */
    private function settings_current_status_meta_box() {
        return array(
            'title' => __('Aktuell status', 'life-freedom-system'),
            'id' => 'settings_status',
            'settings_pages' => array('lfs-settings'),
            'fields' => array(
                array(
                    'name' => __('Nuvarande livsfas', 'life-freedom-system'),
                    'id' => 'lfs_current_phase',
                    'type' => 'select',
                    'options' => array(
                        'survival' => __('Survival - Överleva och stabilisera', 'life-freedom-system'),
                        'stabilizing' => __('Stabilisering - Bygga buffert', 'life-freedom-system'),
                        'autonomy' => __('Autonomi - Full frihet', 'life-freedom-system'),
                    ),
                    'std' => 'survival',
                    'desc' => __('Din fas påverkar poängvärde och belöningsbudget', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Poäng per krona', 'life-freedom-system'),
                    'id' => 'lfs_points_per_kr',
                    'type' => 'number',
                    'step' => 0.1,
                    'std' => 0.5,
                    'desc' => __('Hur många kronor 10 poäng är värda (Survival: 0.5, Stabilisering: 0.8, Autonomi: 1.0)', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Streak (dagar i rad)', 'life-freedom-system'),
                    'id' => 'lfs_streak_days',
                    'type' => 'number',
                    'readonly' => true,
                    'desc' => __('Beräknas automatiskt', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Dagar sedan sista "läcka"', 'life-freedom-system'),
                    'id' => 'lfs_days_since_leak',
                    'type' => 'number',
                    'readonly' => true,
                    'desc' => __('Dagar sedan du flyttade pengar från sparkonton', 'life-freedom-system'),
                ),
            ),
        );
    }
    
    /**
     * Settings - Goals Meta Box
     */
    private function settings_goals_meta_box() {
        return array(
            'title' => __('Veckomål', 'life-freedom-system'),
            'id' => 'settings_goals',
            'settings_pages' => array('lfs-settings'),
            'fields' => array(
                array(
                    'name' => __('FP-mål per vecka', 'life-freedom-system'),
                    'id' => 'lfs_weekly_fp_goal',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                    'std' => 500,
                ),
                array(
                    'name' => __('BP-mål per vecka', 'life-freedom-system'),
                    'id' => 'lfs_weekly_bp_goal',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                    'std' => 300,
                ),
                array(
                    'name' => __('SP-mål per vecka', 'life-freedom-system'),
                    'id' => 'lfs_weekly_sp_goal',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 50,
                    'std' => 400,
                ),
            ),
        );
    }
    
    /**
     * Settings - Economic Meta Box
     */
    private function settings_economic_meta_box() {
        return array(
            'title' => __('Ekonomiska inställningar', 'life-freedom-system'),
            'id' => 'settings_economic',
            'settings_pages' => array('lfs-settings'),
            'fields' => array(
                array(
                    'name' => __('Månadsbudget följd denna månad?', 'life-freedom-system'),
                    'id' => 'lfs_budget_followed',
                    'type' => 'switch',
                    'style' => 'rounded',
                    'on_label' => __('Ja', 'life-freedom-system'),
                    'off_label' => __('Nej', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Belöningskonto % av inkomst', 'life-freedom-system'),
                    'id' => 'lfs_reward_account_percent',
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 15,
                    'step' => 1,
                    'std' => 2,
                    'suffix' => '%',
                    'js_options' => array(
                        'tooltip' => 'always',
                    ),
                    'desc' => __('Rekommenderat: Survival 2-3%, Stabilisering 5%, Autonomi 7-10%', 'life-freedom-system'),
                ),
                array(
                    'name' => __('Månadsinkomst (kr)', 'life-freedom-system'),
                    'id' => 'lfs_monthly_income',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 1000,
                    'prepend' => 'kr',
                ),
            ),
        );
    }
    
    /**
     * Display total points (callback for custom HTML)
     */
    public function display_total_points() {
        global $post;
        $fp = get_post_meta($post->ID, 'lfs_fp', true);
        $bp = get_post_meta($post->ID, 'lfs_bp', true);
        $sp = get_post_meta($post->ID, 'lfs_sp', true);
        $total = intval($fp) + intval($bp) + intval($sp);
        
        echo '<div class="lfs-total-points">';
        echo '<h3>' . esc_html__('Total: ', 'life-freedom-system') . '<span class="lfs-total-value">' . esc_html($total) . '</span> ' . esc_html__('poäng', 'life-freedom-system') . '</h3>';
        echo '</div>';
    }
}