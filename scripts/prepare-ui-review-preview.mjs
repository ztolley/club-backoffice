import { cp, mkdir, readdir, writeFile } from 'node:fs/promises';
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import os from 'node:os';
import path from 'node:path';

const execFileAsync = promisify(execFile);
let ffmpegBinaryPromise;

function parseArgs(argv) {
    const args = {};

    for (let index = 2; index < argv.length; index += 2) {
        args[argv[index].replace(/^--/, '')] = argv[index + 1];
    }

    return args;
}

async function collectFiles(directory) {
    let entries;

    try {
        entries = await readdir(directory, { withFileTypes: true });
    } catch (error) {
        if (error && typeof error === 'object' && 'code' in error && error.code === 'ENOENT') {
            return [];
        }

        throw error;
    }

    const files = await Promise.all(entries.map(async (entry) => {
        const fullPath = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            return collectFiles(fullPath);
        }

        return [fullPath];
    }));

    return files.flat();
}

function pickPreferred(paths) {
    return [...paths].sort((left, right) => {
        const leftRetry = left.includes('retry') ? 1 : 0;
        const rightRetry = right.includes('retry') ? 1 : 0;

        if (leftRetry !== rightRetry) {
            return leftRetry - rightRetry;
        }

        return left.localeCompare(right);
    })[0];
}

function markdownImage(label, url) {
    return `![${label}](${url})`;
}

async function canRun(binary) {
    if (!binary) {
        return false;
    }

    try {
        await execFileAsync(binary, ['-version']);
        return true;
    } catch {
        return false;
    }
}

async function resolveFfmpegBinary() {
    if (!ffmpegBinaryPromise) {
        ffmpegBinaryPromise = (async () => {
            const candidates = [];

            if (process.env.PLAYWRIGHT_FFMPEG) {
                candidates.push(process.env.PLAYWRIGHT_FFMPEG);
            }

            const playwrightCacheDirectory = path.join(os.homedir(), '.cache', 'ms-playwright');

            try {
                const entries = await readdir(playwrightCacheDirectory, { withFileTypes: true });
                const ffmpegDirectories = entries
                    .filter((entry) => entry.isDirectory() && entry.name.startsWith('ffmpeg-'))
                    .sort((left, right) => right.name.localeCompare(left.name));

                for (const directory of ffmpegDirectories) {
                    candidates.push(
                        path.join(playwrightCacheDirectory, directory.name, 'ffmpeg-linux'),
                        path.join(playwrightCacheDirectory, directory.name, 'ffmpeg-mac'),
                        path.join(playwrightCacheDirectory, directory.name, 'ffmpeg-win64.exe'),
                    );
                }
            } catch {
                // Ignore cache lookup failures and fall back to PATH.
            }

            candidates.push('ffmpeg');

            for (const candidate of candidates) {
                if (await canRun(candidate)) {
                    return candidate;
                }
            }

            return null;
        })();
    }

    return ffmpegBinaryPromise;
}

async function tryCreateGif(source, destination) {
    const ffmpegBinary = await resolveFfmpegBinary();

    if (!ffmpegBinary) {
        return false;
    }

    try {
        await execFileAsync(ffmpegBinary, [
            '-y',
            '-i', source,
            '-vf', 'fps=5,scale=960:-1:flags=lanczos',
            destination,
        ]);

        return true;
    } catch {
        return false;
    }
}

async function main() {
    const {
        input,
        output,
        'base-url': baseUrl,
        'comment-file': commentFile,
        'run-url': runUrl,
    } = parseArgs(process.argv);

    if (!input || !output || !baseUrl || !commentFile || !runUrl) {
        throw new Error('Missing required arguments.');
    }

    const files = await collectFiles(input);
    const screenshotNames = [
        'applicant-admin-review.png',
        'admin-player-review.png',
        'player-signup-admin-review.png',
    ];

    await mkdir(output, { recursive: true });

    const published = {};

    for (const name of screenshotNames) {
        const matches = files.filter((file) => path.basename(file) === name);

        if (!matches.length) {
            continue;
        }

        const source = pickPreferred(matches);
        const destination = path.join(output, name);
        await cp(source, destination);
        published[name] = `${baseUrl}/${name}`;
    }

    const previewAssets = [
        {
            gif: 'applicant-journey.gif',
            screenshot: 'applicant-admin-review.png',
            video: 'applicant-journey.webm',
        },
        {
            gif: 'admin-player-journey.gif',
            screenshot: 'admin-player-review.png',
            video: 'admin-player-journey.webm',
        },
        {
            gif: 'player-signup-journey.gif',
            screenshot: 'player-signup-admin-review.png',
            video: 'player-signup-journey.webm',
        },
    ];

    for (const asset of previewAssets) {
        const screenshotUrl = published[asset.screenshot];

        if (!screenshotUrl) {
            continue;
        }

        const screenshotMatch = pickPreferred(files.filter((file) => path.basename(file) === asset.screenshot));
        const videoSource = path.join(path.dirname(screenshotMatch), 'video.webm');
        const videoDestination = path.join(output, asset.video);

        await cp(videoSource, videoDestination);
        published[asset.video] = `${baseUrl}/${asset.video}`;

        const gifDestination = path.join(output, asset.gif);

        if (await tryCreateGif(videoSource, gifDestination)) {
            published[asset.gif] = `${baseUrl}/${asset.gif}`;
        }
    }

    await writeFile(path.join(output, '.nojekyll'), '');
    await writeFile(path.join(output, 'manifest.json'), JSON.stringify(published, null, 2));

    const lines = [
        '<!-- ui-review-preview -->',
        '## UI Review Preview',
        'Review these visuals before approving or merging UI-affecting changes.',
        '',
        `Workflow run: [Open run](${runUrl})`,
        '',
    ];

    const sections = [
        {
            gif: 'applicant-journey.gif',
            screenshot: 'applicant-admin-review.png',
            title: 'Applicant journey',
            video: 'applicant-journey.webm',
        },
        {
            gif: 'admin-player-journey.gif',
            screenshot: 'admin-player-review.png',
            title: 'Admin player journey',
            video: 'admin-player-journey.webm',
        },
        {
            gif: 'player-signup-journey.gif',
            screenshot: 'player-signup-admin-review.png',
            title: 'Public player signup journey',
            video: 'player-signup-journey.webm',
        },
    ];

    for (const section of sections) {
        if (!published[section.gif] && !published[section.screenshot] && !published[section.video]) {
            continue;
        }

        lines.push(`### ${section.title}`);

        if (published[section.gif]) {
            lines.push(markdownImage(`${section.title} preview`, published[section.gif]));
        } else if (published[section.screenshot]) {
            lines.push(markdownImage(`${section.title} still`, published[section.screenshot]));
        }

        if (published[section.video]) {
            lines.push('', `[Full video](${published[section.video]})`);
        }

        if (published[section.screenshot]) {
            lines.push(`[Still image](${published[section.screenshot]})`);
        }

        lines.push('');
    }

    if (!Object.keys(published).length) {
        lines.push('No preview screenshots were generated on this run. Open the workflow run for failure details.');
        lines.push('');
    }

    lines.push('Full bundle: download the `ui-review-artifacts` artifact from the workflow run.');

    await writeFile(commentFile, `${lines.join('\n')}\n`);
}

await main();
