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

test.describe('core journeys', () => {
    test('public applicant submission can be reviewed and edited by admin', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium', 'Core admin journey is desktop-only.');

        const applicant = buildApplicantJourney(testInfo);

        await submitPublicApplicant(page, applicant);
        await reviewApplicantInAdmin(page, applicant);
    });

    test('admin player creation and public parent signup both work end to end', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium', 'Core admin journey is desktop-only.');

        const player = buildPlayerJourney(testInfo);

        await createPlayerInAdmin(page, player);
        await submitPublicPlayerSignup(page, player);
        await verifyPublicSignupPlayerInAdmin(page, player);
    });
});
