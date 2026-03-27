import express from 'express';
import rateLimit from 'express-rate-limit';
import dotenv from 'dotenv';
import {coloringBookQueue} from '../../../global/worker/index.js';
import {generateId} from '../../../global/services/generate-id/generate-id.js';
import {batchStore} from '../services/batch-store.js';
import {sendTelegramMessage} from '../../../global/services/telegram/send-telegram-message.js';

dotenv.config({quiet: true});

const router = express.Router();

const limiter = rateLimit({
    windowMs: 60 * 1000,
    max: 300,
    message: 'Too many requests',
});
router.use(limiter);

router.post('/', async (req, res) => {
    try {
        const providedApiKey = req.headers.authorization?.split(' ')[1];
        if (providedApiKey !== process.env.APISECRETKEY) {
            await sendTelegramMessage('❌ Invalid API key in createColoringBookRoute');
            return res.status(401).json({success: false});
        }

        const tasks = req.body;
        if (!Array.isArray(tasks) || !tasks.length) {
            await sendTelegramMessage('❌ Empty or invalid tasks payload');
            return res.status(400).json({success: false});
        }

        console.log(tasks)

        const batchId = generateId();
        const taskId = generateId();

        batchStore.set(batchId, {
            postId: tasks[0].postId,
            taskId,
            tasks: {},
            completedTasks: 0,
            totalTasks: tasks.length
        });

        for (const task of tasks) {
            const jobData = {...task, batchId, taskId};
            await coloringBookQueue.add('coloringBatch', jobData, {
                removeOnComplete: true,
                removeOnFail: true
            });
        }

        res.json({success: true, batchId});

    } catch (e) {
        console.error(e);
        await sendTelegramMessage(`❌ createColoringBookRoute error\n${e.message}`);
        res.status(500).json({success: false});
    }
});

export const createColoringBookRoute = router;
