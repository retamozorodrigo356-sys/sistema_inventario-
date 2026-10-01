/**
 * Comprueba que las capturas de docs/capturas sean PNG válidos y no estén en blanco.
 *
 * Uso:  node tools/check-pngs.mjs
 */

import fs from 'node:fs';
import path from 'node:path';
import zlib from 'node:zlib';

const DIR = path.join(process.cwd(), 'docs', 'capturas');

function decodePng(file) {
    const buffer = fs.readFileSync(file);
    const signature = Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]);
    if (!buffer.subarray(0, 8).equals(signature)) throw new Error('firma PNG inválida');

    let offset = 8;
    let width = 0;
    let height = 0;
    let bitDepth = 0;
    let colorType = 0;
    let interlace = 0;
    const idat = [];

    while (offset < buffer.length) {
        const length = buffer.readUInt32BE(offset);
        const type = buffer.toString('ascii', offset + 4, offset + 8);
        const data = buffer.subarray(offset + 8, offset + 8 + length);

        if (type === 'IHDR') {
            width = data.readUInt32BE(0);
            height = data.readUInt32BE(4);
            bitDepth = data[8];
            colorType = data[9];
            interlace = data[12];
        } else if (type === 'IDAT') {
            idat.push(data);
        } else if (type === 'IEND') {
            break;
        }

        offset += 12 + length;
    }

    if (bitDepth !== 8 || interlace !== 0) throw new Error(`perfil no soportado (bitDepth=${bitDepth}, interlace=${interlace})`);

    const channels = colorType === 6 ? 4 : colorType === 2 ? 3 : colorType === 0 ? 1 : -1;
    if (channels === -1) throw new Error(`colorType=${colorType} no soportado`);

    const raw = zlib.inflateSync(Buffer.concat(idat));
    const stride = width * channels;
    const pixels = Buffer.alloc(stride * height);
    let source = 0;

    for (let y = 0; y < height; y++) {
        const filter = raw[source++];
        const line = raw.subarray(source, source + stride);
        source += stride;
        const current = pixels.subarray(y * stride, (y + 1) * stride);
        const previous = y > 0 ? pixels.subarray((y - 1) * stride, y * stride) : null;

        for (let x = 0; x < stride; x++) {
            const left = x >= channels ? current[x - channels] : 0;
            const up = previous ? previous[x] : 0;
            const upLeft = previous && x >= channels ? previous[x - channels] : 0;
            const value = line[x];

            let result;
            switch (filter) {
                case 0: result = value; break;
                case 1: result = value + left; break;
                case 2: result = value + up; break;
                case 3: result = value + Math.floor((left + up) / 2); break;
                case 4: {
                    const p = left + up - upLeft;
                    const pa = Math.abs(p - left);
                    const pb = Math.abs(p - up);
                    const pc = Math.abs(p - upLeft);
                    const predict = pa <= pb && pa <= pc ? left : pb <= pc ? up : upLeft;
                    result = value + predict;
                    break;
                }
                default: throw new Error(`filtro desconocido: ${filter}`);
            }

            current[x] = result & 0xff;
        }
    }

    return { width, height, channels, pixels, stride };
}

const files = fs.readdirSync(DIR).filter((file) => file.endsWith('.png')).sort();
let failures = 0;

for (const file of files) {
    try {
        const { width, height, channels, pixels, stride } = decodePng(path.join(DIR, file));
        const colors = new Set();
        let dark = 0;
        let sampled = 0;

        for (let y = 0; y < height; y += 8) {
            for (let x = 0; x < width; x += 8) {
                const i = y * stride + x * channels;
                const r = pixels[i];
                const g = channels > 1 ? pixels[i + 1] : r;
                const b = channels > 2 ? pixels[i + 2] : r;
                colors.add(`${r >> 4}-${g >> 4}-${b >> 4}`);
                if ((r + g + b) / 3 < 200) dark++;
                sampled++;
            }
        }

        const variety = colors.size;
        const contentRatio = dark / sampled;
        const blank = variety < 10 || contentRatio < 0.02;

        if (blank) failures++;
        console.log(
            `${blank ? 'FALLA' : 'OK   '} ${file}  ${width}x${height}  colores=${variety}  contenido=${(contentRatio * 100).toFixed(1)}%`
        );
    } catch (error) {
        failures++;
        console.log(`FALLA ${file}  →  ${error.message}`);
    }
}

console.log(failures === 0 ? `\n${files.length} capturas válidas.` : `\n${failures} captura(s) con problemas.`);
process.exitCode = failures === 0 ? 0 : 1;
