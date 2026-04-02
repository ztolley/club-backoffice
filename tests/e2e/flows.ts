import { expect, type Page, type TestInfo } from '@playwright/test';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@example.com';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password123';

type ApplicantJourney = {
    address: string;
    ageGroup: string;
    dob: string;
    email: string;
    name: string;
    phone: string;
};

type PlayerJourney = {
    adminPlayerName: string;
    fan: string;
    parentAddress: string;
    parentEmail: string;
    parentName: string;
    parentPhone: string;
    playerDob: string;
    publicPlayerName: string;
    teamName: string;
};

type ScreenshotCapture = (name: string) => Promise<void>;

function slug(value: string): string {
    return value.toLowerCase().replace(/[^a-z0-9]+/g, '-');
}

function suffix(testInfo: TestInfo, prefix: string): string {
    return `${prefix}-${testInfo.project.name}-${testInfo.retry}-${Date.now()}`;
}

export function buildApplicantJourney(testInfo: TestInfo): ApplicantJourney {
    const id = suffix(testInfo, 'applicant');

    return {
        address: '1 Journey Street, Hartland',
        ageGroup: 'U14',
        dob: '2012-04-05',
        email: `${slug(id)}@example.com`,
        name: `Journey Applicant ${id}`,
        phone: '07123456789',
    };
}

export function buildPlayerJourney(testInfo: TestInfo): PlayerJourney {
    const id = suffix(testInfo, 'player');

    return {
        adminPlayerName: `Admin Player ${id}`,
        fan: `${Math.floor(100000000 + Math.random() * 899999999)}`,
        parentAddress: '10 Parent Lane, Hartland',
        parentEmail: `${slug(id)}-parent@example.com`,
        parentName: `Parent ${id}`,
        parentPhone: '07987654321',
        playerDob: '2011-06-11',
        publicPlayerName: `Signup Player ${id}`,
        teamName: 'E2E Team',
    };
}

export async function loginAsAdmin(page: Page): Promise<void> {
    await page.goto('/login');

    await page.locator('[id="form.email"]').fill(adminEmail);
    await page.locator('[id="form.password"]').fill(adminPassword);
    await page.getByRole('button', { name: /sign in/i }).click();

    await expect(page).toHaveURL(/\/$/, { timeout: 15000 });
    await expect(page.getByRole('heading', { name: '403' })).toHaveCount(0);
}

export async function submitPublicApplicant(page: Page, applicant: ApplicantJourney): Promise<void> {
    await page.goto('/player-application');

    await page.getByLabel('Player Name').fill(applicant.name);
    await page.getByLabel('Address').fill(applicant.address);
    await page.getByLabel('Email').fill(applicant.email);
    await page.getByLabel('Phone').fill(applicant.phone);
    await page.getByLabel('Date of Birth').fill(applicant.dob);
    await page.getByLabel('School').fill('Hartland Academy');
    await page.getByLabel('Current Saturday Club').fill('Hartland Saturdays');
    await page.getByLabel('Current Sunday Club').fill('Hartland Sundays');
    await page.getByLabel('Previous Clubs').fill('Village FC');
    await page.getByLabel('Playing Experience').fill('School and district football.');
    await page.getByLabel('Preferred Position').fill('CM');
    await page.getByLabel('Other Positions').fill('RB');
    await page.getByLabel('Preferred Foot').selectOption('Right');
    await page.getByLabel('Applicable Age Groups (26/27)').fill(applicant.ageGroup);
    await page.getByLabel('How Did You Hear About Us').fill('Social media');
    await page.getByLabel('Relevant Medical Conditions').fill('None');
    await page.getByLabel('Injuries').fill('No current injuries');
    await page.getByLabel('Additional Comments').fill('Looking forward to trial dates.');

    await page.getByRole('button', { name: /^submit$/i }).click();

    await expect(page).toHaveURL(/\/player-application\/success$/);
    await expect(page.getByRole('heading', { name: /thank you/i })).toBeVisible();
}

