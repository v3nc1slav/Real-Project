/**
 * @copyright: 
 * @jira: T-198
 * @jira: T-199
 * @author: Ventsislav Verchov
 */
 
 import { Component, AfterViewInit, OnInit } from '@angular/core';
 import { model } from '../../../services/model.service';
 import { backend } from '../../../services/backend.service';
 import { relatedmodels } from '../../../services/relatedmodels.service';
 @Component({
     selector: 'calculate-fields-accounts',
     template: '<div></div>',
     providers: [relatedmodels]
 })
 export class CalculateAccountsFilds implements AfterViewInit {
  
     private hasSubscribed: boolean = false;
     private lastCityId: string = '';
     private lastNkid: string = '';
  
     constructor(
         private backend: backend,
         private model: model,
         private relatedmodels: relatedmodels
  
     ) {
     }
     ngAfterViewInit(): void {
         this.relatedmodels.relatedModule
         this.relatedmodels.getData();
         if (this.hasSubscribed == false) {
             this.lastCityId = this.model.getField('bzlnk_cities_id_c');
             this.lastNkid = this.model.getField('nkid_8_c');
             this.model.data$.subscribe(data => {
                 if (this.model.isEditing === true && this.lastCityId!=this.model.getField('bzlnk_cities_id_c')) {
  
                     let cityId = this.model.getField('bzlnk_cities_id_c');
                     this.lastCityId = cityId;
  
                     if (cityId == "") {
                         cityId = 11;
                     }
                     this.backend.getRequest(`/getdatafromaccounts/${cityId}`).subscribe(result => {
  
                         if (result.length > 0) {
                             let municipalityName = result[0]['municipality'];
                             let municipalityId = result[0]['municipalityId'];
                             if (this.model.getField('address_municipality_c') != municipalityName) {
                                 this.model.setField('address_municipality_c', municipalityName);
                                 this.model.setField('bzlnk_municipality_id_c', municipalityId);
  
                             }
                             let zipCode = result[0]['zipCode'];
                             if (this.model.getField('billing_address_postalcode') != zipCode) {
                                 this.model.setField('billing_address_postalcode', zipCode)
                             }
                             let state = result[0]['state'];
                             let stateId = result[0]['stateId'];
                             if (this.model.getField('address_area_c') != state) {
                                 this.model.setField('address_area_c', state)
                                 this.model.setField('bzlnk_state_id_c', stateId)
  
                             }
                             let region = result[0]['region'];
                             let regionId = result[0]['regionId'];
                             if (this.model.getField('address_region_c') != region) {
                                 this.model.setField('address_region_c', region);
                                 this.model.setField('bzlnk_regions_id_c', regionId);
                             }
                         }
                         else {
                             if (this.model.getField('address_municipality_c') != "") {
                                 this.model.setField('address_municipality_c', "");
                                 this.model.setField('bzlnk_municipality_id_c', "");
  
                             }
                             if (this.model.getField('billing_address_postalcode') != "") {
                                 this.model.setField('billing_address_postalcode', "")
                             }
                             if (this.model.getField('address_area_c') != "") {
                                 this.model.setField('address_area_c', "")
                                 this.model.setField('bzlnk_state_id_c', "")
  
                             }
                             if (this.model.getField('address_region_c') != "") {
                                 this.model.setField('address_region_c', "");
                                 this.model.setField('bzlnk_regions_id_c', "");
                             }
                         }
                     })
                 }
                 if (this.model.isEditing === true && this.lastNkid != this.model.getField('nkid_8_c')) {
                     let nkid = this.model.getField('nkid_8_c');
                     this.lastNkid = nkid;
  
                     if (nkid == "") {
                         nkid = 11;
                     }
                     this.backend.postRequest(`/getdatafromnikd2008/${nkid}`).subscribe(res => {
                         let result = res
  
                         if (result.length > 0) {
                             let groups = result[0]['groups'];
                             let groupsId = result[0]['groupsId'];
                             if (this.model.getField('groups_nikd2008_c') != groups) {
                                 this.model.setField('groups_nikd2008_c', groups);
                                 this.model.setField('tom03_group_nkid2008_id_c', groupsId);
                             }
                             let classifications = result[0]['classifications'];
                             let classificationsId = result[0]['classificationsId'];
                             if (this.model.getField('classifications_nikd2008_c') != classifications) {
                                 this.model.setField('classifications_nikd2008_c', classifications)
                                 this.model.setField('tom02_classifications_id_c', classificationsId)
                             }
                             let sectors = result[0]['sectors'];
                             let sectorsId = result[0]['sectorsId'];
                             if (this.model.getField('sectors_nkid2008_c') != sectors) {
                                 this.model.setField('sectors_nkid2008_c', sectors)
                                 this.model.setField('tom01_sectors_id_c', sectorsId)
                             }
                         }
                         else {
                             if (this.model.getField('groups_nikd2008_c') != "") {
                                 this.model.setField('groups_nikd2008_c', "");
                                 this.model.setField('tom03_group_nkid2008_id_c', "");
                             }
                             if (this.model.getField('classifications_nikd2008_c') != "") {
                                 this.model.setField('classifications_nikd2008_c', "")
                                 this.model.setField('tom02_classifications_id_c', "")
                             }
                             if (this.model.getField('sectors_nkid2008_c') != "") {
                                 this.model.setField('sectors_nkid2008_c', "")
                                 this.model.setField('tom01_sectors_id_c', "")
                             }
                         }
                     })
  
                 }
             });
             this.hasSubscribed = true;
         }
     }
 }