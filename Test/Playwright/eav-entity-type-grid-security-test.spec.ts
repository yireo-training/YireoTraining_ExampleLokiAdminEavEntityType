import {expect, test} from '@loki/test';
import type {Page, Route} from '@playwright/test';

const GRID_URL = '/loki_admin_eav_entity_type/index/grid/';
const AJAX_URL = '**/loki_components/index/html**';

const env = (name: string, fallback: string): string => process.env[name]?.trim() || fallback;
const adminUrl = (path: string) => '/' + env('MAGENTO_BACKEND_FRONTNAME', 'admin') + path;

async function adminLogin(page: Page) {
    await page.goto(adminUrl(''));
    await page.locator('#username').fill(env('MAGENTO_ADMIN_USER', 'admin@loki-checkout.com'));
    await page.locator('#login').fill(env('MAGENTO_ADMIN_PASSWORD', 'P@ssw0rd1234'));
    await page.locator('.action-login').click();
    await page.waitForLoadState('domcontentloaded');

    test.skip(/\/tfa\//.test(page.url()), 'Two-factor authentication is enabled for the Magento Admin');
    await expect(page.locator('.page-header .admin-user')).toBeVisible();
}

async function openGrid(page: Page) {
    await page.goto(adminUrl(GRID_URL));
    await expect(page.locator('.page-title')).toContainText('EAV Entity Types');
    await expect(page.locator('table.data-grid tbody tr.data-row').first()).toBeVisible();
    await page.waitForLoadState('networkidle');
}

async function rewriteNextUpdate(page: Page, rewrite: (update: Record<string, any>) => Record<string, any>) {
    await page.route(AJAX_URL, async (route: Route) => {
        const body = JSON.parse(route.request().postData() ?? '{}');
        body.updates = (body.updates ?? []).map((update: Record<string, any>) => ({
            ...update,
            update: rewrite(update.update ?? {}),
        }));

        await route.continue({postData: JSON.stringify(body)});
    }, {times: 1});
}

async function getCellText(page: Page, rowId: string, columnCode: string): Promise<string> {
    const headers = page.locator('table.data-grid thead th');
    const columnIndex = await headers.evaluateAll(
        (elements, code) => elements.findIndex(element => element.getAttribute('data-column') === code),
        columnCode
    );
    expect(columnIndex, `Column "${columnCode}" is shown`).toBeGreaterThan(0);

    const row = page.locator('tr.data-row', {has: page.locator(`input[data-row-id="${rowId}"]`)});
    return (await row.locator('td').nth(columnIndex).innerText()).trim();
}

test.describe('YireoTraining_ExampleLokiAdminEavEntityType grid security', () => {
    test.beforeEach(async ({page}) => {
        page.setCatchErrors(false);
        await adminLogin(page);
        await openGrid(page);
    });

    test('should sort by a known column', async ({page}) => {
        const response = page.waitForResponse(AJAX_URL);
        await page.locator('th[data-column="entity_type_code"]').click();
        expect((await response).ok()).toBeTruthy();

        await expect(page.locator('th[data-column="entity_type_code"]')).toHaveClass(/_(ascend|descend)/);
    });

    test('should ignore an SQL expression as sort field', async ({page}) => {
        await rewriteNextUpdate(page, update => ({
            ...update,
            sort: {sortBy: '(SELECT SLEEP(5))', sortDirection: 'ASC'},
        }));

        const startedAt = Date.now();
        const response = page.waitForResponse(AJAX_URL);
        await page.locator('th[data-column="entity_type_code"]').click();
        expect((await response).ok()).toBeTruthy();
        expect(Date.now() - startedAt, 'The injected SLEEP() did not run').toBeLessThan(4000);

        await openGrid(page);
        await expect(page.locator('th._ascend, th._descend')).toHaveCount(0);
    });

    test('should not allow an inline edit on this read-only grid', async ({page}) => {
        const originalValue = await getCellText(page, '1', 'increment_per_store');
        const injectedValue = originalValue === '1' ? '0' : '1';

        await rewriteNextUpdate(page, () => ({
            inline_edit: {identifier: '1', property: 'increment_per_store', value: injectedValue},
        }));

        const response = page.waitForResponse(AJAX_URL);
        await page.locator('th[data-column="entity_type_code"]').click();
        await response;

        await openGrid(page);
        expect(await getCellText(page, '1', 'increment_per_store')).toBe(originalValue);
    });
});
