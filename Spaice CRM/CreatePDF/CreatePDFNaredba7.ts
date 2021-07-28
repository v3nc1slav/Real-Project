/**
 * @copyright: 
 * @jira: T-133
 * @author: Ventsislav Verchov 
 */
/**
 * @module ModuleBizlinkCustom
 */

import { Component } from '@angular/core';
import {Observable, Subject, of, BehaviorSubject} from 'rxjs';
import { modal } from '../../../services/modal.service';
import { model } from '../../../services/model.service';
import { backend } from '../../../services/backend.service';

@Component({
    selector: 'cstm-change-account-assigned-user-action',
    templateUrl: './src/custom/bizlinkcustom/templates/createpdfopportunity.html'
})
export class CreatePDFOpportunityAction {
 
    public actionconfig: any;
    public selected: any;
    public shouldShow : boolean = false;
    constructor(
        private backend: backend,
        private model: model,
        private modal:modal
    ) {
    }
 
    ngAfterViewInit(){
        if(this.model.getField("product_categories_c") == 1){
            this.shouldShow = true;
        }
        this.model.data$.subscribe(((a)=>{
            if(this.model.getField("product_categories_c") == 1){
                this.shouldShow = true;
            }else{
                this.shouldShow = false;   
            }
        }).bind(this))
    }
    
    public execute() {
        if(this.shouldShow == false){
            return;
        }
        let pdfName = `${this.model.getField("name")}.pdf`
        let stopper = this.modal.await("Моля, изчакайте!")
        this.backend.downloadFile(
            {
                route:  '/BZLNK_Offers/createPDF/Naredba7',
                method: "POST",
                body:{
                    id:this.model.getField("id")
                }
            },pdfName,"application/pdf").subscribe((e)=>{
                stopper.emit(true);
            });
 
     }
}