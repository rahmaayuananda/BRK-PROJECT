<?php
/**
 * Simple test to check /forum/get_users endpoint output
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test /forum/get_users Endpoint</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .section { background: #252526; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #0d6efd; }
        pre { background: #1e1e1e; padding: 10px; border-radius: 3px; overflow-x: auto; color: #4ec9b0; }
        button { background: #0d6efd; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        button:hover { background: #0b5ed7; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .info { color: #9cdcfe; }
    </style>
</head>
<body>
    <h1>🔧 Test /forum/get_users Endpoint</h1>
    
    <div class="section">
        <h2>Endpoint: /forum/get_users</h2>
        <button onclick="testEndpoint()">Click to fetch users</button>
        <button onclick="clearOutput()">Clear output</button>
    </div>
    
    <div class="section">
        <h3>Response:</h3>
        <pre id="output">Waiting for response...</pre>
    </div>
    
    <div class="section">
        <h3>Parsed Data:</h3>
        <div id="parsed"></div>
    </div>

    <script>
        async function testEndpoint() {
            const output = document.getElementById('output');
            const parsed = document.getElementById('parsed');
            
            output.textContent = '⏳ Fetching...';
            parsed.innerHTML = '';
            
            try {
                const url = 'http://localhost/brk-project/index.php/forum/get_users?t=' + Date.now();
                console.log('Fetching:', url);
                
                const res = await fetch(url, {
                    cache: 'no-store',
                    headers: {
                        'Cache-Control': 'no-cache, no-store, must-revalidate'
                    }
                });
                
                const text = await res.text();
                output.textContent = text;
                
                try {
                    const json = JSON.parse(text);
                    let html = `<p class="success">✅ Valid JSON with ${json.length} users</p>`;
                    html += '<table border="1" cellpadding="10" style="border-collapse: collapse; color: white;">';
                    html += '<tr style="background: #333;"><th>ID</th><th>Username</th><th>Name</th><th>Mention Tag</th></tr>';
                    
                    json.forEach((u, idx) => {
                        html += `<tr>
                            <td>${u.id_users || '-'}</td>
                            <td>${u.username || '-'}</td>
                            <td>${u.name || '-'}</td>
                            <td><strong>${u.mention_tag || '-'}</strong></td>
                        </tr>`;
                    });
                    html += '</table>';
                    
                    parsed.innerHTML = html;
                } catch (e) {
                    parsed.innerHTML = `<p class="error">❌ Invalid JSON: ${e.message}</p>`;
                }
            } catch (e) {
                output.textContent = 'ERROR: ' + e.message;
                parsed.innerHTML = `<p class="error">❌ ${e.message}</p>`;
            }
        }
        
        function clearOutput() {
            document.getElementById('output').textContent = '';
            document.getElementById('parsed').innerHTML = '';
        }
        
        // Test on page load
        window.addEventListener('load', () => {
            setTimeout(testEndpoint, 500);
        });
    </script>
</body>
</html>
