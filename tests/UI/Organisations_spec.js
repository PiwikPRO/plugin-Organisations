/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
describe("Organisations", function () {
    this.timeout(0);

    this.fixture = "Piwik\\Plugins\\Organisations\\tests\\Fixtures\\TrackVisitsWithOrganisationsFixture";

    var reporturl = "?module=CoreHome&action=index&idSite=1&period=day&date=2013-01-23#?idSite=1&period=day&date=2013-01-23&category=General_Visitors&subcategory=Organisations_Organisation";
    var adminUrl = "?module=Organisations&action=adminIndex";

    before(function (done) {

        testEnvironment.configOverride = {
            PluginsInstalled: { PluginsInstalled: ['Organisations'] }
        };

        testEnvironment.pluginsToLoad = ['Organisations'];
        testEnvironment.save();
        done();
    });

    it('should show report', function (done) {
        expect.screenshot('report').to.be.captureSelector('.theWidgetContent', function (page) {
            page.load(reporturl);
        }, done);
    });

    it('should place Organisations in the menu', function (done) {
        expect.screenshot('menu').to.be.captureSelector('.navbar', function (page) {
        }, done);
    });

    it('should organisations management', function (done) {
        expect.screenshot('admin').to.be.captureSelector('.pageWrap', function (page) {
            page.load(adminUrl);
        }, done);
    });

    it('should show add organisation form', function (done) {
        expect.screenshot('admin_add_form').to.be.captureSelector('.pageWrap', function (page) {
            page.click('.addOrganisation');
        }, done);
    });

    it('should show invalid ip range error', function (done) {
        expect.screenshot('admin_add_invalid_range').to.be.captureSelector('.pageWrap', function (page) {
            page.sendKeys('[ng-model="organisation.name"]', 'New Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "20:0:2d0:2zf:0:0:f123:0/4");
            page.click('input[type=submit]');
            page.wait(500);
        }, done);
    });

    it('should show ip range overlapping error, when overlapping internal', function (done) {
        expect.screenshot('admin_add_overlapping_range').to.be.captureSelector('.pageWrap', function (page) {
            page.load(adminUrl);
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'New Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "20:0:2d0:2df::0/96\n20:0:2d0:2df::f123:0/118");
            page.click('input[type=submit]');
            page.wait(500);
        }, done);
    });

    it('should add organisation', function (done) {
        expect.screenshot('admin_add_success').to.be.captureSelector('.pageWrap', function (page) {
            page.load(adminUrl);
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'New Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "86.12.5.0/32\n20:0:2d0:2df::f123:0/118");
            page.click('input[type=submit]');
            page.wait(500);
        }, done);
    });

    it('should show ip range overlapping error, when overlapping with other organisation', function (done) {
        expect.screenshot('admin_add_overlapping_other').to.be.captureSelector('.pageWrap', function (page) {
            page.load(adminUrl);
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'Second Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "20:0:2d0:2df::0/96\n158.65.88.20/64");
            page.click('input[type=submit]');
            page.wait(500);
        }, done);
    });

    it('should add another organisation', function (done) {
        expect.screenshot('admin_add_success_2').to.be.captureSelector('.pageWrap', function (page) {
            page.load(adminUrl);
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'Second Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "158.65.88.20/64");
            page.click('input[type=submit]');
            page.wait(500);
        }, done);
    });

    it('should show update form', function (done) {
        expect.screenshot('admin_update_form').to.be.captureSelector('.pageWrap', function (page) {
            page.load(adminUrl);
            page.wait(500);
            page.click('[ng-click="editOrganisation()"]:eq(2)');
            page.sendKeys('[field="organisation.ipranges"]', "6.9.5.1/12\n");
        }, done);
    });

    it('should update organisation', function (done) {
        expect.screenshot('admin_update_success').to.be.captureSelector('.pageWrap', function (page) {
            page.click('input[type=submit]');
            page.wait(500);
        }, done);
    });

    it('should show delete confirmation', function (done) {
        expect.screenshot('admin_delete_confirmation').to.be.captureSelector('.modal.open', function (page) {
            page.load(adminUrl);
            page.click('[ng-click="openDeleteDialog()"]:eq(2)');
        }, done);
    });

    it('should delete organisation', function (done) {
        expect.screenshot('admin_delete_success').to.be.captureSelector('.pageWrap', function (page) {
            page.click('.modal.open a:contains(Yes)');
        }, done);
    });

});
