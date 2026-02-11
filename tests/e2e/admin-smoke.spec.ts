import { expect, test } from '@playwright/test';

const adminEmail = process.env.E2E_ADMIN_EMAIL ?? 'admin@example.com';
const adminPassword = process.env.E2E_ADMIN_PASSWORD ?? 'password123';

test('admin can login, open key sections, and open first record', async ({ page }) => {
    await page.goto('/login');

    await page.locator('[id="form.email"]').fill(adminEmail);
    await page.locator('[id="form.password"]').fill(adminPassword);
    await page.getByRole('button', { name: /sign in/i }).click();

    await expect(page).toHaveURL(/\/$/);

    await expect(page.locator('a[href$="/users"]')).toBeVisible();
    await expect(page.locator('a[href$="/players"]')).toBeVisible();
    await expect(page.locator('a[href$="/teams"]')).toBeVisible();
    await expect(page.locator('a[href$="/applicants"]')).toBeVisible();

    const sections: Array<{ path: string; detailPathPattern: RegExp }> = [
        { path: '/users', detailPathPattern: /\/users\/.+\/edit$/ },
        { path: '/players', detailPathPattern: /\/players\/.+\/edit$/ },
        { path: '/teams', detailPathPattern: /\/teams\/.+\/view$/ },
        { path: '/applicants', detailPathPattern: /\/applicants\/.+\/edit$/ },
    ];

    for (const section of sections) {
        await page.goto(section.path);
        await expect(page).toHaveURL(section.path);
        await expect(page.locator('table').first()).toBeVisible();

        const firstRecordLink = page.locator('table tbody tr a').first();
        await expect(firstRecordLink).toBeVisible();
        await firstRecordLink.click();
        await expect(page).toHaveURL(section.detailPathPattern);
    }
});
