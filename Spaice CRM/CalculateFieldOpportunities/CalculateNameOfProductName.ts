/**
 * @copyright: Bizlink
 * @jira: T-134
 * @author: Ventsislav Verchov vverchov@bizlink-solutions.eu
 */
 
 import { Component, AfterViewInit, OnInit } from '@angular/core';
 import { model } from '../../../services/model.service';
 import { relatedmodels } from '../../../services/relatedmodels.service';
 @Component({
     selector: 'custom-calculate-fields-opportunities',
     template: '<div></div>',
     providers: [relatedmodels]
 })
 export class CalculateNameOfProductName implements AfterViewInit {
 
     private hasSubscribed: boolean = false;
     private hasInitalizedFields: boolean = false;
 
     public salesStage: string;
 
     constructor(
         private model: model,
         private relatedmodels: relatedmodels
     ) {
     }
 
     ngAfterViewInit(): void {
         this.relatedmodels.relatedModule
         this.relatedmodels.getData();
         if (this.hasSubscribed == false) {
             this.model.data$.subscribe(data => {
                 console.log(data);
                 if (this.model.isEditing === true) {
                     this.doCalculate();
                 }
             });
             this.hasSubscribed = true;
         }
     }
 
     private initializeFields() {
 
        if (this.model.getField("nominal_value") === undefined) {
            this.model.initializeField("nominal_value", 0);
        }
        if (this.model.getField("price_c") === undefined) {
            this.model.initializeField("price_c", 0);
        }
    }
 
     private doCalculate() {
        if (this.hasInitalizedFields == false) {
            this.initializeFields();
        }
        let offerName = this.model.getField('bzlnk_offers_products_name');
 
        if (this.model.isNew && offerName!==" " && offerName!=undefined) {
            let name = this.model.getField('name');
            if (name !== offerName) {
                this.model.setField("name", offerName);
            }
        }
 
        let nominalValue = this.model.getField('nominal_value');
        nominalValue = !isNaN(nominalValue) ? nominalValue : 0.00;
        let price = parseFloat(this.model.getField('price_c'));
        price = !isNaN(price) ? price : 0;
        let serviceAmountMonth = parseFloat(nominalValue)*price;
        serviceAmountMonth = serviceAmountMonth/100;
        serviceAmountMonth = serviceAmountMonth+parseFloat(nominalValue);
 
        if (this.model.getField("service_amount_month_c") !== serviceAmountMonth) {
            this.model.setField("service_amount_month_c", serviceAmountMonth);
        }
    }
}
 
 
