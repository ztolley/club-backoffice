import { expect, type Page, test } from '@playwright/test';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@example.com';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password123';

test.use({
    trace: 'off',
    video: 'on',
});

async function login(page: Page): Promise<void> {
    await page.goto('/login');

    await page.locator('[id="form.email"]').fill(adminEmail);
    await page.locator('[id="form.password"]').fill(adminPassword);
    await page.getByRole('button', { name: /sign in/i }).click();

    await expect(page).toHaveURL(/\/$/);
    await expect(page.getByRole('heading', { name: '403' })).toHaveCount(0);
}

async function capture(page: Page, name: string): Promise<void> {
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await page.screenshot({
        fullPage: true,
        path: test.info().outputPath(name),
    });
}

test('desktop UI review captures dashboard and applicants flow @ui-review', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name !== 'chromium', 'Desktop-only UI review coverage.');

    await login(page);
    await capture(page, 'desktop-dashboard.png');

    await page.goto('/applicants');
    await expect(page).toHaveURL('/applicants');
    await capture(page, 'desktop-applicants-list.png');

    const firstRow = page.locator('table tbody tr').first();

    if (await firstRow.count()) {
        await firstRow.click();
        await expect(page).toHaveURL(/\/applicants\/.+\/edit$/);
        await capture(page, 'desktop-applicant-edit.png');
    }
});

test('mobile UI review captures dashboard and applicants flow @ui-review', async ({ page }, testInfo) => {
    test.skip(testInfo.project.name !== 'mobile-chromium', 'Mobile-only UI review coverage.');

    await login(page);
    await capture(page, 'mobile-dashboard.png');

    await page.goto('/applicants');
    await expect(page).toHaveURL('/applicants');
    await capture(page, 'mobile-applicants-list.png');
});
