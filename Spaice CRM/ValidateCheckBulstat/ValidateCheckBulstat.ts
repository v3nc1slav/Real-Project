/**
 * @copyright: 
 * @jira: T-169
 * @jira: T-170
 * @author: Ventsislav Verchov 
 */
 
///$$                           /$$          
// $$                          | $$          
// $$  /$$$$$$   /$$$$$$   /$$$$$$$  /$$$$$$$
// $$ /$$__  $$ |____  $$ /$$__  $$ /$$_____/
// $$| $$$$$$$$  /$$$$$$$| $$  | $$|  $$$$$$ 
// $$| $$_____/ /$$__  $$| $$  | $$ \____  $$
// $$|  $$$$$$$|  $$$$$$$|  $$$$$$$ /$$$$$$$/
//__/ \_______/ \_______/ \_______/|_______/                                        
 
 
import { Component, AfterViewInit, OnInit } from '@angular/core';
import { model } from '../../../services/model.service';
import { modal } from '../../../services/modal.service';
import { backend } from '../../../services/backend.service';
import { relatedmodels } from '../../../services/relatedmodels.service';
@Component({
    selector: 'validate-check-bulstat',
    template: '<div></div>',
    providers: [relatedmodels]
})
export class ValidateCheckBulstat implements AfterViewInit {
 
    public salesStage: string;
    private hasSubscribed: boolean = false;
    private validtBulstat: boolean = false;
    private lastBulstat: boolean = false;
    private msg = 'Въведеният булстат е грешен!';
 
 
    constructor(
        private model: model,
        private modal: modal,
        private relatedmodels: relatedmodels,
        private backend: backend
    ) {
    }
 
    ngAfterViewInit(): void {
        this.relatedmodels.relatedModule
        this.relatedmodels.getData();
        let self = this
        if (this.hasSubscribed == false) {
            this.lastBulstat  = this.model.getField('bulstat_c'); 
            this.model.data$.subscribe(data => {
 
                if ((this.model.isEditing === true || this.model.isNew) && this.validtBulstat == false && this.lastBulstat!=this.model.getField('bulstat_c')) {
                    doValidateCheckBulstat(self);
                }
            });
            this.hasSubscribed = true;
        }
    }
}
 
function doValidateCheckBulstat(self) {
    var bulstat_val = self.model.getField('bulstat_c');
    self.lastBulstat  = bulstat_val;
    if (bulstat_val != null && bulstat_val != '' && bulstat_val != ' ') {
         let bulstat = bulstat_val
        if (isNaN(bulstat)) {
            self.modal.info("Въведения Булстат не е число");
            return;
        }
        var leng = bulstat_val.length;
        switch (leng) {
            case 13:
                var digit11 = parseInt(bulstat_val.substr(10, 1));
                var digit12 = parseInt(bulstat_val.substr(11, 1));
                var digit13 = parseInt(bulstat_val.substr(12, 1));
            case 10:
                var digit10 = parseInt(bulstat_val.substr(9, 1));
            case 9:
                var digit1 = parseInt(bulstat_val.substr(0, 1));
                var digit2 = parseInt(bulstat_val.substr(1, 1));
                var digit3 = parseInt(bulstat_val.substr(2, 1));
                var digit4 = parseInt(bulstat_val.substr(3, 1));
                var digit5 = parseInt(bulstat_val.substr(4, 1));
                var digit6 = parseInt(bulstat_val.substr(5, 1));
                var digit7 = parseInt(bulstat_val.substr(6, 1));
                var digit8 = parseInt(bulstat_val.substr(7, 1));
                var digit9 = parseInt(bulstat_val.substr(8, 1));
        }
 
        switch (leng) {
            case 9:
                if (!getSum1(digit1, digit2, digit3, digit4, digit5, digit6, digit7, digit8, digit9)) {
                    self.modal.confirm(self.msg).subscribe(data => {
                        if (data === true) {
                            self.validtBulstat = true;
                            prefilledDataForBulstat(self);
                        }
                    });
                }
                else if (bulstat_val === '000000000') {
                    self.modal.confirm(self.msg).subscribe(data => {
                        if (data === true) {
                            self.validtBulstat = true;
                            prefilledDataForBulstat(self);
                        }
                    });
                }
                else {
                    prefilledDataForBulstat(self);
                }
                break;
            case 10:
                var egn = bulstat_val;
                if (!egn_valid(egn)) {
                    self.modal.confirm(self.msg).subscribe(data => {
                        if (data === true) {
                            self.validtBulstat = true;
                            prefilledDataForBulstat(self);
                        }
                    });
                }
                else if (bulstat_val === '0000000000') {
                    self.modal.confirm(self.msg).subscribe(data => {
                        if (data === true) {
                            self.validtBulstat = true;
                            prefilledDataForBulstat(self);
                        }
                    });
                }
                else {
                    prefilledDataForBulstat(self);
                }
                break;
            case 13:
                if (!getSum2(bulstat_val, digit1, digit2, digit3, digit4, digit5, digit6, digit7, digit8, digit9, digit13)) {
                    self.modal.confirm(self.msg).subscribe(data => {
                        if (data === true) {
                            self.validtBulstat = true;
                            prefilledDataForBulstat(self);
                        }
                    });
                }
                else if (bulstat_val === '0000000000000') {
                    self.modal.confirm(self.msg).subscribe(data => {
                        if (data === true) {
                            self.validtBulstat = true;
                            prefilledDataForBulstat(self);
                        }
                    });
                }
                else {
                    prefilledDataForBulstat(self);
                }
                break;
            default:
                self.modal.confirm(self.msg).subscribe(data => {
                    if (data === true) {
                        self.validtBulstat = true;
                        prefilledDataForBulstat(self);
                    }
                });
        }
    }
 
}
 
