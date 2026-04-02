import { cp, mkdir, readdir, writeFile } from 'node:fs/promises';
import path from 'node:path';

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
        'desktop-dashboard.png',
        'desktop-applicants-list.png',
        'desktop-applicant-edit.png',
        'mobile-dashboard.png',
        'mobile-applicants-list.png',
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

    const videoCandidates = {
        'desktop-flow.webm': files.filter((file) => path.basename(file) === 'video.webm' && file.includes('chromium') && !file.includes('mobile-chromium')),
        'mobile-flow.webm': files.filter((file) => path.basename(file) === 'video.webm' && file.includes('mobile-chromium')),
    };

    for (const [name, matches] of Object.entries(videoCandidates)) {
        if (!matches.length) {
            continue;
        }

        const source = pickPreferred(matches);
        const destination = path.join(output, name);
        await cp(source, destination);
        published[name] = `${baseUrl}/${name}`;
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

    if (published['desktop-dashboard.png'] || published['desktop-applicants-list.png'] || published['desktop-applicant-edit.png']) {
        lines.push('### Desktop');

        if (published['desktop-dashboard.png']) {
            lines.push(markdownImage('Desktop dashboard', published['desktop-dashboard.png']));
        }

        if (published['desktop-applicants-list.png']) {
            lines.push(markdownImage('Desktop applicants list', published['desktop-applicants-list.png']));
        }

        if (published['desktop-applicant-edit.png']) {
            lines.push(markdownImage('Desktop applicant edit', published['desktop-applicant-edit.png']));
        }

        if (published['desktop-flow.webm']) {
            lines.push('', `[Desktop flow video](${published['desktop-flow.webm']})`);
        }

        lines.push('');
    }

    if (published['mobile-dashboard.png'] || published['mobile-applicants-list.png']) {
        lines.push('### Mobile');

        if (published['mobile-dashboard.png']) {
            lines.push(markdownImage('Mobile dashboard', published['mobile-dashboard.png']));
        }

        if (published['mobile-applicants-list.png']) {
            lines.push(markdownImage('Mobile applicants list', published['mobile-applicants-list.png']));
        }

        if (published['mobile-flow.webm']) {
            lines.push('', `[Mobile flow video](${published['mobile-flow.webm']})`);
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
