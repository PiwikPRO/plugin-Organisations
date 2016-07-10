
describe("Organisations", function () {
    this.timeout(0);

    it('should organisations management', function (done) {
        expect.screenshot('admin').to.be.captureSelector('#content', function (page) {
            page.load("?module=Organisations&action=adminIndex");
        }, done);
    });

    it('should show add organisation form', function (done) {
        expect.screenshot('admin_add_form').to.be.captureSelector('#content', function (page) {
            page.click('.addOrganisation');
        }, done);
    });

    it('should show invalid ip range error', function (done) {
        expect.screenshot('admin_add_invalid_range').to.be.captureSelector('#content', function (page) {
            page.sendKeys('[ng-model="organisation.name"]', 'New Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "20:0:2d0:2zf:0:0:f123:0/4");
            page.click('input[type=submit]');
        }, done);
    });

    it('should show ip range overlapping error, when overlapping internal', function (done) {
        expect.screenshot('admin_add_overlapping_range').to.be.captureSelector('#content', function (page) {
            page.load("?module=Organisations&action=adminIndex");
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'New Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "20:0:2d0:2df::0/96\n20:0:2d0:2df::f123:0/118");
            page.click('input[type=submit]');
        }, done);
    });

    it('should add organisation', function (done) {
        expect.screenshot('admin_add_success').to.be.captureSelector('#content', function (page) {
            page.load("?module=Organisations&action=adminIndex");
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'New Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "86.12.5.0/32\n20:0:2d0:2df::f123:0/118");
            page.click('input[type=submit]');
        }, done);
    });

    it('should show ip range overlapping error, when overlapping with other organisation', function (done) {
        expect.screenshot('admin_add_overlapping_other').to.be.captureSelector('#content', function (page) {
            page.load("?module=Organisations&action=adminIndex");
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'Second Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "20:0:2d0:2df::0/96\n158.65.88.20/64");
            page.click('input[type=submit]');
        }, done);
    });

    it('should add another organisation', function (done) {
        expect.screenshot('admin_add_success_2').to.be.captureSelector('#content', function (page) {
            page.load("?module=Organisations&action=adminIndex");
            page.click('.addOrganisation');
            page.sendKeys('[ng-model="organisation.name"]', 'Second Organisation');
            page.sendKeys('[field="organisation.ipranges"]', "158.65.88.20/64");
            page.click('input[type=submit]');
        }, done);
    });

    it('should show update form', function (done) {
        expect.screenshot('admin_update_form').to.be.captureSelector('#content', function (page) {
            page.load("?module=Organisations&action=adminIndex");
            page.click('[ng-click="editOrganisation()"]:nth-child(1)');
            page.sendKeys('[field="organisation.ipranges"]', "\n6.9.5.1/12");
        }, done);
    });

    it('should show update form', function (done) {
        expect.screenshot('admin_update_success').to.be.captureSelector('#content', function (page) {
            page.click('input[type=submit]');
        }, done);
    });

});
