const http = require('http');
const url = require('url');
const WebSocket = require('ws');

const PORT = process.env.PORT || 8080;

const server = http.createServer((req, res) => {
  const parsed = url.parse(req.url, true);
  if (req.method === 'GET' && parsed.pathname === '/notify') {
    const type = parsed.query.type || 'unknown';
    let data = parsed.query.data || parsed.query.message || null;
    try {
      if (typeof data === 'string' && data !== '') data = JSON.parse(data);
    } catch (e) {
      // keep raw
    }
    const payload = JSON.stringify({ type, data });
    // broadcast to all websocket clients
    wss.clients.forEach(client => {
      if (client.readyState === WebSocket.OPEN) {
        client.send(payload);
      }
    });
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ ok: true }));
    return;
  }
  res.writeHead(404);
  res.end();
});

const wss = new WebSocket.Server({ server });

wss.on('connection', (ws, req) => {
  console.log('WS client connected');
  ws.on('message', (msg) => {
    // optional: handle client messages
    console.log('Received from client:', msg.toString());
  });
});

server.listen(PORT, () => {
  console.log('WebSocket/notify server listening on port', PORT);
});