export async function reviewApplicantInAdmin(
    page: Page,
    applicant: ApplicantJourney,
    capture?: ScreenshotCapture,
): Promise<void> {
    await loginAsAdmin(page);
    await page.goto('/applicants');
    await expect(page).toHaveURL('/applicants');
    await expect(page.locator('table')).toContainText(applicant.name);

    const row = page.locator('table tbody tr').filter({ hasText: applicant.name }).first();
    await row.click();

    await expect(page).toHaveURL(/\/applicants\/.+\/edit$/);
    await expect(page.getByLabel('Name')).toHaveValue(applicant.name);
    await expect(page.getByLabel('Email Address')).toHaveValue(applicant.email);

    await page.getByLabel('Preferred Position').fill('CM - reviewed');
    await page.getByLabel('Notes').fill('Reviewed in admin during E2E flow.');

    if (capture) {
        await capture('applicant-admin-review.png');
    }

    await page.getByRole('button', { name: /save changes/i }).click();
    await expect(page.getByText(/saved/i)).toBeVisible({ timeout: 10000 });

    await page.reload();
    await expect(page.getByLabel('Preferred Position')).toHaveValue('CM - reviewed');
    await expect(page.getByLabel('Notes')).toHaveValue('Reviewed in admin during E2E flow.');
}

export async function createPlayerInAdmin(page: Page, player: PlayerJourney): Promise<void> {
    await loginAsAdmin(page);
    await page.goto('/players/create');

    await expect(page).toHaveURL('/players/create');
    await page.getByLabel('Name').fill(player.adminPlayerName);
    await page.getByLabel('Date of Birth').fill(player.playerDob);
    await page.getByLabel('FAN').fill(player.fan);
    await page.getByLabel('Preferred Position').fill('CB');
    await page.getByLabel('Other Positions').fill('LB');
    await page.getByLabel('Allowed marketing').click();
    await page.getByRole('button', { name: /^create$/i }).click();

    await expect(page).toHaveURL(/\/players\/.+\/edit$/);
    await expect(page.getByLabel('Name')).toHaveValue(player.adminPlayerName);

    await page.goto('/players');
    await expect(page.locator('table')).toContainText(player.adminPlayerName);
}

export async function submitPublicPlayerSignup(
    page: Page,
    player: PlayerJourney,
    capture?: ScreenshotCapture,
): Promise<void> {
    await page.goto('/player-signup');

    await page.getByLabel('Player Name').fill(player.publicPlayerName);
    await page.getByLabel('Player FAN').fill(player.fan);
    await page.getByLabel('Player Date of Birth').fill(player.playerDob);
    await page.getByLabel('Team').selectOption({ label: player.teamName });
    await page.getByLabel('Preferred Position').fill('RW');
    await page.getByLabel('Other Positions').fill('LW');
    await page.getByLabel('Relevant Medical Conditions').fill('None');
    await page.getByLabel('Injuries').fill('No injuries');
    await page.getByLabel('Additional Comments').fill('Parent signup E2E journey.');

    await page.getByLabel(/^Name$/).first().fill(player.parentName);
    await page.getByLabel(/^Email$/).first().fill(player.parentEmail);
    await page.getByLabel(/^Phone$/).first().fill(player.parentPhone);
    await page.getByLabel(/^Address$/).first().fill(player.parentAddress);

    await page.getByLabel(/player code of conduct/i).check();
    await page.getByLabel(/parent code of conduct/i).check();
    await page.getByLabel(/participate visually in marketing materials/i).check();

    await page.getByRole('button', { name: /^submit$/i }).click();

    await expect(page).toHaveURL(/\/player-signup\/complete$/);
    await expect(page.getByRole('heading', { name: /thank you/i })).toBeVisible();

    if (capture) {
        await capture('player-signup-success.png');
    }
}

export async function verifyPublicSignupPlayerInAdmin(page: Page, player: PlayerJourney): Promise<void> {
    await loginAsAdmin(page);
    await page.goto('/players');

    await expect(page.locator('table')).toContainText(player.publicPlayerName);
    await expect(page.locator('table')).toContainText(player.parentEmail);

    const row = page.locator('table tbody tr').filter({ hasText: player.publicPlayerName }).first();
    await row.click();

    await expect(page).toHaveURL(/\/players\/.+\/edit$/);
    await expect(page.getByLabel('Name')).toHaveValue(player.publicPlayerName);
    await expect(page.getByLabel('FAN')).toHaveValue(player.fan);
    await expect(page.getByText(player.parentName)).toBeVisible();
    await expect(page.getByText(player.parentEmail)).toBeVisible();
}
