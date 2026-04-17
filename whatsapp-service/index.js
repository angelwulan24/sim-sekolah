const express = require('express');
const cors = require('cors');
const qrcode = require('qrcode');
const { Server } = require('socket.io');
const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');

const app = express();
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const server = require('http').createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST']
    }
});

let sock;
let currentQR = null;
let isConnected = false;

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');
    const { version, isLatest } = await fetchLatestBaileysVersion();
    console.log(`Using WA v${version.join('.')}, isLatest: ${isLatest}`);

    sock = makeWASocket({
        version,
        printQRInTerminal: true,
        auth: state,
        browser: ['SIM Sekolah Gateway', 'Chrome', '1.0.0']
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('Got new QR');
            currentQR = qr;
            qrcode.toDataURL(qr, (err, url) => {
                if (!err) {
                    io.emit('qr_code', url);
                }
            });
        }

        if (connection === 'close') {
            isConnected = false;
            currentQR = null;
            io.emit('connection_status', { status: 'disconnected' });
            
            const shouldReconnect = (lastDisconnect.error)?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('Connection closed. Reconnecting:', shouldReconnect);
            
            if (shouldReconnect) {
                setTimeout(connectToWhatsApp, 3000);
            } else {
                console.log('Logged out.');
                io.emit('connection_status', { status: 'logged_out' });
            }
        } else if (connection === 'open') {
            console.log('WhatsApp connection opened!');
            isConnected = true;
            currentQR = null;
            io.emit('connection_status', { status: 'connected' });
        }
    });

    sock.ev.on('messages.upsert', async (m) => {
        // Handle incoming messages if needed
    });
}

// Socket.io for Real-time QR and Status
io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);
    
    // Send current status immediately
    if (isConnected) {
        socket.emit('connection_status', { status: 'connected' });
    } else if (currentQR) {
        qrcode.toDataURL(currentQR, (err, url) => {
            if (!err) socket.emit('qr_code', url);
        });
    }

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id);
    });
});

// API for sending messages
app.post('/message/send-text', async (req, res) => {
    try {
        const { to, text } = req.body;
        
        if (!to || !text) {
            return res.status(400).json({ status: false, message: 'Missing "to" or "text"' });
        }
        
        if (!isConnected || !sock) {
            return res.status(500).json({ status: false, message: 'WhatsApp not connected' });
        }

        let formattedNumber = to.toString().replace(/\D/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.slice(1);
        }
        if (!formattedNumber.endsWith('@s.whatsapp.net')) {
            formattedNumber += '@s.whatsapp.net';
        }

        await sock.sendMessage(formattedNumber, { text });
        console.log(`Sent message to ${formattedNumber}`);
        return res.json({ status: true, message: 'Success' });
    } catch (e) {
        console.error('Send error:', e);
        return res.status(500).json({ status: false, message: e.message });
    }
});

// Start gateway on port 5001
const PORT = 5001;
server.listen(PORT, () => {
    console.log(`Wa-Gateway replacement running on http://localhost:${PORT}`);
    connectToWhatsApp();
});
