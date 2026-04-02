import { test } from '@playwright/test';
import {
    buildApplicantJourney,
    buildPlayerJourney,
    createPlayerInAdmin,
    reviewApplicantInAdmin,
    submitPublicApplicant,
    submitPublicPlayerSignup,
    verifyPublicSignupPlayerInAdmin,
} from './flows';

test.use({
    trace: 'off',
    video: 'on',
});

async function capture(page: import('@playwright/test').Page, name: string): Promise<void> {
    await page.screenshot({
        fullPage: true,
        path: test.info().outputPath(name),
    });
}

test('applicant journey review @ui-review', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name !== 'chromium', 'Desktop-only UI review coverage.');

    const applicant = buildApplicantJourney(testInfo);

    await submitPublicApplicant(page, applicant);
    await reviewApplicantInAdmin(page, applicant, async (name) => capture(page, name));
});

test('admin player journey review @ui-review', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name !== 'chromium', 'Desktop-only UI review coverage.');

    const player = buildPlayerJourney(testInfo);

    await createPlayerInAdmin(page, player);
    await capture(page, 'admin-player-review.png');
});

test('public player signup journey review @ui-review', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name !== 'chromium', 'Desktop-only UI review coverage.');

    const player = buildPlayerJourney(testInfo);

    await submitPublicPlayerSignup(page, player);
    await verifyPublicSignupPlayerInAdmin(page, player, async (name) => capture(page, name));
});
