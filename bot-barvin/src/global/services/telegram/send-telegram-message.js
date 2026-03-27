import axios from 'axios';
import dotenv from 'dotenv';

dotenv.config({quiet: true});

function escapeTelegramHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

export async function sendTelegramMessage(message) {
    const chatId = process.env.TELEGRAMCHATID;
    const token = process.env.TELEGRAMBOTTOKEN;
    const url = `https://api.telegram.org/bot${token}/sendMessage`;

    let safeMessage = message.toString().slice(0, 4000);
    safeMessage = escapeTelegramHtml(safeMessage);

    try {
        await axios.post(url, {
            chat_id: chatId,
            text: safeMessage,
            parse_mode: 'HTML'
        });
    } catch (err) {
        console.error('Error sending message to Telegram:', err);
    }
}
