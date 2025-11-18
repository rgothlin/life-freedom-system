<?php
/**
 * TEST AJAX ACTIONS
 * 
 * Skapa denna fil som: wp-content/plugins/life-freedom-system/test-ajax.php
 * Besök: yoursite.com/wp-content/plugins/life-freedom-system/test-ajax.php
 * 
 * VIKTIG: TA BORT efter test!
 */

require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Access denied');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test AJAX Actions</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .test { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px 20px; margin: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Test AJAX Actions</h1>
    
    <div class="test">
        <h2>1. Check if actions are registered</h2>
        <?php
        global $wp_filter;
        
        $actions = array(
            'wp_ajax_lfs_generate_transaction_now',
            'wp_ajax_lfs_toggle_recurring_transaction',
            'wp_ajax_lfs_create_recurring_transaction',
        );
        
        foreach ($actions as $action) {
            if (isset($wp_filter[$action]) && !empty($wp_filter[$action])) {
                echo '<p class="success">✅ ' . $action . ' is registered</p>';
            } else {
                echo '<p class="error">❌ ' . $action . ' is NOT registered</p>';
            }
        }
        ?>
    </div>
    
    <div class="test">
        <h2>2. Test Generate Transaction Now</h2>
        <p>Recurring ID 117: <button onclick="testGenerate(117)">Test Generate</button></p>
        <p>Recurring ID 115: <button onclick="testGenerate(115)">Test Generate</button></p>
        <div id="generate-result"></div>
    </div>
    
    <div class="test">
        <h2>3. Test Toggle</h2>
        <p>Recurring ID 117: <button onclick="testToggle(117)">Test Toggle</button></p>
        <p>Recurring ID 115: <button onclick="testToggle(115)">Test Toggle</button></p>
        <div id="toggle-result"></div>
    </div>
    
    <div class="test">
        <h2>4. Console Output</h2>
        <div id="console" style="background: #f9f9f9; padding: 10px; font-family: monospace; max-height: 400px; overflow-y: auto;"></div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    var lfsData = {
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('lfs_nonce'); ?>'
    };
    
    function log(msg) {
        console.log(msg);
        var consoleDiv = document.getElementById('console');
        consoleDiv.innerHTML += msg + '<br>';
        consoleDiv.scrollTop = consoleDiv.scrollHeight;
    }
    
    function testGenerate(recurringId) {
        log('=== Testing Generate for ID: ' + recurringId + ' ===');
        
        var data = {
            action: 'lfs_generate_transaction_now',
            nonce: lfsData.nonce,
            recurring_id: recurringId
        };
        
        log('Sending: ' + JSON.stringify(data));
        
        jQuery.post(lfsData.ajaxUrl, data)
            .done(function(response) {
                log('Response: ' + JSON.stringify(response));
                
                var resultDiv = document.getElementById('generate-result');
                if (response.success) {
                    resultDiv.innerHTML = '<p class="success">✅ Success! Transaction ID: ' + response.data.transaction_id + '</p>';
                } else {
                    resultDiv.innerHTML = '<p class="error">❌ Error: ' + response.data + '</p>';
                }
            })
            .fail(function(xhr, status, error) {
                log('FAILED: ' + error);
                log('Status: ' + status);
                log('Response: ' + xhr.responseText);
                
                document.getElementById('generate-result').innerHTML = 
                    '<p class="error">❌ AJAX Failed: ' + error + '</p>' +
                    '<pre>' + xhr.responseText + '</pre>';
            });
    }
    
    function testToggle(recurringId) {
        log('=== Testing Toggle for ID: ' + recurringId + ' ===');
        
        var data = {
            action: 'lfs_toggle_recurring_transaction',
            nonce: lfsData.nonce,
            recurring_id: recurringId,
            active: false
        };
        
        log('Sending: ' + JSON.stringify(data));
        
        jQuery.post(lfsData.ajaxUrl, data)
            .done(function(response) {
                log('Response: ' + JSON.stringify(response));
                
                var resultDiv = document.getElementById('toggle-result');
                if (response.success) {
                    resultDiv.innerHTML = '<p class="success">✅ Success! ' + response.data.message + '</p>';
                } else {
                    resultDiv.innerHTML = '<p class="error">❌ Error: ' + response.data + '</p>';
                }
            })
            .fail(function(xhr, status, error) {
                log('FAILED: ' + error);
                log('Response: ' + xhr.responseText);
                
                document.getElementById('toggle-result').innerHTML = 
                    '<p class="error">❌ AJAX Failed: ' + error + '</p>';
            });
    }
    
    log('Test page loaded');
    log('lfsData: ' + JSON.stringify(lfsData));
    </script>
    
    <div style="background: #fff3cd; padding: 20px; margin: 20px 0;">
        <strong>⚠️ VIKTIGT:</strong> Ta bort denna fil efter test!<br>
        Fil: wp-content/plugins/life-freedom-system/test-ajax.php
    </div>
</body>
</html>