import { expect, test } from '@playwright/test';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@example.com';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password123';

test('admin can login, open key sections, and open first record', async ({ page }) => {
    await page.goto('/login');

    await page.locator('[id="form.email"]').fill(adminEmail);
    await page.locator('[id="form.password"]').fill(adminPassword);
    await page.getByRole('button', { name: /sign in/i }).click();

    await expect(page).toHaveURL(/\/$/);

    const sections: Array<{ path: string; detailPathPattern: RegExp }> = [
        { path: '/users', detailPathPattern: /\/users\/.+\/edit$/ },
        { path: '/players', detailPathPattern: /\/players\/.+\/edit$/ },
        { path: '/teams', detailPathPattern: /\/teams\/.+\/view$/ },
        { path: '/applicants', detailPathPattern: /\/applicants\/.+\/edit$/ },
    ];

    for (const section of sections) {
        await page.goto(section.path);
        await expect(page).toHaveURL(section.path);
        await expect(page.getByRole('heading', { level: 1 })).toBeVisible();

        const firstRow = page.locator('table tbody tr').first();
        const rowCount = await page.locator('table tbody tr').count();

        if (rowCount > 0) {
            await firstRow.click();
            await expect(page).toHaveURL(section.detailPathPattern);
        }
    }
});
