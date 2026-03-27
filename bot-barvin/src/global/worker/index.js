import {Queue} from 'bullmq';
import IORedis from 'ioredis';

export const connection = new IORedis({
    host: 'redis',
    port: 6379,
    maxRetriesPerRequest: null,
});

export const coloringBookQueue = new Queue('coloringBookQueue', {connection});
