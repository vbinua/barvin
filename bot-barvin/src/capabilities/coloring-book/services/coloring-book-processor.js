import {Worker} from 'bullmq';
import {connection} from '../../../global/worker/index.js';
import {batchStore} from './batch-store.js';
import {sendTelegramMessage} from '../../../global/services/telegram/send-telegram-message.js';
import {createColoringBook} from './create-coloring-book.js';
import {coloringBookSender} from '../../../global/services/sending/send-сoloring-books.js';

export const worker = new Worker(
    'coloringBookQueue',
    async job => {
        try {
            const {images, clientId, orderId, domain, quizSlug, postId, acfField, batchId, taskId} = job.data;
            const results = [];

            for (const image of images) {
                const result = await createColoringBook(image, clientId, orderId, domain, quizSlug, postId);
                results.push(result);
            }

            return {batchId, taskId, postId, domain, acfField, images: results};
        } catch (e) {
            await sendTelegramMessage(`❌ Worker job processing error\nJob ID: ${job.id}\n${e.message}`);
            throw e;
        }
    },
    {connection, concurrency: 1}
);

worker.on('completed', async (job, result) => {
    try {
        const {batchId, acfField, images, postId, taskId} = result;

        if (!batchStore.has(batchId)) {
            batchStore.set(batchId, {tasks: {}, completedTasks: 0, totalTasks: 1, postId, taskId});
        }

        const batch = batchStore.get(batchId);

        batch.tasks[acfField] = images;
        batch.completedTasks++;

        if (batch.completedTasks >= batch.totalTasks) {
            const senderResult = await coloringBookSender({taskId, postId, tasks: batch.tasks});

            if (!senderResult) {
                await sendTelegramMessage(`❌ coloringBookSender returned null\nBatch: ${batchId}\nPost: ${postId}`);
            }

            batchStore.delete(batchId);
            console.log(`✅ Batch ${batchId} fully completed`);
        }

    } catch (e) {
        console.error(e);
        await sendTelegramMessage(`❌ Worker completed handler error\n${e.message}`);
    }
});

worker.on('failed', async (job, err) => {
    console.error(`❌ Job ${job.id} failed: ${err.message}`);
    await sendTelegramMessage(`❌ Coloring job failed\nJob ID: ${job.id}\nBatch: ${job.data?.batchId}\nError: ${err.message}`);
});