function checkdate(m, d, y) {
    return m > 0 && m < 13 && y > 0 && y < 32768 && d > 0 && d <= (new Date(y, m, 0))
        .getDate();
}
 
function egn_valid(egn) {
    var EGN_WEIGHTS = new Array(2, 4, 8, 5, 10, 9, 7, 3, 6);
    var year = parseInt(egn.substr(0, 2));
    var mon = parseInt(egn.substr(2, 2));
    var day = parseInt(egn.substr(4, 2));
    if (mon > 40) {
        if (!checkdate(mon - 40, day, year + 2000))
            return false;
    } else
        if (mon > 20) {
            if (!checkdate(mon - 20, day, year + 1800))
                return false;
        } else {
            if (!checkdate(mon, day, year + 1900))
                return false;
        }
    var checksum = parseInt(egn.substr(9, 1));
    var egnsum = 0;
    for (let t = 0; t < 9; t++) {
        egnsum += parseInt(egn.substr(t, 1)) * EGN_WEIGHTS[t];
        var valid_checksum = egnsum % 11;
    }
    if (valid_checksum == 10)
        valid_checksum = 0;
    if (checksum == valid_checksum)
        return true;
}
 
function getSum1(digit1, digit2, digit3, digit4, digit5, digit6, digit7, digit8, digit9) {
    var mutiplied1 = digit1 * 1;
    var mutiplied2 = digit2 * 2;
    var mutiplied3 = digit3 * 3;
    var mutiplied4 = digit4 * 4;
    var mutiplied5 = digit5 * 5;
    var mutiplied6 = digit6 * 6;
    var mutiplied7 = digit7 * 7;
    var mutiplied8 = digit8 * 8;
    var sum = mutiplied1 + mutiplied2 + mutiplied3 + mutiplied4 + mutiplied5 + mutiplied6 + mutiplied7 + mutiplied8;
    sum = (sum % 11);
    if ((sum != 10) && (sum != digit9)) {
        return false;
    } else if (sum === 10) {
        mutiplied1 = digit1 * 3;
        mutiplied2 = digit2 * 4;
        mutiplied3 = digit3 * 5;
        mutiplied4 = digit4 * 6;
        mutiplied5 = digit5 * 7;
        mutiplied6 = digit6 * 8;
        mutiplied7 = digit7 * 9;
        mutiplied8 = digit8 * 10;
        sum = mutiplied1 + mutiplied2 + mutiplied3 + mutiplied4 + mutiplied5 + mutiplied6 + mutiplied7 + mutiplied8;
        sum = (sum % 11);
        if ((sum != 10) && (sum != digit9) && (digit9 != 0)) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}
 
function getSum2(bulstat_val, digit1, digit2, digit3, digit4, digit5, digit6, digit7, digit8, digit9, digit13) {
    if (getSum1(digit1, digit2, digit3, digit4, digit5, digit6, digit7, digit8, digit9)) {
        var multiplier = new Array(2, 7, 3, 5);
        var sum = 0;
        var k = 0;
        for (k = 9; k < 13; k++)
            sum += parseInt(bulstat_val.substr(k-1, 1)) * multiplier[k - 9];
 
        sum = (sum % 11);
 
        if ((sum != 10) && (sum != digit13)) {
            return false;
 
        } else if (sum === 10) {
            var multiplier = new Array(4, 9, 5, 7);
            var sum = 0;
            for (k = 0; k < 4; k++) {
                sum += parseInt(bulstat_val.substr(k + 8, 1)) * multiplier[k];
            }
 
            sum = (sum % 11);
            if ((sum != 10) && (sum != digit13) && (digit13 != 0)) {
                return false;
            } 
            else {
                return true;
            }
        }
        else {
            return true;
        }
    } else {
        return false;
    }
}
 
function prefilledDataForBulstat(self) {
    let bulstat = self.model.getField('bulstat_c');
    self.lastBulstat  = bulstat;
    console.log(self.lastBulstat);
    if (bulstat !== undefined) {
    self.backend.getRequest(`/prefilleddatabulstat/${bulstat}/`).subscribe(data => {
 
        if (data.length>0) {
            let name = data[0].accountName;
            let nameId = data[0].accountId;
            let cityId = data[0].cityId
            let cityName = data[0].cityName
            console.log(name, cityId, cityName);
            if (self.model.getField("account_name") !== name) {
                self.model.setField("account_name", name);
                self.model.setField("account_id", nameId);
            }
            if (self.model.getField("bzlnk_cities_leads_1_name") !== cityName) {
                self.model.setField("bzlnk_cities_leads_1bzlnk_cities_ida", cityId);
                self.model.setField("bzlnk_cities_leads_1_name", cityName);
            }
        }
        else {
            if (self.model.getField("account_name")!=='') {
                self.model.setField("account_name", '');
            }
            if (self.model.getField("bzlnk_cities_leads_1_name") !== '') {
                self.model.setField("bzlnk_cities_leads_1_name", '');
            }
            if (self.model.getField("bzlnk_cities_leads_1bzlnk_cities_ida")!=="") {
                self.model.setField("bzlnk_cities_leads_1bzlnk_cities_ida", '');
            }
        }
    })
}
}