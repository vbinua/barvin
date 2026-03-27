import {GoogleGenAI} from '@google/genai';
import fs from 'fs';
import path from 'path';
import mime from 'mime';
import axios from 'axios';
import {sendTelegramMessage} from '../../../global/services/telegram/send-telegram-message.js';

const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

export async function createColoringBook(image, clientId, orderId, domain, quizSlug, postId) {
    if (process.env.MODE === 'dev') {
        try {
            console.log(`[DEV MODE] Using mock image for task ${postId}...`);

            const filePath = path.resolve(process.cwd(), 'g1.jpg');

            const buffer = fs.readFileSync(filePath);
            const base64 = buffer.toString('base64');
            const ext = mime.getExtension('image/jpeg') || 'jpg';

            await sleep(2000);

            return {
                mimeType: 'image/jpeg',
                fileName: `coloring_${Date.now()}.${ext}`,
                image: base64
            };

        } catch (e) {
            await sendTelegramMessage(`❌ createColoringBook DEV error\n${e.message}`);
            return null;
        }
    }

    const MAX_RETRIES_DEFAULT = 3;
    const RETRY_503_DELAY = 5000;

    const ai = new GoogleGenAI({
        apiKey: process.env.GEMINIAPIKEY,
    });

    const results = [];
    let attempt = 0;
    let success = false;
    let lastError = null;
    let retry503Used = false;
    let requestText = '';

    if (quizSlug === 'new-year-style') {
        requestText = 'Создайте черно-белую раскраску в стиле Новогоднем с исключительно контурными линиями и обводками. Изображение должно быть простым и легким для раскрашивания детьми от 3 лет. Используйте только контурные линии — никаких заливок, теней, штриховки или градиентов. Линии должны быть толстыми, ровными, четкими и плавными, с ярко выраженными мультяшными чертами: большие глаза, простые и округлые формы, выразительные эмоции. Детали должны быть минималистичными, с упрощенными чертами и преувеличениями, характерными для Disney/Pixar. В дополнение к основным персонажам, добавьте новогодние элементы, такие как: Новогодние шапки (шапки Деда Мороза, снеговиков или праздничные вязаные шапки). Елки в контурном стиле, украшенные игрушками и гирляндами. Снежинки или снежные узоры. Снеговики с контурными линиями. Подарки, банты и другие атрибуты новогоднего праздника. Все элементы должны быть выполнены в стиле мультфильмов Новогоднем, где внимание уделяется ярким, простым формам и выразительным чертам персонажей. Фон должен быть легким, минималистичным, с несложными контурными рисунками, не перегружающими изображение. Избегайте мелких деталей и сложных текстур, чтобы раскраска была удобной и понятной для детей. Сохраните все элементы, которые были на оригинальной фотографии, и учтите пол персонажа на изображении. Результат должен быть полностью готов для печати и раскрашивания.';
    } else {
        requestText = 'Преобразуйте загруженную фотографию в качественную черно-белую раскраску в стиле Disney/Pixar. Используйте исключительно контурные линии и обводку — НЕ закрашивайте, НЕ заштриховывайте и НЕ заполняйте никакие области (only contours and edges, no filled-in surfaces or shapes). Разрешены только линии контуров. Внутренние области должны оставаться полностью пустыми (белыми). Никаких заливок, теней, градиентов, штриховки или серых оттенков. Используйте мягкие, плавные линии с ярко выраженными мультяшными чертами: большие глаза, простые и округлые формы, выразительные эмоции. Все детали должны быть минималистичными и очень четкими, чтобы маленьким детям (от 3 лет) было легко раскрашивать. Линии должны быть толстые, ровные и четкие, контуры плавные, с немного преувеличенными чертами, типичными для Disney/Pixar. Избегайте сложных элементов, сложных текстур и мелких деталей. Фон должен быть простым и лаконичным, также выполненным только контурными линиями, не перегружать изображение и не отвлекать внимание от персонажей. Сохраните все элементы, которые были на оригинальной фотографии. Внимательно учитывайте пол человека на изображении. Без текста, надписей и водяных знаков. Результат должен выглядеть как чистая детская раскраска, состоящая только из контуров, полностью готовая для печати и раскрашивания.';
        // text: `Преобразуйте загруженную фотографию в качественную раскраску в стиле Disney/Pixar. Используйте мягкие, плавные линии, с ярко выраженными мультяшными чертами — большие глаза, простые и округлые формы, яркие эмоции. Все детали должны быть минималистичными и очень четкими, чтобы маленьким детям (от 3 лет) было легко раскрашивать. Сделайте персонажей яркими и выразительными, избегайте сложных элементов, сложных текстур и мелких деталей. Линии должны быть толстые и четкие, контуры плавные, с немного преувеличенными чертами, типичными для Disney. Фон должен быть простым и лаконичным, не перегружать картинку, чтобы фокус был на персонажах. Без текста и водяных знаков. Сохраните все елементы что были на фото. Смотри внимательно пол человека на картинке. Это должна быть расскраска черно белая`
    }

    while (!success) {
        try {
            attempt++;

            const dockerImageUrl = image.replace('localhost:8000', 'wordpress');

            const responseImage = await axios.get(dockerImageUrl, {
                responseType: 'arraybuffer'
            });

            const inputImageBase64 = Buffer
                .from(responseImage.data, 'binary')
                .toString('base64');

            const model = 'gemini-3-pro-image-preview';
            const config = {responseModalities: ['IMAGE', 'TEXT']};

            const contents = [
                {
                    role: 'user',
                    parts: [
                        {text: requestText},
                        {
                            inlineData: {
                                data: inputImageBase64,
                                mimeType: 'image/jpeg'
                            }
                        }
                    ]
                }
            ];

            const response = await ai.models.generateContentStream({
                model,
                config,
                contents
            });

            for await (const chunk of response) {
                if (!chunk.candidates?.[0]?.content?.parts) continue;

                for (const part of chunk.candidates[0].content.parts) {
                    if (!part.inlineData) continue;

                    const ext = mime.getExtension(part.inlineData.mimeType || 'jpeg') || 'jpg';
                    const base64 = part.inlineData.data;

                    return {
                        mimeType: part.inlineData.mimeType,
                        fileName: `coloring_${Date.now()}.${ext}`,
                        image: base64
                    };
                }
            }

            success = true;

        } catch (err) {
            lastError = err;
            const status = err?.response?.status;

            if (status === 503 && !retry503Used) {
                retry503Used = true;
                await sleep(RETRY_503_DELAY);
                continue;
            }

            if (status !== 503 && attempt < MAX_RETRIES_DEFAULT) {
                continue;
            }

            break;
        }
    }

    if (!success) {
        await sendTelegramMessage(
            `❌ Failed to create coloring image
Domain: ${domain}
Client id: ${clientId}
Order id: ${orderId}
Image: ${image}
Attempts: ${attempt}
Status: ${lastError?.response?.status || 'unknown'}
Message: ${lastError?.response?.data?.error?.message || lastError?.message}`
        );

        return null;
    }

    return results;
}