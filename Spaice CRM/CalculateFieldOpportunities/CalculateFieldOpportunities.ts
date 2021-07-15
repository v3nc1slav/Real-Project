/**
 * @copyright: 
 * @jira: TE-134
 * @author: Ventsislav Verchov 
 */
 
 import { Component, AfterViewInit, OnInit } from '@angular/core';
 import { model } from '../../../services/model.service';
 import { modal } from '../../../services/modal.service';
 import { backend } from '../../../services/backend.service';
 import { relatedmodels } from '../../../services/relatedmodels.service';
 @Component({
     selector: 'custom-calculate-fields-opportunities',
     template: '<div></div>',
     providers: [relatedmodels]
 })
 export class CalculateFieldOpportunities implements AfterViewInit {
     public dataOpperOffert: any = '';
     public dataOpperSpiceattachments: any = '';
     public dataOpperContracts: any = '';
     private hasSubscribed: boolean = false;
     private hasInitalizedFields: boolean = false;
     private messigOffer: string = "Моля изберете друг етап, тъй като нямате създадена оферта!";
     private messigDraftContract = "Моля изберете друг етап, тъй като нямате създаден договор!";
  
     public salesStage: string;
  
     constructor(
         private backend: backend,
         private model: model,
         private modal: modal,
         private relatedmodels: relatedmodels
     ) {
     }
  
     ngAfterViewInit(): void {
         this.relatedmodels.relatedModule
         this.relatedmodels.getData();
         if (this.hasSubscribed == false) {
             this.model.data$.subscribe(data => {
                 if (this.model.isEditing === true || this.model.isNew === true ) {
                    this.doCalculate();
                 }
             });
             this.hasSubscribed = true;
         }
     }
  
     private initializeFields() {

         if (this.model.getField("name") === undefined) {
             this.model.initializeField("name", " ");
         }
         if (this.model.getField("periods_c") === undefined) {
             this.model.initializeField("periods_c", 0);
         }
         if (this.model.getField("quantity_c") === undefined) {
             this.model.initializeField("quantity_c", 0);
         }
         if (this.model.getField("period_number_employees_c") === undefined) {
             this.model.initializeField("period_number_employees_c", 0);
         }
         if (this.model.getField("amount_employee_c") === undefined) {
             this.model.initializeField("amount_employee_c", 0);
         }
         if (this.model.getField("total_amount_c") === undefined) {
             this.model.initializeField("total_amount_c", 0);
         }
         if (this.model.getField("period_total_amount_c") === undefined) {
             this.model.initializeField("period_total_amount_c", 0);
         }
         if (this.model.getField("gasoline_c") === undefined) {
             this.model.initializeField("gasoline_c", 0);
         }
         if (this.model.getField("gasoline_amount_c") === undefined) {
             this.model.initializeField("gasoline_amount_c", 0);
         }
         if (this.model.getField("diesel_c") === undefined) {
             this.model.initializeField("diesel_c", 0);
         }
         if (this.model.getField("diesel_amount_c") === undefined) {
             this.model.initializeField("diesel_amount_c", 0);
         }
         if (this.model.getField("lpg_amount_c") === undefined) {
             this.model.initializeField("lpg_amount_c", 0);
         }
         if (this.model.getField("total_liter_month_c") === undefined) {
             this.model.initializeField("total_liter_month_c", 0);
         }
         if (this.model.getField("total_amount_month_c") === undefined) {
             this.model.initializeField("total_amount_month_c", 0);
         }
         if (this.model.getField("total_liter_year_c") === undefined) {
             this.model.initializeField("total_liter_year_c", 0);
         }
         if (this.model.getField("total_amount_year_c") === undefined) {
             this.model.initializeField("total_amount_year_c", 0);
         }
         if (this.model.getField("lightweight_cars_c") === undefined) {
             this.model.initializeField("lightweight_cars_c", 0);
         }
         if (this.model.getField("totalaverageweight_cars_c_vehicle_c") === undefined) {
             this.model.initializeField("averageweight_cars_c", 0);
         }
         if (this.model.getField("heavyweight_cars_c") === undefined) {
             this.model.initializeField("heavyweight_cars_c", 0);
         }
         if (this.model.getField("total_vehicle_c") === undefined) {
             this.model.initializeField("total_vehicle_c", 0);
         }
  
         this.hasInitalizedFields = true;
     }
  
     private doCalculate() {
         if (this.hasInitalizedFields == false) {
             this.initializeFields();
         }
  
         let productsName = this.model.getField('opportunities_products_name');
  
         if (this.model.isNew && productsName !== " " && productsName!=undefined) {
             let name = this.model.getField('name');
             if (name !== productsName) {
                 this.model.setField("name", productsName);
             }
         }
  
         let periods = this.model.getField('periods_c');
         periods = isNaN(periods) ? 1.00 : periods;
         periods = !isNaN(parseInt(periods)) ? periods : 0.00;
  
         let quantity = this.model.getField('quantity_c');
         quantity = !isNaN(quantity) ? quantity : 0.00;
         let numberEmployees = periods * quantity;
  
         if (this.model.getField("period_number_employees_c") !== numberEmployees) {
             this.model.setField("period_number_employees_c", numberEmployees);
         }
  
         let amount = this.model.getField('amount_employee_c');
  
         amount = !isNaN(parseFloat(amount)) ? parseFloat(amount) : 0.00;
         let totalAmount = amount * quantity;
  
         if (this.model.getField("total_amount_c") !== totalAmount) {
             this.model.setField("total_amount_c", totalAmount);
         }
  
         let periodTotalAmount = periods * totalAmount;
  
         if (this.model.getField("period_total_amount_c") !== periodTotalAmount) {
             this.model.setField("period_total_amount_c", periodTotalAmount);
         }
  
         let gasoline = this.model.getField('gasoline_c');
         gasoline = !isNaN(parseFloat(gasoline)) ? parseFloat(gasoline) : 0.00;
         let gasolineAmount = 2.30 * gasoline;
  
         if (this.model.getField("gasoline_amount_c") !== gasolineAmount) {
             this.model.setField("gasoline_amount_c", gasolineAmount);
         }
  
         let diesel = this.model.getField('diesel_c');
         diesel = !isNaN(parseFloat(diesel)) ? parseFloat(diesel) : 0.00;
         let dieselAmount = 2.30 * diesel;
  
         if (this.model.getField("diesel_amount_c") !== dieselAmount) {
             this.model.setField("diesel_amount_c", dieselAmount);
         }
  
         let lpg = this.model.getField('lpg_c');
         lpg = !isNaN(parseFloat(lpg)) ? parseFloat(lpg) : 0.00;
         let lpgAmount = 1.10 * lpg;
  
         if (this.model.getField("lpg_amount_c") !== lpgAmount) {
             this.model.setField("lpg_amount_c", lpgAmount);
         }
  
         let totalLiterMonth = gasoline + diesel + lpg;
  
         if (this.model.getField("total_liter_month_c") !== totalLiterMonth) {
             this.model.setField("total_liter_month_c", totalLiterMonth);
         }
  
         let totalAmountMonth = gasolineAmount + dieselAmount + lpgAmount;
  
         if (this.model.getField("total_amount_month_c") !== totalAmountMonth) {
             this.model.setField("total_amount_month_c", totalAmountMonth);
         }
  
         let totalLiterYear = totalLiterMonth * 12;
  
         if (this.model.getField("total_liter_year_c") !== totalLiterYear) {
             this.model.setField("total_liter_year_c", totalLiterYear);
         }
  
         let totalAmountYear = totalAmountMonth * 12;
  
         if (this.model.getField("total_amount_year_c") !== totalAmountYear) {
             this.model.setField("total_amount_year_c", totalAmountYear);
         }
  
         let lightweightCars = this.model.getField("lightweight_cars_c");
         diesel = !isNaN(lightweightCars) ? lightweightCars : 0;
         let averageweightCars = this.model.getField("averageweight_cars_c");
         diesel = !isNaN(averageweightCars) ? averageweightCars : 0;
         let heavyweightCars = this.model.getField("heavyweight_cars_c");
         diesel = !isNaN(heavyweightCars) ? heavyweightCars : 0;
  
         let totalVehicle = lightweightCars + averageweightCars + heavyweightCars;
  
         if (this.model.getField("total_vehicle_c") !== totalVehicle) {
             this.model.setField("total_vehicle_c", totalVehicle);
         }
  
         this.salesStage = this.model.getField("sales_stage");
  
         if (this.salesStage === "Offer") {
             this.backend.getRequest(`/oppertunitiesgetrelatedoffers/${this.model.id}`).subscribe(result => {
             this.dataOpperOffert = result;
  
                 this.backend.getRequest(`/oppertunitiesgetspiceattachments/${this.model.id}`).subscribe(result1 => {
                 this.dataOpperSpiceattachments = result1;
  
                     if ((this.dataOpperOffert == 0) && (this.dataOpperSpiceattachments == 0)) {
                         this.modal.info(this.messigOffer);
                         this.model.setField("sales_stage", "");
                     }
                 });
             });
         }
  
         if (this.salesStage === "Draft Contract") {
             this.backend.getRequest(`/oppertunitiesgetrelatedoffers/${this.model.id}`).subscribe(result => {
                 this.dataOpperOffert = result;
                 this.backend.getRequest(`/oppertunitiesgetspiceattachments/${this.model.id}`).subscribe(result1 => {
                     this.dataOpperSpiceattachments = result1;
                     this.backend.getRequest(`/oppertunitiesgetcontracts/${this.model.id}`).subscribe(result2 => {
                         this.dataOpperContracts = result2;
  
                         if (this.dataOpperOffert == 0 && this.dataOpperSpiceattachments == 0 && this.dataOpperContracts == 0) {
                             this.modal.info(this.messigDraftContract);
                             this.model.setField("sales_stage", "");
                         }
                         else if (this.dataOpperContracts == 0) {
                             this.modal.info(this.messigDraftContract);
                             this.model.setField("sales_stage", "");
                         }
                     });
                 });
             });
         }
     }
 }