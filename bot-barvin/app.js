import express from 'express';
import path from 'path';
import dotenv from 'dotenv';

import {createColoringBookRoute} from './src/capabilities/coloring-book/controllers/coloring-book.js';

dotenv.config({quiet: true});

const app = express();

import {fileURLToPath} from 'url';
import {dirname} from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

app.use(express.json());
app.use(express.static(path.resolve(__dirname, 'public')));
app.use('/images', express.static(path.join(__dirname, 'images')));

app.use('/api/v1/create-coloring-book', createColoringBookRoute);

const PORT = process.env.PORT || 4550;
app.listen(PORT, () => console.log(`Server running at http://localhost:${PORT}`));
