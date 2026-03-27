import axios from 'axios';
import {sendTelegramMessage} from '../telegram/send-telegram-message.js';

export async function coloringBookSender({taskId, postId, tasks}) {
    try {
        if (!postId || !tasks) return null;

        const endpoint = process.env.APIBACKENDENDPOINT;
        const results = [];

        for (const fieldId of Object.keys(tasks)) {

            const currentTasks = tasks[fieldId];
            if (!Array.isArray(currentTasks)) continue;

            const validImages = currentTasks.filter(img => img !== null && img !== undefined && img.mimeType);

            if (validImages.length === 0) continue;

            const images = validImages.map(img =>
                `data:${img.mimeType};base64,${img.image}`
            );

            const batches = chunkArray(images, 3);

            for (let i = 0; i < batches.length; i++) {

                const payload = {
                    taskId,
                    postId,
                    tasks: {
                        [fieldId]: batches[i]
                    }
                };

                const response = await axios.post(
                    endpoint,
                    payload,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${process.env.APIBACKENDSECRETKEY}`
                        },
                        timeout: 80000
                    }
                );

                results.push(response.data);

                if (i < batches.length - 1) {
                    await new Promise(r => setTimeout(r, 800));
                }
            }
        }

        return {
            success: true,
            batches: results
        };

    } catch (error) {
        console.error('\n======= 🛑 ОШИБКА ОТПРАВКИ В WORDPRESS =======');
        console.error('URL отправки:', process.env.APIBACKENDENDPOINT);
        console.error('Статус ответа:', error.response?.status);
        console.error('Тело ответа:', error.response?.data);
        console.error('Текст ошибки:', error.message);
        console.error('================================================\n');

        await sendTelegramMessage(
            `❌ Coloring sender error\nStatus: ${error.response?.status}\n${JSON.stringify(error.response?.data) || error.message}`
        );
        return null;
    }
}

function chunkArray(arr, size) {
    const chunks = [];
    for (let i = 0; i < arr.length; i += size) {
        chunks.push(arr.slice(i, i + size));
    }
    return chunks;
}