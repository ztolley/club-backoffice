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
    test('a public applicant can submit a form and be reviewed in admin', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium', 'Core admin journey is desktop-only.');

        const applicant = buildApplicantJourney(testInfo);

        await submitPublicApplicant(page, applicant);
        await reviewApplicantInAdmin(page, applicant);
    });

    test('an admin can create and update a player record', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium', 'Core admin journey is desktop-only.');

        const player = buildPlayerJourney(testInfo);

        await createPlayerInAdmin(page, player);
    });

    test('a parent can sign up a player and the admin can review the record', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'chromium', 'Core admin journey is desktop-only.');

        const player = buildPlayerJourney(testInfo);

        await submitPublicPlayerSignup(page, player);
        await verifyPublicSignupPlayerInAdmin(page, player);
    });
});
